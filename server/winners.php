<?php
include_once("./constants.inc");
header("Access-Control-Allow-Origin: $clientOrigin");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  die();
}

include_once("./helper.inc");
$conn = get_db_connection();

//GET
if ($_SERVER['REQUEST_METHOD'] == "GET") {
  $sql = $conn->prepare("SELECT award, award_id, team_id FROM awards");
  if (!$sql->execute()) {
    http_response_code(500);
    die("Failed while getting awards.");
  }
  
  http_response_code(200);
  die_JSON($sql->fetchAll(PDO::FETCH_ASSOC));
}

?>