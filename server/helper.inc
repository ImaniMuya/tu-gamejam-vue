<?php

function die_JSON($arr) {
  header('Content-Type: application/json');
  die(json_encode($arr));
}

function die_error($err) {
  http_response_code(500);
  die($err);
}

function get_db_connection() {
  $conn = new PDO('sqlite:./sql/current.db');
  return $conn;
}

//return team or null if bad login
function get_login_team($conn, $teamId, $secret) {
  $sql = $conn->prepare("SELECT name, team_id FROM teams
    WHERE team_id = :teamId AND the_secret = :teamSecret");
  $sql->bindParam(':teamId', $teamId);
  $sql->bindParam(':teamSecret', $secret);
  $sql->execute();
  $team = $sql->fetch();
  return $team;
}

function send_team_email($email, $teamName, $teamId, $secret) {
  $loginURL = "http/localhost:8080/login";
  $msg = "You've been added to team: ".$teamName."! Click this link to sign in! ".$loginURL."?t=".$teamId."&s=".$secret;
  $msg = wordwrap($msg, 70);
  mail($email, "TU GameJam Team Confirmation", $msg);
}

function get_submission_file_path($teamId, $questionId, $baseName) {
  $fileDir = "./files/"; //TODO: constants
  return $fileDir . "$teamId/$questionId-$baseName"; 
}

function try_create_team_dir($teamId) {
  $fileDir = "./files/";  //TODO: constants
  $teamDir = $fileDir . $teamId . "/";
  if (!file_exists($teamDir)) mkdir($teamDir, 0777, true);
}

function getEventData($conn) { //TODO: separate file for sql calls
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
  $teamsDict = $sql->fetchAll(PDO::FETCH_KEY_PAIR);

  //team members
  $sql = $conn->prepare("SELECT teams.team_id, person_name FROM teams 
    INNER JOIN people ON people.team_id = teams.team_id");
  if (!$sql->execute()) die_error("Failed while getting team members.");
  $members = $sql->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_COLUMN);

  $teams = [];
  $submissionlessTeams = [];
  foreach ($teamsDict as $id => $name) {
    $team = [
      "id" => $id,
      "name" => $name,
      "members" => $members[$id]
    ];
    if (array_key_exists($id, $answers)) $teams[] = $team;
    else $submissionlessTeams[] = $team;
  }
  $teams = array_merge($teams, $submissionlessTeams);

  // question categories
  // $sql = $conn->prepare("SELECT question, category_name FROM subm_questions 
  //   INNER JOIN subm_categories ON subm_questions.question_category = subm_categories.category_id");
  // if (!$sql->execute()) die_error("Failed while getting categories.");
  // $categories = $sql->fetchAll(PDO::FETCH_KEY_PAIR);

  http_response_code(200);
  return ["answers" => $answers, "teams" => $teams];
}

?>