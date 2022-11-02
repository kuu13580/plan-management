<?php
$dbn = "mysql:dbname=qdmat_plan;host=localhost";
$user = " ";
$password = " ";
// データベース接続
try {
    $dbh = new PDO($dbn, $user, $password);
} catch (PDOException $e) {
    print('Error:'.$e->getMessage());
    die();
}
?>