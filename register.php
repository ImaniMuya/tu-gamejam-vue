<?php

function close() {
  flush();
  die();
}

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  close();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(422);
  echo "Please send a POST!";
  close();
}


//expect json body...
$content = file_get_contents('php://input');
$body = json_decode($content);


include('db.php');
header('Content-Type: application/json');

// Validation

if ($body->regCode != "nerds4christ") {
  http_response_code(400);
  echo "Wrong registration code.";
  close();
}

if (!$body->teamName) {
  http_response_code(400);
  echo "Missing Team Name.";
  close();
}
if (!$body->leaderName) {
  http_response_code(400);
  echo "Missing Leader Name.";
  close();
}
if (!$body->email) {
  http_response_code(400);
  echo "Missing email.";
  close();
}

if (empty(filter_var($body->email, FILTER_VALIDATE_EMAIL))) {
  http_response_code(400);  
  echo "Invalid email.";
  close();
}

$sql = "SELECT email FROM people";
$stmt = $conn->prepare($sql);
$stmt->execute();
$people = $stmt->fetchAll();
foreach ($people as $key => $person) {
  if (strtoupper($person["email"]) == strtoupper($body->email)) {
    http_response_code(400);  
    echo "This email has already registered a team.";
    close();
  }
}

// Insertion

$secret = mt_rand(); //TODO: ensure random is unique
$sql = $conn->prepare("INSERT INTO teams (name, the_secret) VALUES (:name, :the_secret)");
$sql->bindParam(':name', $body->teamName);
$sql->bindParam(':the_secret', $secret);
if (!$sql->execute()) {
  http_response_code(500);
  echo "Error Creating Team. Please try again :(";
  close();
}
$team_id = $conn->lastInsertId();


$sql = $conn->prepare("INSERT INTO people (person_name, email, team_id) VALUES (:person_name, :email, :team_id)");
$sql->bindParam(':person_name', $body->leaderName);
$sql->bindParam(':email', $body->email);
$sql->bindParam(':team_id', $team_id);
if (!$sql->execute()) {
  http_response_code(500);
  echo "Error Creating Leader. Please try again :(";
  close();
  // TODO: if failed delete team?
}

$msg = "You just created team: ".$teamName."! Click this link to sign in!".$loginURL."?team=".$team_id."&secret=".$secret;
$msg = wordwrap($msg,70);
mail($body->email,"TU GameJam Team Confirmation",$msg);

echo "Team Created: $body->teamName.";
http_response_code(201);

?>