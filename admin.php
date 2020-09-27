<?php
header('Access-Control-Allow-Origin: http://localhost:8080'); //TODO: make constants file
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  die();
}

include_once("helper.inc");

$conn = get_db_connection();

$pepper = "gjivCjruT7S1HBkf6WFhkQPZfZcTk00Y";

//create session
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $content = file_get_contents('php://input');
  $sql = $conn->prepare("SELECT name, value FROM admin_properties
    WHERE name = 'password_hash' OR name = 'one_time_pass'");
  $sql->execute();
  $dict = $sql->fetchAll(PDO::FETCH_KEY_PAIR);

  if ($dict['one_time_pass']) {
    $dbOneTime = hash("sha256", $dict['one_time_pass']); //mimic client side hash
    $passwordDoesMatch = $content == $dbOneTime;
    if (!$passwordDoesMatch) {
      http_response_code(403);
      die("One time password does not match.");
    }
  
    $sql = $conn->prepare("UPDATE admin_properties 
    SET value = '' WHERE name = 'one_time_pass'");
    $sql->execute();
  } else {
    // TODO: hash content after receiving
    $passwordHash = hash("sha256", $content + $pepper);
    $dbPasswordHash = $dict['password_hash'];
    $passwordDoesMatch = $passwordHash == $dbPasswordHash;
    if (!$passwordDoesMatch) {
      http_response_code(403);
      die("Wrong Password.");
    }
  }

  $sessionId = rand();
  $sql = $conn->prepare("UPDATE admin_properties 
    SET value = :sid WHERE name = 'current_session'");
  $sql->bindValue(':sid', $sessionId);
  if (!$sql->execute()) {
    http_response_code(500);
    die("Failed while creating session.");
  }

  $sql = $conn->prepare("UPDATE admin_properties 
    SET value = datetime('now') WHERE name = 'session_start'");
  if (!$sql->execute()) {
    http_response_code(500);
    die("Failed while starting session.");
  }
  
  http_response_code(201);
  die_JSON($sessionId);
}

// TODO: only admin

//change password
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
  $content = file_get_contents('php://input');
  $passwordHash = hash("sha256", $content + $pepper);

  $sql = $conn->prepare("UPDATE admin_properties 
    SET value = :pwhash WHERE name = 'password_hash'");
  $sql->bindValue(':pwhash', $passwordHash);
  if (!$sql->execute()) {
    http_response_code(500);
    die("Failed while updating password.");
  }

  http_response_code(200);
  die("Password Created");
}

?>