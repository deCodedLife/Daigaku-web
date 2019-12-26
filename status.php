<?php
header('Content-Type: text/html; charset=utf-8');
$errors= array();
$date  = date("d/m/Y");

$stat  = $_GET["stat"];
$astat = preg_split('//u', $stat,  NULL, PREG_SPLIT_NO_EMPTY);

$token = $_GET["token"];
$array = explode ("*", base64_decode($token));
$login = $array[0];


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

if ( trim($token)  == "" ) $errors[] = "Токен не передан!";
if ( trim($stat)   == "" ) $errors[] = "Вы забыли статус... _) ";

for ($i = 0; $i < count($astat); $i++)
{
    if ( !in_array($astat[$i], $symbols) )
    $errors[] = 'Статус содержит недопустимые символы';
}

if ( empty($errors) )
{
    $connect = mysqli_connect("localhost", "id10026645_administrator", "8895304025dr", "id10026645_school"); 
    $request = mysqli_query ($connect, "select * FROM `users` WHERE `login` = \"".$login."\";");
    
    while ( ($res = mysqli_fetch_assoc($request)) )
    {
        $token_c = base64_encode($res["login"]."*".$res["pass"]."*".$date);
        if ($token != $token_c) $errors[] = "Удачи в попытках взлома бд...";
        else $str = $res["id"];
    }
    if ( empty ($errors) )
        mysqli_query($connect, "update `users` set `stat` = \"".$stat."\" WHERE `id` = \"".$str."\";");
    else echo array_shift($errors);
} else echo array_shift($errors);

?>