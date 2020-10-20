<?php
include_once("./constants.inc");
header("Access-Control-Allow-Origin: $clientOrigin");
header("Access-Control-Allow-Methods: GET, PUT, OPTIONS");
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
  die("Only Admin can access event properties.");
}

// GET
if ($_SERVER['REQUEST_METHOD'] == "GET") {
  $sql = $conn->prepare("SELECT name, value FROM event_properties");
  if (!$sql->execute()) {
    http_response_code(500);
    die("Failed while getting themes.");
  }
  
  http_response_code(200);
  die_JSON($sql->fetchAll(PDO::FETCH_ASSOC));
}

// PUT
if ($_SERVER['REQUEST_METHOD'] == "PUT") {
  $content = file_get_contents('php://input');  
  $body = json_decode($content);
  $sql = $conn->prepare("UPDATE event_properties SET value = :v WHERE name = :n");
  $sql->bindValue(':v', $body->value);
  $sql->bindValue(':n', $body->name);
  

  if (!$sql->execute()) {
    http_response_code(500);
    die("Failed while editing property.");
  }
  http_response_code(200);
  die_JSON(array(
    "name" => $body->name,
    "value" => $body->value
  ));
}

?>
