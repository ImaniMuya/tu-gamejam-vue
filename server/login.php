<?php
header('Access-Control-Allow-Origin: http://localhost:8080'); //TODO: make constants file
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Access-Control-Allow-Credentials: true');

$teamId = $_COOKIE['gjt'];
$secret = $_COOKIE['gjs'];
if (!$teamId || !$secret) {
  http_response_code(400);
  die("Missing info.");
}

include_once("./helper.inc");
$conn = get_db_connection();
$team = get_login_team($conn, $teamId, $secret);

if (!$team) {
  http_response_code(400);
  die("Failed to login.");
}

http_response_code(200);
die_JSON(array(
  "name" => $team["name"],
  "id" => $team["team_id"]
));
?>
