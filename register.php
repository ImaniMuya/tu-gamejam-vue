<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  die();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(422);
  die("Please send a POST!");
}


//expect json body...
$content = file_get_contents('php://input');
$body = json_decode($content);


include_once('./helper.inc');
$conn = get_db_connection();

header('Content-Type: application/json');

// Validation

if ($body->regCode != "nerds4christ") {
  http_response_code(400);
  die("Wrong registration code.");
}

if (!$body->teamName) {
  http_response_code(400);
  die("Missing Team Name.");
}
if (!$body->leaderName) {
  http_response_code(400);
  die("Missing Leader Name.");
}
if (!$body->email) {
  http_response_code(400);
  die("Missing email.");
}

if (empty(filter_var($body->email, FILTER_VALIDATE_EMAIL))) {
  http_response_code(400);
  die("Invalid email.");
}

$sql = "SELECT email FROM people";
$stmt = $conn->prepare($sql);
$stmt->execute();
$people = $stmt->fetchAll();
foreach ($people as $key => $person) {
  if (strtoupper($person["email"]) == strtoupper($body->email)) {
    http_response_code(400);
    die("This email has already registered a team");
  }
}

// Insertion

$secret = mt_rand(); //TODO: ensure random is unique
$sql = $conn->prepare("INSERT INTO teams (name, the_secret) VALUES (:name, :the_secret)");
$sql->bindParam(':name', $body->teamName);
$sql->bindParam(':the_secret', $secret);
if (!$sql->execute()) {
  http_response_code(500);
  die("Error Creating Team. Please try again :(");
}
$team_id = $conn->lastInsertId();


$sql = $conn->prepare("INSERT INTO people (person_name, email, team_id) VALUES (:person_name, :email, :team_id)");
$sql->bindParam(':person_name', $body->leaderName);
$sql->bindParam(':email', $body->email);
$sql->bindParam(':team_id', $team_id);
if (!$sql->execute()) {
  http_response_code(500);
  die("Error Creating Leader. Please try again :(");
  // TODO: delete team here?
}

// Email

$loginURL = "http/localhost:8080/login";
$msg = "You just created team: ".$body->teamName."! Click this link to sign in!".$loginURL."?t=".$team_id."&s=".$secret;
$msg = wordwrap($msg,70);
mail($body->email,"TU GameJam Team Confirmation",$msg);


http_response_code(201);
die("Team Created: $body->teamName.");
?>
