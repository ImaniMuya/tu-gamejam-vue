<?php

function die_JSON($arr) {
  header('Content-Type: application/json');
  die(json_encode($arr));
}


function get_db_connection() { //TODO: would a try catch here help?
  $conn = new PDO('sqlite:./sql/current.db');
  return $conn;
}

//return team or null if bad login
function get_login_team($conn, $teamId, $secret) {
  $sql = $conn->prepare("SELECT name, team_id FROM teams
    WHERE team_id = :teamId AND the_secret = :teamSecret");
  $sql->bindParam(':teamId', $teamId);
  $sql->bindParam(':teamSecret', $secret);
  if (!$sql->execute()) {
    throw new Exception("Failed while finding team."); //die instead (and error code 500)?
  }
  $team = $sql->fetch();
  return $team;
}

function send_team_email($email, $teamName, $teamId, $secret) {
  $loginURL = "http/localhost:8080/login";
  $msg = "You've been added to team: ".$teamName."! Click this link to sign in! ".$loginURL."?t=".$teamId."&s=".$secret;
  $msg = wordwrap($msg, 70);
  mail($email, "TU GameJam Team Confirmation", $msg);
}

function get_submission_file_path($teamId, $questionId, $baseName) {
  $fileDir = "./files/"; //TODO: constants
  return $fileDir . "$teamId/$questionId-$baseName"; 
}

function try_create_team_dir($teamId) {
  $fileDir = "./files/";  //TODO: constants
  $teamDir = $fileDir . $teamId . "/";
  if (!file_exists($teamDir)) mkdir($teamDir, 0777, true);
}

?>