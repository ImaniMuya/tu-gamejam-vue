<?php
include_once("./constants.inc");
header("Access-Control-Allow-Origin: $clientOrigin");
header("Access-Control-Allow-Methods: OPTIONS, POST");
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
    //TODO: this is potentially wrong. Could be seeing team member.
    die("This email has already registered a team");
  }
}

// Insertion

$secret = mt_rand(); //TODO: ensure random is unique
$sql = $conn->prepare("INSERT INTO teams (name, the_secret) VALUES (:name, :the_secret)");
$sql->bindValue(':name', $body->teamName);
$sql->bindValue(':the_secret', $secret);
if (!$sql->execute()) {
  http_response_code(500);
  die("Error Creating Team. Please try again :(");
}
$teamId = $conn->lastInsertId();


$sql = $conn->prepare("INSERT INTO people (person_name, email, team_id) VALUES (:person_name, :email, :team_id)");
$sql->bindValue(':person_name', $body->leaderName);
$sql->bindValue(':email', $body->email);
$sql->bindValue(':team_id', $teamId);
if (!$sql->execute()) {
  http_response_code(500);
  die("Error Creating Leader. Please try again :(");
  // TODO: delete team here?
}

send_team_email($body->email, $body->teamName, $teamId, $secret);

http_response_code(201);
die("Team Created: $body->teamName.");
?>
