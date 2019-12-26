<?php

header('Content-Type: text/html; charset=utf-8');
$time = date("d/m/Y");
$errors = [];

$login = $_GET["name"];
$passw = $_GET["pass"];
$alogin= preg_split('//u', $login, NULL, PREG_SPLIT_NO_EMPTY);
$apassw= preg_split('//u', $passw, NULL, PREG_SPLIT_NO_EMPTY);

if ( trim($login) == "" ) $errors[] = "Логин не указан!";
if ( trim($passw) == "" ) $errors[] = "Пароль не указан!";

$symbols = array(
    'А','а','Б','б','В','в','Г','г','Д','д','Е','е','Ё','ё',
    'Ж','ж','З','з','И','и','Й','й','К','к','Л','л','М','м',
    'О','о','П','п','Р','р','С','с','Т','т','У','у','Ф','ф',
    'Х','х','Ц','ц','Ч','ч','Ш','ш','Щ','щ','ы','ъ','ь','Э',
    'э','Ю','ю','Я','я','_', ' ', 'Н', 'н',
    'A','a','B','b','C','c','D','d','E','e','F','f','G','g',
    'H','h','I','i','J','j','K','k','L','l','M','m','N','n',
    'O','o','P','p','Q','q','R','r','S','s','T','t','U','u',
    'V','v','W','w','X','x','Y','y','Z','z','!','@','$','%',
    '*','1','2','3','4','5','6','7','8','9','0','.','-','/'
);

for ($i = 0; $i < count($alogin); $i++)
{
    if ( !in_array($alogin[$i], $symbols) )
    $errors[] = 'Имя содержит недопустимые символы';
}
for ($i = 0; $i < count($apassw); $i++)
{
    if ( !in_array($apassw[$i], $symbols) )
    $errors[] = 'Пароль содержит недопустимые символы';
}

if (empty($errors) )
{
    $connect = mysqli_connect("localhost", "id10026645_administrator", "8895304025dr", "id10026645_school");
    $request = mysqli_query  ($connect, "select * FROM `users` WHERE name = \"$login\"");
    $res = mysqli_fetch_assoc($request);
    if ( $login != $res["name"] )  $errors[] = "Вас нет в списке пользователей";
    if ( hash("sha256",$passw)  != $res["pass"] ) $errors[] = "Пароль введён неверно";
    if ( empty($errors) ) 
    {
        $token = base64_encode($login."*".hash("sha256", $passw)."*".$time);
        echo $token;
    } else echo array_shift($errors);
} else echo array_shift($errors);

?>