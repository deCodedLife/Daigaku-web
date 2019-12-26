<?php

header('Content-Type: text/html; charset=utf-8');
$date  = date("d/m/Y");
$pick  = $_GET["pick"];
$mess  = $_GET["message"];
$token = $_GET["token"];
$amess = preg_split('//u', $mess,   NULL, PREG_SPLIT_NO_EMPTY);

$array = explode("*", base64_decode($token));
$login = $array[0];
$time  = $array[2];
$errors= array();

$symbols = array(
    'А','а','Б','б','В','в','Г','г','Д','д','Е','е','Ё','ё',
    'Ж','ж','З','з','И','и','Й','й','К','к','Л','л','М','м',
    'О','о','П','п','Р','р','С','с','Т','т','У','у','Ф','ф',
    'Х','х','Ц','ц','Ч','ч','Ш','ш','Щ','щ','ы','ъ','ь','Э',
    'э','Ю','ю','Я','я','_',' ', 'Н', ' ', 'н',
    'A','a','B','b','C','c','D','d','E','e','F','f','G','g',
    'H','h','I','i','J','j','K','k','L','l','M','m','N','n',
    'O','o','P','p','Q','q','R','r','S','s','T','t','U','u',
    'V','v','W','w','X','x','Y','y','Z','z','!','@','$','%',
    '*','1','2','3','4','5','6','7','8','9','0','.','-','/'
);

$nums = array("1","0");

if ( trim($token) == "" ) $errors[] = "Токен не указан";
if ( $time != $date ) $errors[] = "Ваш токен устарел";

if ( trim($mess)  == "" ) $errors[] = "Вы должны указать сообщение";
if ( trim($pick)  == "" ) $errors[] = "Вы должны указать важность события";
if ( count($amess) > 1500 ) $errors[] = "Слишком длинное сообщение";
if ( !in_array($pick, $nums) ) $errors[] = 'Важность события содержит недопустимые символы';

for ($i = 0; $i < count($amess); $i++)
{
    if ( !in_array($amess[$i], $symbols) )
    $errors[] = 'Сообщение содержит недопустимые символы';
}
if ($pick != 1 && $pick != 0) $errors[] = "Отметка важности передана неправильно";

if ( empty($errors) )
{
    $connect = mysqli_connect("localhost", "your_database_login", "your_database_password", "database_name");
    $request1 = mysqli_query ($connect, "select * FROM `users` WHERE name = \"$login\"");
    $request2 = mysqli_query ($connect, "select * FROM `groups` WHERE name = \"$gname\"");
    $res1 = mysqli_fetch_assoc($request1);
    $res2 = mysqli_fetch_assoc($request2);
    $token_c = base64_encode($res1["name"]."*".$res1["pass"]."*".$date);
    if ($token != $token_c) $errors[] = "~~LALALA You idiot hacker";
    if ($res2["name"] != $gname) $errors[] = "Такой группы не существует";
    if ($res1["status"]  != "updater" && $res1["status"] != "admin" && $res1["status"] != "curator") $errors[] = "Вы не староста своей группы";
    if (empty($errors)){
        $gname = $res1["fgroup"];
        mysqli_query($connect, "insert INTO `messages` (`groups`, `pick`, `message`, `operator`, `date`) VALUES (\"$gname\", \"$pick\", \"$mess\", \"$login\", \"".date("Y-m-d")."\")");
        echo "succsess";
    }
    else echo array_shift($errors);
} else echo array_shift($errors);

?>
