<?php
include_once("./constants.inc");
header("Access-Control-Allow-Origin: $clientOrigin");
header("Access-Control-Allow-Methods: POST, OPTIONS, PUT");
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  die();
}

include_once("helper.inc");

$conn = get_db_connection();

$pepper = "gjivCjruT7S1HBkf6WFhkQPZfZcTk00Y";

function logAttempt($failed) {
  global $conn;
  $failed = $failed ? 1 : 0;
  $sql = $conn->prepare("INSERT INTO admin_logins (time, failed) VALUES (:time, :failed)");
  $sql->bindValue(':time', time());
  $sql->bindValue(':failed', $failed);
  $sql->execute();
}

function tooManyAttempts() {
  global $conn;
  $sql = $conn->prepare("SELECT COUNT(*) FROM admin_logins
    WHERE failed = 1 AND time > :cutoff");
  $cutoff = time() - 10 * 60; // 10 minutes
  $sql->bindValue(":cutoff", time() - 60*5);
  $sql->execute();
  $numAttempts = $sql->fetch()[0];
  return $numAttempts >= 5;
}

//create session
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  if (tooManyAttempts()) {
    http_response_code(403);
    die("Too many login attempts!");
  }

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
      logAttempt(TRUE);
      die("One time password does not match.");
    }
  
    $sql = $conn->prepare("UPDATE admin_properties 
      SET value = '' WHERE name = 'one_time_pass'");
    $sql->execute();
  } else {
    $passwordHash = hash("sha256", $content . $pepper);
    $dbPasswordHash = $dict['password_hash'];
    $passwordDoesMatch = $passwordHash == $dbPasswordHash;
    logAttempt(TRUE);
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
    SET value = :login_time WHERE name = 'session_start'");
  $sql->bindValue(':login_time', time());
  if (!$sql->execute()) {
    http_response_code(500);
    die("Failed while starting session.");
  }

  http_response_code(201);
  logAttempt(FALSE);
  die_JSON($sessionId);
}


//change password
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {

  if (!isAdmin($conn)) {
    http_response_code(403);
    die("Only Admin can change password.");
  }

  $content = file_get_contents('php://input');
  $passwordHash = hash("sha256", $content . $pepper);

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