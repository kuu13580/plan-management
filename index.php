<!DOCTYPE html>
<html lang="ja">
<head>
  <title>リダイレクト</title>
</head>
<body>
<?php
if (isset($_GET["key"])){
  http_response_code( 301 ) ;
  header( "Location: ./mainpage.php?key=".$_GET["key"]);
  exit ;
}else{
  http_response_code( 301 ) ;
  header( "Location: ./create-event.php");
  exit ;
}
?>
</body>
</html>