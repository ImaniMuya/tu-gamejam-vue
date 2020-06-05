<?php

function get_db_connection() { //TODO: would a try catch here help?
  $conn = new PDO('sqlite:./sql/current.db');
  return $conn;
}

//return team or null if bad login
function get_login_team($conn, $teamId, $secret) {
  $sql = $conn->prepare("SELECT name, team_id FROM teams
    WHERE team_id = :teamId AND the_secret = :teamSecret");
  $sql->bindParam(':teamId', $teamId);
  $sql->bindParam(':teamSecret', $secret);
  if (!$sql->execute()) {
    throw new Exception("Failed while finding team."); //die instead (and error code 500)?
  }
  $team = $sql->fetch();
  return $team;
}

?>