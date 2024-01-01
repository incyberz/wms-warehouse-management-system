<?php
include 'harus_login.php';

$path = $_GET['path'] ?? die(erid('path')); if(!$path) die(erid('path::empty'));
$file = $_GET['file'] ?? die(erid('file')); if(!$file) die(erid('file::empty'));

$target = "../$path/$file";
if(file_exists($target)){
  unlink($target);
  echo 'sukses';
}else{
  echo 'file not found.';
}