<?php
$data;
include "_connectDB.php";
if (!($_POST["latest"]) or !($_POST["key"])) {
    echo "error";
    exit;
}
$sql = $dbh->prepare("SELECT latest FROM events_table WHERE event_key = ?");
$sql->execute(array($_POST["key"]));
$latest = $sql->fetch();
if ($_POST["latest"] == $latest) {
    $data["renew"] = false;
  } else {
    $data["renew"] = true;
}
$data["latest"] = $latest;
header("Content-type: application/json; charset=UTF-8");
echo json_encode($data);
?>
