<?php
header('Content-Type: text/html; charset=utf-8');
$sdate  = date("d/m/Y");
$passw = "";
$group = $_GET["group"];
$name  = $_GET["name"];
$status= $_GET["status"];
$token = $_GET["token"];

$array = explode("*", base64_decode($token));
$username = $array[0];
$password = $array[1];
$udate = $array[2];
$subStat = 0;

if ( $status == "curator" ) $subStat = 1;

$agroup= preg_split('//u', $group, NULL, PREG_SPLIT_NO_EMPTY);
$aname = preg_split('//u', $name,  NULL, PREG_SPLIT_NO_EMPTY);
$astatus=preg_split('//u', $status,NULL, PREG_SPLIT_NO_EMPTY);
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

if ( trim( $token ) == "" ) $errors[] = "Токен не указан";
if ( trim( $name )  == "" ) $errors[] = "Вы должны ввести  имя!";
if ( trim( $group ) == "" ) $errors[] = "Не указана группа";
if ( trim( $status )== "" ) $errors[] = "Вы должны указать статус";
if ( count($aname) < 4 ) $errors[] = "Имя должно быть не менее 4 символов";
if ( $sdate != $udate )  $errors[] = "Ваш токен устарел";

for ($i = 0; $i < count($agroup); $i++)
{
    if ( !in_array($agroup[$i], $symbols) )
    $errors[] = 'Имя группы содержит недопустимые символы';
}
for ($i = 0; $i < count($aname); $i++)
{
    if ( !in_array($aname[$i], $symbols) )
    $errors[] = 'Имя содержит недопустимые символы';
}
for ($i = 0; $i < count($astatus); $i++)
{
    if ( !in_array($astatus[$i], $symbols) )
    $errors[] = 'Статус содержит недопустимые символы';
}

for ( $i = 0; $i < 15; $i++ )
{
    $rand_ = rand(0, count($symbols));
    $passw = $passw.$symbols[$rand_];
}

if (empty($errors))
{
    $connect = mysqli_connect("localhost", "your_database_login", "your_database_password", "database_name");
    $request = mysqli_query  ($connect, "select * FROM `users` WHERE name = \"$username\"");
    $res = mysqli_fetch_assoc($request);
    if ( $password != $res["pass"] ) $errors[] = "きみはばかです Не пытайтесь взломать систему";
    if ( $res["status"] != "admin" ) $errors[] = "У вас недостаточно прав для этого";
    if ( empty( $errors ) ) {
        $request = mysqli_query  ($connect, "select * FROM `users` WHERE name = \"$name\"");
        if ($res["name"]  == $name ) $errors[] = "Пользователь уже зарегистрированы!";
        if ( empty($errors) )
        {
            $sql = "insert INTO `users` ( `pass`, `name`, `status`, `fgroup`, `is_curator`, `profile`, `curatorTag`) VALUES
            (
            \"".hash("sha256", $passw)."\",
            \"$name\",
            \"$status\",
            \"$group\",
            $subStat,
            \"\",
            \"\" ); ";
            mysqli_query($connect, $sql);
            echo $passw;
        } else echo array_shift($errors);
    } else echo array_shift($errors);
} else echo array_shift($errors);

?>
