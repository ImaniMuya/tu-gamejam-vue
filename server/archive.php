<?php
header('Access-Control-Allow-Origin: http://localhost:8080'); //TODO: make constants file
header("Access-Control-Allow-Methods: POST, OPTIONS");
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  die();
}


include_once("./helper.inc");
$conn = get_db_connection();


if (!isAdmin($conn)) {
  http_response_code(403);
  die("Only Admin can archive.");
}

// POST
if ($_SERVER['REQUEST_METHOD'] == "POST") {
  $content = file_get_contents('php://input');
  $body = json_decode($content);
  $event = $body->name;
  if (!$event) {
    http_response_code(400);
    die("Event name missing.");
  }
  $eventDir = "./past/$event";
  $filepath = "$eventDir/data.json";
  if (file_exists($eventDir)) {
    http_response_code(400);
    die("Event with that name already exists!");
  }

  $eventData = getEventData($conn);
  
  foreach ($body as $key => $value) {
    $eventData[$key] = $value;
  }
  mkdir($eventDir);
  $fp = fopen($filepath, 'w');
  fwrite($fp, json_encode($eventData));
  fclose($fp);

  mkdir("$eventDir/files");
  if (!rename("./files/", "./past/$event/files")) {
    http_response_code(500);
    die("Failed while moving files.");
  }
  mkdir("./files/");

  if (!copy("./sql/current.db", "./past/$event/data.db")) { //maybe unnecessary (already have json)
    http_response_code(500);
    die("Failed while moving db.");
  }

  //reset db
  $query = file_get_contents("./sql/reset-db.sql");
  if (!$conn->exec($query)) {
    http_response_code(500);
    die("Failed to reset db. Make sure to close all sqlite3 terminals.");
  }

  http_response_code(201);
  die("Created past event: $event");
}

?>
