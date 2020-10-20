<?php
include_once("./constants.inc");
header("Access-Control-Allow-Origin: $clientOrigin");
header("Access-Control-Allow-Methods: GET");
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

include_once("helper.inc");
$conn = get_db_connection();

if ($_SERVER['REQUEST_METHOD'] == "GET") {
  die_JSON(getEventData($conn));
}

?>