<?php
header('Access-Control-Allow-Origin: http://localhost:8080'); //TODO: make constants file
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
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
  die("Only Admin can use themes endpoint.");
}

// GET
if ($_SERVER['REQUEST_METHOD'] == "GET") {
  $sql = $conn->prepare("SELECT theme, theme_id FROM themes");
  if (!$sql->execute()) {
    http_response_code(500);
    die("Failed while getting themes.");
  }
  
  http_response_code(200);
  die_JSON($sql->fetchAll());
}

// POST
if ($_SERVER['REQUEST_METHOD'] == "POST") {
  $content = file_get_contents('php://input');  
  $sql = $conn->prepare("INSERT INTO themes (theme) VALUES (:theme)");
  $sql->bindParam(':theme', $content);
  if (!$sql->execute()) {
    http_response_code(500);
    die("Failed while adding theme.");
  }
  http_response_code(201);

  die_JSON(array(
    "theme_id" => $conn->lastInsertId(),
    "theme" => $content,
  ));
}

// PUT
if ($_SERVER['REQUEST_METHOD'] == "PUT") {
  $content = file_get_contents('php://input');
  if ($_GET["id"] == "") {
    http_response_code(400);
    die("No theme provided to update.");
  }
  
  $sql = "UPDATE themes SET theme = :theme WHERE theme_id = :theme_id";
  $sql = $conn->prepare($sql);
  $sql->bindParam(':theme', $content);
  $sql->bindParam(':theme_id', $_GET["id"]);

  if (!$sql->execute()) {
    http_response_code(500);
    die("Failed while updating theme.");
  }
  
  http_response_code(200);
  die($content);
}

// DELETE
if ($_SERVER['REQUEST_METHOD'] == "DELETE") {
  $sql = $conn->prepare("DELETE FROM themes WHERE theme_id = :theme_id");
  $sql->bindParam(':theme_id', $_GET["id"]);
  if (!$sql->execute()) {
    http_response_code(500);
    die("Failed while deleting theme.");
  }

  http_response_code(200);
  die("Deleted.");
}

?>
