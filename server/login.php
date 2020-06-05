<?php
header('Access-Control-Allow-Origin: http://localhost:8080'); //TODO: make constants file
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Access-Control-Allow-Credentials: true');

$teamId = $_COOKIE['gjt'];
$secret = $_COOKIE['gjs'];
if (!$teamId || !$secret) {
  echo "Missing info.";
  flush();
  die();
}

include_once("./helper.inc");
$conn = get_db_connection();
$team = get_login_team($conn, $teamId, $secret);

if (!$team) {
  http_response_code(403);
  die("Failed to login.");
  flush();
}

http_response_code(200);
die($team["name"]);
flush();
?>
