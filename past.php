<?php
header('Access-Control-Allow-Origin: http://localhost:8080'); //TODO: make constants file
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

include_once("./helper.inc");

// GET
if ($_SERVER['REQUEST_METHOD'] == "GET") {
  if (!array_key_exists("event", $_GET)) {
    header('Content-type: application/json');
    http_response_code(200);
    $pastEvents = array_diff(scandir("./past/"), array('..', '.'));
    die_JSON($pastEvents); //TODO: return json array instead of object
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
