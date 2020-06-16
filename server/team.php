<?php
header('Access-Control-Allow-Origin: http://localhost:8080'); //TODO: make constants file
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  die();
}

$teamId = $_COOKIE['gjt'];
$secret = $_COOKIE['gjs'];
if (!$teamId || !$secret) {
  http_response_code(403);
  die("Please log in first.");
}

include_once("./helper.inc");
$conn = get_db_connection();
$team = get_login_team($conn, $teamId, $secret);

if (!$team) {
  http_response_code(403);
  die("Login invalid.");
}

// GET
if ($_SERVER['REQUEST_METHOD'] == "GET") {
  $sql = $conn->prepare("SELECT * FROM people WHERE team_id = :teamId");
  $sql->bindParam(':teamId', $team["team_id"]);
  if (!$sql->execute()) {
    http_response_code(500);
    die("Failed while finding people.");
  }
  
  http_response_code(200);
  die_JSON($sql->fetchAll());
}

// POST
if ($_SERVER['REQUEST_METHOD'] == "POST") {
  $content = file_get_contents('php://input');
  $body = json_decode($content);
  
  $sql = $conn->prepare("INSERT INTO people (person_name, email, team_id)
  VALUES (:person_name, :email, :team_id)");
  $sql->bindParam(':person_name', $body->name);
  $sql->bindParam(':email', $body->email);
  $sql->bindParam(':team_id', $team["team_id"]);
  if (!$sql->execute()) {
    http_response_code(500);
    die("Failed while adding member.");
  }
  http_response_code(201);
  
  // send_team_email($body->email, $team["name"], $team["team_id"], $secret);
  
  die_JSON(array(
    "person_id" => $conn->lastInsertId(),
    "person_name" => $body->name,
    "email" => $body->email
  ));
}

// PUT (this should technically be "PATCH")
if ($_SERVER['REQUEST_METHOD'] == "PUT") {
  $content = file_get_contents('php://input');
  $body = json_decode($content);
  if ($body->name == "" && $body->email == "") {
    http_response_code(400);
    die("No member info to update.");
  }
  
  $sql = "UPDATE people SET ";
  if ($body->name != "") {
    $sql .= "person_name = :person_name ";
    if ($body->email != "") $sql .= ", ";
  }
  if ($body->email != "") $sql .= "email = :email ";
  $sql .= "WHERE person_id=:person_id AND team_id=:team_id";

  $sql = $conn->prepare($sql);
  $sql->bindParam(':person_id', $body->id);
  if ($body->name != "") $sql->bindParam(':person_name', $body->name);
  if ($body->email != "") $sql->bindParam(':email', $body->email);
  $sql->bindParam(':team_id', $team["team_id"]);

  if (!$sql->execute()) {
    http_response_code(500);
    die("Failed while updating member.");
  }
  http_response_code(200);
  if ($body->email != "") {
    // send_team_email($body->email, $team["name"], $team["team_id"], $secret);
  }
  die_JSON(array(
    "person_name" => $body->name,
    "email" => $body->email
  ));
}

// DELETE
if ($_SERVER['REQUEST_METHOD'] == "DELETE") {
  $sql = $conn->prepare("DELETE FROM people WHERE person_id = :person_id AND team_id = :team_id");
  $sql->bindParam(':person_id', $_GET["id"]);
  $sql->bindParam(':team_id', $team["team_id"]);
  if (!$sql->execute()) {
    http_response_code(500);
    die("Failed while deleting member.");
  }

  http_response_code(200);
  die("Deleted.");
}

?>
