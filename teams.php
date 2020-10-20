<?php
include_once("./constants.inc");
header("Access-Control-Allow-Origin: $clientOrigin");
header("Access-Control-Allow-Methods: GET, DELETE");
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  die();
}

include_once("helper.inc");
$conn = get_db_connection();


if (!isAdmin($conn)) {
  http_response_code(403);
  die("Only Admin can use teams endpoint.");
}

if ($_SERVER['REQUEST_METHOD'] == "GET") {
  $members = getTeamMemberDictionary($conn);

  $sql = $conn->prepare("SELECT team_id, name, the_secret FROM teams");
  if (!$sql->execute()) die_error("Failed while getting teams.");
  $teamsDict = $sql->fetchAll(PDO::FETCH_ASSOC);
  $teams = [];
  foreach ($teamsDict as $team) {
    $id = $team["team_id"];
    $teams[] = [
      "id" => $id,
      "name" => $team["name"],
      "secret" => $team["the_secret"],
      "members" => $members[$id]
    ];
  }

  die_JSON($teams);
}

if ($_SERVER['REQUEST_METHOD'] == "DELETE") {
  $id = $_GET["id"];
  // $sql = $conn->prepare("DELETE FROM people WHERE team_id = :id");
  // $sql->bindValue(':id', $id);
  // if (!$sql->execute()) die_error("Failed while removing people.");

  $sql = $conn->prepare("DELETE FROM teams WHERE team_id = :id");
  $sql->bindValue(':id', $id);
  if (!$sql->execute()) die_error("Failed while removing team.");
  
  http_response_code(200);
  die("Removed team with ID $id");
}


?>