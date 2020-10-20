<?php
include_once("./constants.inc");
header("Access-Control-Allow-Origin: $clientOrigin");
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

  // if (!copy("./sql/current.db", "./past/$event/data.db")) { //maybe unnecessary (already have json)
  //   http_response_code(500);
  //   die("Failed while moving db.");
  // }

  //reset db
  $query = file_get_contents("./sql/reset-db.sql");
  $reset_result = $conn->exec($query);
  if ($reset_result === FALSE) {
    http_response_code(500);
    die("Failed to reset db. Everything else probably worked.");
  }

  http_response_code(201);
  die("Created past event: $event");
}

?>
