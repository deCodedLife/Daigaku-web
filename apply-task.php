<?php

header("Content-type: text/html; charset=utf-8");
$date_ = date("d/m/Y");
$id    = $_GET["id"];
$token = $_GET["token"];
$array = explode( "*", base64_decode( $token ) );
$login = $array[0];
$passw = $array[1];
$udate = $array[2];
$errors = array();

if ( !isset( $token ) ) $errors[] = "Токен не указан";
if ( $udate != $date_ ) $errors[] = "Токен просрочен";
if ( !is_numeric($id) ) $errors[] = 'Id реферата должно быть числом';
if ( !isset( $id ) )  $errors[] = "Id Реферата не указан";
if ( trim( $token ) == "" ) $errors[] = "Токен не указан";

if ( empty( $errors ) ) {
  $connect = mysqli_connect("localhost", "id10026645_administrator", "8895304025dr", "id10026645_school");
  $request = mysqli_query  ($connect, "select * FROM `users` WHERE name = \"$login\"");
  $res = mysqli_fetch_assoc($request);
  $ctoken = base64_encode($res["name"]."*".$res["pass"]."*".$date_);
  if ( $res["name"] != $login ) $errors[] = "Пользователь не существует!";
  if ($ctoken != $token) $errors[] = "Удачи во взломе...";
  if ($res["status"] != "curator" && $res["status"] != "admin") $errors[] = "У вас нет прав на выполнение этого";
  if (empty ($errors)) {
      mysqli_query($connect, "update `Tasks` SET finished = 1 WHERE id = $id");
      echo "succsess";
  } else echo array_shift( $errors );
} else echo array_shift( $errors );

?>
