<?php

header("Content-type: text/html; charset=utf-8");
$date_ = date("d/m/Y");
$group = $_GET["group"];
$tag   = $_GET["tag"];
$task  = $_GET["task"];
$date  = $_GET["date_to"];
$token = $_GET["token"];
$ugroup= preg_split('//u', $group,  NULL, PREG_SPLIT_NO_EMPTY);
$utag  = preg_split('//u', $tag,    NULL, PREG_SPLIT_NO_EMPTY);
$udate = preg_split('//u', $date,   NULL, PREG_SPLIT_NO_EMPTY);
$utask = preg_split('//u', $task,   NULL, PREG_SPLIT_NO_EMPTY);
$array = explode( "*", base64_decode( $token ) );
$login = $array[0];
$passw = $array[1];
$udate = $array[2];
$errors = array();

$symbols = array(
    'А','а','Б','б','В','в','Г','г','Д','д','Е','е','Ё','ё',
    'Ж','ж','З','з','И','и','Й','й','К','к','Л','л','М','м',
    'О','о','П','п','Р','р','С','с','Т','т','У','у','Ф','ф',
    'Х','х','Ц','ц','Ч','ч','Ш','ш','Щ','щ','ы','ъ','ь','Э',
    'э','Ю','ю','Я','я','_','Н', ' ', 'н', '?', '!',
    'A','a','B','b','C','c','D','d','E','e','F','f','G','g',
    'H','h','I','i','J','j','K','k','L','l','M','m','N','n',
    'O','o','P','p','Q','q','R','r','S','s','T','t','U','u',
    'V','v','W','w','X','x','Y','y','Z','z','!','@','$','%',
    '*','1','2','3','4','5','6','7','8','9','0','.','-','/'
);

for ($i = 0; $i < count($ugroup); $i++)
{
    if ( !in_array($ugroup[$i], $symbols) )
    $errors[] = 'Название группы содержит недопустимые символы';
}
for ($i = 0; $i < count($utag); $i++)
{
    if ( !in_array($utag[$i], $symbols) )
    $errors[] = 'Имя предмета содержит недопустимые символы';
}
for ($i = 0; $i < count($udate); $i++)
{
    if ( !in_array($udate[$i], $symbols) )
    $errors[] = 'Дата содержит недопустимые символы';
}
for ($i = 0; $i < count($utask); $i++)
{
    if ( !in_array($utask[$i], $symbols) )
    $errors[] = 'Задание содержит недопустимые символы';
}

if ( trim( $group ) == "" ) $errors[] = "Отсутствует группа";
if ( trim( $token ) == "" ) $errors[] = "Отсутствует токен";
if ( trim( $task )  == "" ) $errors[] = "Отсутствует задание";
if ( trim( $tag )  == "" && !isset( $_GET["date_to"] ) ) $errors[] = "Отсутствует предмет";
if ( trim( $date ) == "" && !isset( $_GET["tag"] ) )     $errors[] = "Отсутствует дата";
if ( $udate != $date_) $errors[] = "Токен устарел";

if ( empty($errors))
{
    $connect = mysqli_connect("localhost", "your_database_login", "your_database_password", "database_name");
    $request = mysqli_query  ($connect, "select * FROM `users` WHERE name = \"$login\"");
    $res = mysqli_fetch_assoc($request);
    $ctoken = base64_encode($res["name"]."*".$res["pass"]."*".$date_);
    if ( $res["name"] != $login ) $errors[] = "Пользователь не существует!";
    if ($ctoken != $token) $errors[] = "Удачи во взломе...";
    if ($res["status"] != "curator") $errors[] = "У вас нет прав на выполнение этого";
    if (empty ($errors)) {
      //$group = $res["fgroup"];
      if ( trim( $tag ) == "" ) $tag = $res["curatorTag"];
      mysqli_query ($connect, "insert INTO `Tasks` (`groups`,`tag`,`task`,`attached`,`date_to`,`operator`,`finished`) VALUES (\"$group\",\"$tag\",\"$task\",\"false\",\"$date\",\"$login\",0)");
      echo "succsess";
    } else echo array_shift($errors);
} else echo array_shift($errors);

?>
