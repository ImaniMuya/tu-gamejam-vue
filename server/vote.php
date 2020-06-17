<?php
header('Access-Control-Allow-Origin: http://localhost:8080'); //TODO: make constants file
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  die();
}

$teamId = $_COOKIE['gjt'];
$secret = $_COOKIE['gjs'];
if (!$teamId || !$secret) {
  http_response_code(403);
  die("Please log in first.");
}

include_once("./helper.inc");
$conn = get_db_connection();
$team = get_login_team($conn, $teamId, $secret);

if (!$team) {
  http_response_code(403);
  die("Login invalid.");
}

// GET
if ($_SERVER['REQUEST_METHOD'] == "GET") {
  $sql = $conn->prepare("SELECT votes.ranking, themes.theme, themes.theme_id FROM themes
    LEFT JOIN votes ON themes.theme_id = votes.theme_id AND team_id = :teamId
    ORDER BY IFNULL(votes.ranking, 9999)");
  $sql->bindValue(':teamId', $team["team_id"]);
  if (!$sql->execute()) {
    http_response_code(500);
    die("Failed while getting votes.");
  }

  http_response_code(200);
  die_JSON($sql->fetchAll());
}

// POST
if ($_SERVER['REQUEST_METHOD'] == "POST") {
  $content = file_get_contents('php://input');
  $body = json_decode($content);
  if (!$body) die("No Body :(");
  $insertData = array();
  $valStr = implode(",", array_fill(0, count($body), "(".$team["team_id"].", ?, ?)"));
  foreach ($body as $rank => $themeId) {
    $insertData[] = $themeId;
    $insertData[] = $rank;
  }
  $sql = $conn->prepare("INSERT OR REPLACE INTO votes(team_id, theme_id, ranking) VALUES $valStr");
  if (!$sql->execute($insertData)) {
    http_response_code(500);
    die("Failed while updating vote.");
  }
  http_response_code(201);
  
  
  die("Vote Updated.");
}

?>
