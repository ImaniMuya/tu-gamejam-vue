<?php
include_once("./constants.inc");
header("Access-Control-Allow-Origin: $clientOrigin");
header("Access-Control-Allow-Methods: GET, POST");
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
  $sql = $conn->prepare("SELECT q.question_id, q.question, q.placeholder, c.category_name, a.answer
    FROM subm_questions q
    INNER JOIN subm_categories c ON q.question_category = c.category_id
    LEFT JOIN subm_answers a ON a.question_id = q.question_id AND team_id = :teamId");
  $sql->bindValue(':teamId', $team["team_id"]);


  if (!$sql->execute()) {
    http_response_code(500);
    die("Failed while getting submission.");
  }

  http_response_code(200);
  die_JSON($sql->fetchAll());
}

// POST
if ($_SERVER['REQUEST_METHOD'] == "POST") {
  foreach($_POST as $qid => $ans) {
    if ($ans) {
      // $ans = filter_input(INPUT_POST, $ans, FILTER_SANITIZE_STRING);
      $sql = $conn->prepare("INSERT OR REPLACE INTO 
        subm_answers(answer, question_id, team_id)
        VALUES(:answer, :question_id, :team_id)");
      $sql->bindValue(':question_id', $qid);
      $sql->bindValue(':answer', $ans);
      $sql->bindValue(':team_id', $team["team_id"]);
    } else { 
      //delete existing answer if field is empty
      $sql = $conn->prepare("DELETE FROM subm_answers
        WHERE question_id=:question_id AND team_id=:team_id");
      $sql->bindValue(':question_id', $qid);
      $sql->bindValue(':team_id', $team["team_id"]);
    }
    if (!$sql->execute()) die_error("Failed while updating submission.");
  }
  foreach($_FILES as $qid => $file) {
    if (!$file["name"]) continue;
    $basename = basename($file["name"]);
    if ($file["size"] > 72428800) { //50MB TODO: constants
      http_response_code(400);
      die("File exceeds size limit.");
    }
    //check extension
    $fileExt = strtolower(pathinfo($basename, PATHINFO_EXTENSION));
    $allowedExtensions = array("zip","png","jpg","gif"); //TODO: constansts
    if (!in_array($fileExt, $allowedExtensions)) {
      http_response_code(400);
      die("Unsupported file extension: " . $fileExt . " file: " . $basename);
    }

    //upload file
    try_create_team_dir($team["team_id"]);
    $target_file = get_submission_file_path($team["team_id"], $qid, $basename);
    if (!move_uploaded_file($file["tmp_name"], $target_file)) {
      http_response_code(500);
      die("Failed while uploading file.");
    }

    //delete old file
    $sql = $conn->prepare("SELECT answer FROM subm_answers 
      WHERE question_id=:question_id AND team_id=:team_id");
    $sql->bindValue(':question_id', $qid);
    $sql->bindValue(':team_id', $team["team_id"]);
    $sql->execute();
    $result = $sql->fetch(PDO::FETCH_ASSOC);
    if ($result && $result["answer"] != $basename) {
      $oldFile = get_submission_file_path($team["team_id"], $qid, $result["answer"]);
      unlink($oldFile);
    }

    //update database
    $sql = $conn->prepare("INSERT OR REPLACE INTO
      subm_answers(answer, question_id, team_id)
      VALUES(:answer, :question_id, :team_id)");
    $sql->bindValue(':question_id', $qid);
    $sql->bindValue(':answer', $basename);
    $sql->bindValue(':team_id', $teamId);
    if (!$sql->execute()) {
      http_response_code(500);
      die("Problem saving files. Try again later.");
    }

  }
  http_response_code(201);
  die("Submission Updated.");
}

?>
