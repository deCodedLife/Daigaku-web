<?php

header('Content-Type: text/html; charset=utf-8');
$date  = date("d/m/Y");
$tname = $_GET["tag"];
$group = $_GET["group"];
$token = $_GET["token"];
$atname= preg_split('//u', $tname,  NULL, PREG_SPLIT_NO_EMPTY);
$agroup= preg_split('//u', $group,  NULL, PREG_SPLIT_NO_EMPTY);

$array = explode("*", base64_decode($token));
$login = $array[0];
$time  = $array[2];
$errors= array();

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

if ( trim($token) == "" ) $errors[] = "Не передан токен!";
if ($time != $date) $errors[] = "Ваш токен устарел";
if ( trim($group) == "" ) $errors[] = "Вы должны указать группу!";
if ( trim($tname) == "" ) $errors[] = "Вы должны указать предмет";

for ($i = 0; $i < count($atname); $i++)
{
    if ( !in_array($atname[$i], $symbols) )
    $errors[] = 'Имя предмета содержит недопустимые символы';
}
for ($i = 0; $i < count($agroup); $i++)
{
    if ( !in_array($agroup[$i], $symbols) )
    $errors[] = 'Имя группы содержит недопустимые символы';
}

if ( empty($errors) )
{
    $connect = mysqli_connect("localhost", "id10026645_administrator", "8895304025dr", "id10026645_school");
    $request1 = mysqli_query  ($connect, "select * FROM `users` WHERE name = \"$login\";");
    $request2 = mysqli_query  ($connect, "select * FROM `groups` WHERE name = \"$group\"");
    $request3 = mysqli_query  ($connect, "select * FROM `tags` WHERE groups = \"$group\"");
    $request4 = mysqli_query  ($connect, "select * FROM `Images` WHERE groups = \"$group\" AND tag = \"$tname\"");
    $res1 =mysqli_fetch_assoc($request1);
    $res2 =mysqli_fetch_assoc($request2);
    $res3 =mysqli_fetch_assoc($request3);
    $token_c = base64_encode($res1["name"]."*".$res1["pass"]."*".$date);
    if ($token != $token_c) $errors[] = "Удачи во взломе!";
    if ($res1["status"] != "admin" && $res1["status"] != "curator" && $res1["status"] != "updater") $errors[] = "У вас нет прав на выполнение операции";
    if ($res3["static"] == 1 && $res1["status"] != "curator" && $res1["status"] != "admin" ) $errors[] = "Вам нельзя это удалить";
    if ( empty ($errors) ) {
        mysqli_query($connect, "delete FROM `tags` WHERE groups =\"$group\" AND tag = \"$tname\"");
        mysqli_query($connect, "delete FROM `Images` WHERE groups =\"$group\" AND tag = \"$tname\"");
        while ( ($res4 = mysqli_fetch_assoc($request4)) ) unlink( $res4["path"] );
        echo "succsess";
    }
    else echo array_shift($errors);
} else echo array_shift($errors);

?>
