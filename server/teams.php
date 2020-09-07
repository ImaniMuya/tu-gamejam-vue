<?php
header('Access-Control-Allow-Origin: http://localhost:8080'); //TODO: make constants file
header("Access-Control-Allow-Methods: GET");
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  die();
}
// TODO
// if (/* something */) {
//   http_response_code(403);
//   die("Admin only.");
// }

include_once("helper.inc");
$conn = get_db_connection();

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


?>