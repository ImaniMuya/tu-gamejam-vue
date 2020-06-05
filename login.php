<?php
header('Access-Control-Allow-Origin: http://localhost:8080'); //TODO: make constants file
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Access-Control-Allow-Credentials: true');

$teamId = $_COOKIE['team'];
$secret = $_COOKIE['secret'];
if (!$teamId || !$secret) {
  echo "Missing info.";
  flush();
  die();
}

include("./db.php");

$sql = $conn->prepare("SELECT name FROM teams
        WHERE team_id = :teamId AND the_secret = :teamSecret");
$sql->bindParam(':teamId', $teamId);
$sql->bindParam(':teamSecret', $secret);
if (!$sql->execute()) {
  http_response_code(500);
  echo "Failed while finding team.";
  flush();
  die();
}

$team = $sql->fetch();
if (!$team) {
  http_response_code(403);
  echo "Failed to login.";
  flush();
  die();
}

http_response_code(200);
echo $team["name"];
flush();
die();

?>