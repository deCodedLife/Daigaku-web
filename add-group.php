<?php
header("Content-type: text/html; charset=utf-8");
$date  = date("d/m/Y");
$gname = $_GET["groupname"];
$oname = $_GET["operator"];
$token = $_GET["token"];
$aname = preg_split('//u', $oname, NULL, PREG_SPLIT_NO_EMPTY);
$agname= preg_split('//u', $gname, NULL, PREG_SPLIT_NO_EMPTY);

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

if ( trim($gname) == "" ) $errors[] = "Не указано имя группы";
if ( trim($oname) == "" ) $errors[] = "Не указан староста";
if ( trim($token) == "" ) $errors[] = "Нет токена (как так то?)";

$array = explode("*", base64_decode($token));
$login = $array[0];
$time  = $array[2];
$errors= array();

if ( trim($token) == "" ) $errors[] = "Токен не задан!";
if ( $time != $date) $errors[] = "Токен устарел";

for ($i = 0; $i < count($agname); $i++)
{
    if ( !in_array($agname[$i], $symbols) )
    $errors[] = 'Имя группы содержит недопустимые символы';
}
for ($i = 0; $i < count($aname); $i++)
{
    if ( !in_array($aname[$i], $symbols) )
    $errors[] = 'Имя старосты содержит недопустимые символы';
}


if ( empty($errors) )
{
    $connect = mysqli_connect("localhost", "id10026645_administrator", "8895304025dr", "id10026645_school");
    $request1 = mysqli_query ($connect, "select * FROM `users` WHERE name = \"$login\";");
    $request2 = mysqli_query ($connect, "select * FROM `users` WHERE name = \"$oname\";");
    $request3 = mysqli_query ($connect, "select * FROM `groups` WHERE name = \"$gname\"");

    $res1 = mysqli_fetch_assoc($request1);
    $res2 = mysqli_fetch_assoc($request2);
    $res3 = mysqli_fetch_assoc($request3);
    $token_c = base64_encode($res1["name"]."*".$res1["pass"]."*".$date);
    if ($token != $token_c) $errors[] = "~~LALALA You idiot hacker";
    if ($oname != $res2["name"]) $errors[] = "Имени старосты нет в списке польхователей";
    if ($res1["status"] != "curator" && $res1["status"] != "admin") $errors[] = "У вас нет прав на выполнение этого";
    if ($gname == $res3["name"]) $errors[] = "Группа с таким именем уже существует";
    if (empty($errors)) {mysqli_query($connect, "insert INTO `groups` (`name`, `operator`, `curator`) VALUES (\"$gname\", \"$oname\", \"".$login."\")"); echo "succsess";}
    else echo array_shift($errors);
} else echo array_shift($errors);

?>
