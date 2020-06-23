<?php
header('Access-Control-Allow-Origin: http://localhost:8080'); //TODO: make constants file
header("Access-Control-Allow-Methods: GET");
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

include_once("helper.inc");
$conn = get_db_connection();

if ($_SERVER['REQUEST_METHOD'] == "GET") {
  // get answers
  $sql = $conn->prepare("SELECT ans.team_id, que.question_id, que.question AS q, ans.answer AS a
    FROM subm_questions que
    INNER JOIN subm_answers ans ON que.question_id = ans.question_id");
  if (!$sql->execute()) die_error("Failed while getting submissions.");
  $result = $sql->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC);
  $answers = [];
  foreach ($result as $tid => $teamResult) {
    $teamAnswers = [];
    foreach ($teamResult as $answer) {
      $teamAnswers[$answer["q"]] = array(
        "value" => $answer["a"],
        "qid" => $answer["question_id"]
      );
    }
    $answers[$tid] = $teamAnswers;
  }
  
  // teams list
  $sql = $conn->prepare("SELECT team_id, name FROM teams");
  if (!$sql->execute()) die_error("Failed while getting teams.");
  $teams = $sql->fetchAll(PDO::FETCH_KEY_PAIR);

  //team members
  $sql = $conn->prepare("SELECT teams.team_id, person_name FROM teams 
    INNER JOIN people ON people.team_id = teams.team_id");
  if (!$sql->execute()) die_error("Failed while getting team members.");
  $members = $sql->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_COLUMN);

  foreach ($teams as $id => &$team) {
    $team = array(
      "name" => $team,
      "members" => $members[$id]
    );
  }

  // question categories
  // $sql = $conn->prepare("SELECT question, category_name FROM subm_questions 
  //   INNER JOIN subm_categories ON subm_questions.question_category = subm_categories.category_id");
  // if (!$sql->execute()) die_error("Failed while getting categories.");
  // $categories = $sql->fetchAll(PDO::FETCH_KEY_PAIR);

  http_response_code(200);
  die_JSON(array(
    "answers" => $answers,
    "teams" => $teams,
  ));
}


?>