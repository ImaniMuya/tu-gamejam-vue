<?php
include_once("./constants.inc");
header("Access-Control-Allow-Origin: $clientOrigin");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

include_once("./helper.inc");

// GET
if ($_SERVER['REQUEST_METHOD'] == "GET") {
  if (!array_key_exists("event", $_GET)) {
    header('Content-type: application/json');
    http_response_code(200);
    //TODO: return json array instead of object (use array_values)
    //TODO: return in proper sorted order (need to store order somewhere)
    $pastEvents = array_diff(scandir("./past/"), array('..', '.'));
    die_JSON($pastEvents);
  }

  $event = $_GET["event"];

  if ($event == "") {
    http_response_code(400);
    die("No event to get.");
  }
  $filepath = "./past/$event/data.json";
  if (!file_exists($filepath)) {
    http_response_code(400);
    die("Event doesn't exist.");
  }

  header('Content-type: application/json');
  http_response_code(200);
  die(file_get_contents($filepath));
}
?>
