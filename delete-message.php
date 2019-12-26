<?php
header('Content-Type: text/html; charset=utf-8');
$date  = date("d/m/Y");
$mess  = $_GET["message"];
$pick  = $_GET["pick"];
$dated = $_GET["date"];
$token = $_GET["token"];
$amess = preg_split('//u', $mess,   NULL, PREG_SPLIT_NO_EMPTY);
$adate = preg_split('//u', $dated,   NULL, PREG_SPLIT_NO_EMPTY);

$array = explode("*", base64_decode($token));
$login = $array[0];
$time  = $array[2];
$errors= array();

$symbols = array(
    'А','а','Б','б','В','в','Г','г','Д','д','Е','е','Ё','ё',
    'Ж','ж','З','з','И','и','Й','й','К','к','Л','л','М','м',
    'О','о','П','п','Р','р','С','с','Т','т','У','у','Ф','ф',
    'Х','х','Ц','ц','Ч','ч','Ш','ш','Щ','щ','ы','ъ','ь','Э',
    'э','Ю','ю','Я','я','_',' ','Н', ' ', 'н', ':',
    'A','a','B','b','C','c','D','d','E','e','F','f','G','g',
    'H','h','I','i','J','j','K','k','L','l','M','m','N','n',
    'O','o','P','p','Q','q','R','r','S','s','T','t','U','u',
    'V','v','W','w','X','x','Y','y','Z','z','!','@','$','%',
    '*','1','2','3','4','5','6','7','8','9','0','.','-','/'
);

if ( trim($token) == "" ) $errors[] = "Токен не указан!";
if ( $time != $date ) $errors[] = "Ваш токен устарел";

if ( trim($mess)  == "" ) $errors[] = "Вы должны указать сообщение!";
if ( trim($pick)  == "" ) $errors[] = "Отметка важности не указана";

for ($i = 0; $i < count($amess); $i++)
{
    if ( !in_array($amess[$i], $symbols) )
    $errors[] = 'Сообщение содержит недопустимые символы';
}
for ($i = 0; $i < count($adate); $i++)
{
    if ( !in_array($adate[$i], $symbols) )
    $errors[] = 'Дата содержит недопустимые символы';
}
if ($pick != 1 && $pick != 0) $errors[] = "Отметка важности передана неправильно";

if ( empty($errors) )
{
    $connect = mysqli_connect("localhost", "your_database_login", "your_database_password", "database_name");
    $request1 = mysqli_query  ($connect, "select * FROM `users` WHERE name = \"$login\";");
    $request2 = mysqli_query  ($connect, "select * FROM `groups` WHERE name = \"$gname\"");
    $res1 =mysqli_fetch_assoc($request1);
    $res2 =mysqli_fetch_assoc($request2);
    $token_c = base64_encode($res1["name"]."*".$res1["pass"]."*".$date);
    if ( $token != $token_c ) echo "Удачи во взломе, идиоты...";
    if ( $res1["status"] != "admin" && $res1["status"] != "updater" && $res1["status"] != "curator") $errors[] = "У вас нет прав на изменение!";
    if ( empty($errors) ) {
      $gname = $res1["fgroup"];
      mysqli_query($connect, "delete FROM `messages` WHERE groups = \"$gname\" AND message = \"$mess\" AND operator = \"$login\" AND pick = $pick AND date = \"$dated\"");
      echo "succsess";
    }
    else echo array_shift($errors);
} else echo array_shift($errors);

?>
