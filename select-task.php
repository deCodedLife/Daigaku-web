<?php
header('Content-Type: text/html; charset=utf-8');
$date_ = date("d/m/Y");
$token = $_GET["token"];
$id    = $_GET["id"];
$array = explode( "*", base64_decode( $token ) );
$login = $array[0];
$passw = $array[1];
$udate = $array[2];
$errors = array();

if ( !is_numeric($id) ) $errors[] = "id задачи должно быть числом";

if ( empty($errors) ) {
  $connect = mysqli_connect("localhost", "your_database_login", "your_database_password", "database_name");
  $request = mysqli_query  ($connect, "select * FROM `users` WHERE name = \"$login\"");
  $res = mysqli_fetch_assoc($request);
  $ctoken = base64_encode($res["name"]."*".$res["pass"]."*".$date_);
  if ( $res["name"] != $login ) $errors[] = "Пользователь не существует!";
  if ($ctoken != $token) $errors[] = "Удачи во взломе...";
  $request = mysqli_query($connect, "select * FROM `Tasks` WHERE id = $id");
  $res = mysqli_fetch_assoc($request);
  if ( $res["attached"] != "false" ) $errors[] = "Реферат уже занят";
  if (empty ($errors)) {
      mysqli_query($connect, "update `Tasks` SET attached = \"$login|".date("Y-m-d")."\" WHERE id = $id");
      echo "succsess";
  } else echo array_shift($errors);
} else echo array_shift($errors);
?>
