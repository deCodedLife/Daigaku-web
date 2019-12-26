<?php
$sdate  = date("d/m/Y");
$logon = $_GET["username"];
$group = $_GET["group"];
$token = $_GET["token"];
$user_ = preg_split('//u', $logon, NULL, PREG_SPLIT_NO_EMPTY);
$nyau_ = preg_split('//u', $group, NULL, PREG_SPLIT_NO_EMPTY);
$array = explode("*", base64_decode($token));
$login = $array[0];
$passw = $array[1];
$udate = $array[2];
$errors = array();

$symbols = array(
    'А','а','Б','б','В','в','Г','г','Д','д','Е','е','Ё','ё',
    'Ж','ж','З','з','И','и','Й','й','К','к','Л','л','М','м',
    'О','о','П','п','Р','р','С','с','Т','т','У','у','Ф','ф',
    'Х','х','Ц','ц','Ч','ч','Ш','ш','Щ','щ','ы','ъ','ь','Э',
    'э','Ю','ю','Я','я','_', 'Н', ' ', 'н',
    'A','a','B','b','C','c','D','d','E','e','F','f','G','g',
    'H','h','I','i','J','j','K','k','L','l','M','m','N','n',
    'O','o','P','p','Q','q','R','r','S','s','T','t','U','u',
    'V','v','W','w','X','x','Y','y','Z','z','!','@','$','%',
    '*','1','2','3','4','5','6','7','8','9','0','.','-','/'
);

if ( trim( $token ) == "" ) $errors[] = "Токен не указан";
if ( trim( $logon ) == "" ) $errors[] = "Не указано имя пользователя";
if ( trim( $group ) == "" ) $errors[] = "Не указана группа";
if ( $sdate != $udate )     $errors[] = "Ваш токен устарел";
for ($i = 0; $i < count($user_); $i++)
{
    if ( !in_array($user_[$i], $symbols) )
    $errors[] = 'Имя пользователя содержит недопустимые символы';
}
for ($i = 0; $i < count($nyau_); $i++)
{
    if ( !in_array($nyau_[$i], $symbols) )
    $errors[] = 'Имя группы содержит недопустимые символы';
}

if ( empty( $errors ) ) {
    $connect = mysqli_connect("localhost", "id10026645_administrator", "8895304025dr", "id10026645_school");
    $request = mysqli_query  ($connect, "select * FROM `users` WHERE name = \"$login\"");
    $res = mysqli_fetch_assoc($request);
    if ( $passw != $res["pass"] )    $errors[] = "きみはばかです Не пытайтесь взломать систему";
    if ( $res["status"] != "admin" ) $errors[] = "У вас недостаточно прав для этого";
    if ( empty( $errors ) ) {
        mysqli_query ($connect, "update `users` SET fgroup = \"$group\" WHERE name = \"$logon\"");
        echo "succsess";
    } else echo array_shift( $errors );
} else echo array_shift( $errors );
?>
