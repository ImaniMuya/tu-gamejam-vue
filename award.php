<?php
include_once("./constants.inc");
header("Access-Control-Allow-Origin: $clientOrigin");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  die();
}

include_once("./helper.inc");
$conn = get_db_connection();

if (!isAdmin($conn)) {
  http_response_code(403);
  die("Only Admin can use awards endpoint.");
}

// GET
if ($_SERVER['REQUEST_METHOD'] == "GET") {
  $awards = getAwards($conn);

  http_response_code(200);
  die_JSON($awards);
}

//POST
if ($_SERVER['REQUEST_METHOD'] == "POST") {
  $content = file_get_contents('php://input');
  $sql = $conn->prepare("INSERT INTO awards (award) VALUES (:award)");
  $sql->bindParam(':award', $content);
  if (!$sql->execute()) {
    http_response_code(500);
    die("Failed while adding award.");
  }
  http_response_code(201);

  die_JSON(array(
    "award_id" => $conn->lastInsertId(),
    "award" => $content,
  ));
}

// PUT
if ($_SERVER['REQUEST_METHOD'] == "PUT") {
  $content = file_get_contents('php://input');
  $body = json_decode($content);

  if ($_GET["id"] == "") {
    http_response_code(400);
    die("No award provided to update.");
  }
  
  $sql = $conn->prepare("UPDATE awards 
    SET award = :award, team_id = :winner WHERE award_id = :award_id");
  $sql->bindParam(':award', $body->name);
  $sql->bindParam(':winner', $body->winner);
  $sql->bindParam(':award_id', $_GET["id"]);

  if (!$sql->execute()) {
    http_response_code(500);
    die("Failed while updating award.");
  }
  
  http_response_code(200);
  die_JSON(array(
    "award" => $body->name,
    "team_id" => $body->winner,
    "award_id" => $_GET["id"]
  ));
}

?>
