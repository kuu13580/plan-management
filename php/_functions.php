<?php
function convertNull($data){
  if ($data == "" OR $data == "00:00" OR $data == 0){
    return null;
  }else{
    return $data;
  }
}
?>