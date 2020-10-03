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

function isAdmin($conn) {
  $sql = $conn->prepare("SELECT name, value FROM admin_properties
    WHERE name = 'current_session' OR name='session_start'");
  $sql->execute();
  $dict = $sql->fetchAll(PDO::FETCH_KEY_PAIR);
  if (!$dict || !$dict["current_session"] || !$dict["session_start"]) return FALSE;
  
  if ($_COOKIE['gja'] != $dict["current_session"]) return FALSE;
  
  $sessionLength = 3*24*60*60;
  $sessionEnd = strtotime($dict['session_start']) + $sessionLength;
  if ($sessionEnd < time()) return FALSE;

  return TRUE;
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

/**
 * returns array like [
 *   [team_id] => "name"
 * ]
 */
function getTeamNameDictionary($conn) {
  $sql = $conn->prepare("SELECT team_id, name FROM teams");
  if (!$sql->execute()) die_error("Failed while getting teams.");
  $teamsDict = $sql->fetchAll(PDO::FETCH_KEY_PAIR);
  return $teamsDict;
}

/**
 * returns array like [
 *  [team_id] => ["member", "member"]
 * ]
 */
function getTeamMemberDictionary($conn) {
  $sql = $conn->prepare("SELECT teams.team_id, person_name FROM teams 
    INNER JOIN people ON people.team_id = teams.team_id");
  if (!$sql->execute()) die_error("Failed while getting team members.");
  $members = $sql->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_COLUMN);
  return $members;
}

/**
 * returns array of teams like [
 *    "id" => 1,
 *    "name" => "string",
 *    "members" => ["string", "string"]
 * ]
 */
function getTeamsWithMembers($conn) {
  $teamsDict = getTeamNameDictionary($conn);
  $members = getTeamMemberDictionary($conn);

  $teams = [];
  foreach ($teamsDict as $id => $name) {
    $teams[] = [
      "id" => $id,
      "name" => $name,
      "members" => $members[$id]
    ];
  }
  return $teams;
}

/**
  * returns array of awards like [
  *    "award_id" => 1,
  *    "award" => "string",
  *    "team_id" => 1
  * ]
  */
function getAwards($conn) {
  $sql = $conn->prepare("SELECT award, award_id, team_id FROM awards");
  if (!$sql->execute()) die_error("Failed while getting awards.");
  $awards = $sql->fetchAll(PDO::FETCH_ASSOC);
  return $awards;
}

function getGalleryUrls(){
  if (!file_exists('./files/gallery')) {
    mkdir('./files/gallery', 0777, true);
  }

  $urls = array_diff(scandir("./files/gallery"), array('..', '.'));
  // return $urls;
  return array_values($urls);
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
  
  //sort by submission status
  $teams = getTeamsWithMembers($conn);
  $submissionlessTeams = [];
  foreach ($teams as $i => $team) {
    $id = $team["id"];
    if (!array_key_exists($id, $answers)) {
      $submissionlessTeams[] = $team;
      unset($teams[$i]);
    }
  }
  $teams = array_merge($teams, $submissionlessTeams);

  $awards = getAwards($conn);

  $gallery_urls = getGalleryUrls();

  http_response_code(200);
  return [
    "answers" => $answers,
    "teams" => $teams,
    "awards" => $awards,
    "gallery_urls" => $gallery_urls
  ];
}

?>