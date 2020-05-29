<?php

function close() {
  flush();
  exit();
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

if ($body->regCode != "nerds4christ") {
  http_response_code(400);
  echo "Wrong registration code.";
  close();
}

//TODO: other validation

$secret = mt_rand();
$sql = $conn->prepare("INSERT INTO teams (name, the_secret) VALUES (:name, :the_secret)");
$sql->bindParam(':name', $body->teamName);
$sql->bindParam(':the_secret', $secret);
$sql->execute();
$team_id = $conn->lastInsertId();


$sql = $conn->prepare("INSERT INTO people (person_name, email, team_id) VALUES (:person_name, :email, :team_id)");
$sql->bindParam(':person_name', $body->leaderName);
$sql->bindParam(':email', $body->email);
$sql->bindParam(':team_id', $team_id);
$sql->execute();


echo "Created.";
http_response_code(201);

?>