<?php

header('Content-Type: text/html; charset=utf-8');
$errors = array();
$date  = date("d/m/Y");
$token = $_GET["token"];
$path  = $_GET["path"];
$apath = preg_split('//u', $path,  NULL, PREG_SPLIT_NO_EMPTY);

$symbols = array(
    'А','а','Б','б','В','в','Г','г','Д','д','Е','е','Ё','ё',
    'Ж','ж','З','з','И','и','Й','й','К','к','Л','л','М','м',
    'О','о','П','п','Р','р','С','с','Т','т','У','у','Ф','ф',
    'Х','х','Ц','ц','Ч','ч','Ш','ш','Щ','щ','ы','ъ','ь','Э',
    'э','Ю','ю','Я','я','_','Н', ' ', 'н',
    'A','a','B','b','C','c','D','d','E','e','F','f','G','g',
    'H','h','I','i','J','j','K','k','L','l','M','m','N','n',
    'O','o','P','p','Q','q','R','r','S','s','T','t','U','u',
    'V','v','W','w','X','x','Y','y','Z','z','!','@','$','%',
    '*','1','2','3','4','5','6','7','8','9','0','.','-','/'
);

for ($i = 0; $i < count($apath); $i++)
{
    if ( !in_array($apath[$i], $symbols) )
    $errors[] = 'Имя пути содержит недопустимые символы';
}

$array = explode("*", base64_decode($token));
$login = $array[0];
$time  = $array[2];

if ( trim($token) == "" ) $errors[] = "Как так то? Токен не задан...";
if ( trim($path)  == "" ) $errors[] = "Вы не выбрали путь";
if ( $time != $date )     $errors[] = "Выш токен устарел.";

if ( empty($errors) )
{
    $connect = mysqli_connect("localhost", "id10026645_administrator", "8895304025dr", "id10026645_school");
    $request = mysqli_query ($connect, "select * FROM `users` WHERE name = \"$login\";");
    $res =mysqli_fetch_assoc($request);
    $token_c = base64_encode($res1["name"]."*".$res1["pass"]."*".$date);
    if ($token != $token_c) $errors[] = "Удачи во взломе, идиоты...";
    if ($res["status"] != "admin" && $res["status"] != "updater" && $res["status"] != "curator") $errors[] = "У вас нет прав на изменение!";
    if ( empty($errors))
    {
        mysqli_query($connect, "delete from `Images` WHERE path = \"$path\"");
        unlink($path);
        echo "succsess";
    } else echo array_shift($errors);
} else echo array_shift($errors);

?>
