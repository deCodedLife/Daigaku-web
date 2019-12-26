<?php
header('Content-Type: text/html; charset=utf-8');
$date_ = date("d/m/Y");
$token = $_GET["token"];
$array = explode( "*", base64_decode( $token ) );
$login = $array[0];
$passw = $array[1];
$udate = $array[2];
$errors = array();

if ( !isset( $_GET["token"] ) ) $errors[] = "Токен не указан";
if ( trim( $token ) == "" ) $errors[] = "Токен не указан";
if ( $udate != $date_ ) $errors[] = "Токен устарел";

if ( empty( $errors ) ) {
    $connect = mysqli_connect( "localhost", "id10026645_administrator", "8895304025dr", "id10026645_school" );
    $request = mysqli_query  ( $connect, "select * FROM `users` WHERE name = \"$login\"" );
    $sclresp = mysqli_fetch_assoc( $request );
    if ( $passw != $sclresp["pass"] ) $errors[] = "Удачи во взломе, идиоты...";
    if ( empty( $errors ) ) {
        $ugroup = $sclresp["fgroup"];
        $request = mysqli_query( $connect, "select * FROM `groups` WHERE name = \"$ugroup\"" );
        $sclresp = mysqli_fetch_assoc( $request );
        $object->operator = $sclresp["operator"];
        $object->curator  = $sclresp["curator"];
        echo json_encode($object, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
    } else echo array_shift( $errors );
} else echo array_shift( $errors );

?>
