<?php
header('Content-Type: text/html; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
$errors = array();
$date  = date("d/m/Y");
$token = $_GET["token"];
$tag   = $_GET["tag"];
$path  = $_GET["path"];
$atag  = preg_split('//u', $tag,   NULL, PREG_SPLIT_NO_EMPTY);
$apath = preg_split('//u', $path,  NULL, PREG_SPLIT_NO_EMPTY);
$local = "img/" . $group . "/";
$file  = $local . basename($_FILES["image"]["name"]);
$imageFileType =  strtolower(pathinfo($file, PATHINFO_EXTENSION));

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

for ($i = 0; $i < count($atag); $i++)
{
    if ( !in_array($atag[$i], $symbols) )
    $errors[] = 'Имя предмета содержит недопустимые символы';
}
for ($i = 0; $i < count($apath); $i++)
{
    if ( !in_array($apath[$i], $symbols) )
    $errors[] = 'Имя пути содержит недопустимые символы';
}

$array = explode("*", base64_decode($token));
$login = $array[0];
$time  = $array[2];

if ( trim($tag)   == "" ) $errors[] = "Вы должны ввести предмет";
if ( trim($path)  == "" ) $errors[] = "Вы не выбрали путь";
if ( $time != $date )     $errors[] = "Выш токен устарел.";
if ( count($path) > 30 )  $errors[] = "Имя картинки слишком большое";
if ($_FILES["image"]["size"] > 500120000) $errors[] = "Картинка слишком большая";
if ( !isset($_FILES["image"]) ) $errors[] = "Картинка не передана";
while (file_exists($file) != false)
{
    $name = "";
    for ( $i = 0; $i < 10; $i++ )
    {
        $rand = rand(0, count($symbols));
        $name = $symbols[$rand] . "." . $imageFileType;
    }
    $file = $local . $name;
}

if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "bmp" && $imageFileType != "ico" && $imageFileType != "gif" )
    $errors[] = "Недопустимый формат файла";

if ( empty($errors) )
{
    $connect = mysqli_connect("localhost", "your_database_login", "your_database_password", "database_name");
    $request1 = mysqli_query ($connect, "select * FROM `users` WHERE name = \"$login\";");
    $request2 = mysqli_query ($connect, "select * FROM `groups` WHERE name = \"$group\"");
    $request3 = mysqli_query ($connect, "select * FROM `tags` WHERE tag = \"$tag\"");
    $res1 = mysqli_fetch_assoc($request1);
    $res2 = mysqli_fetch_assoc($request2);
    $res3 = mysqli_fetch_assoc($request3);
    $token_c = base64_encode($res1["name"]."*".$res1["pass"]."*".$date);
    if ($token != $token_c) $errors[] = "You are idiot hacker";
    if ($res1["status"] != "updater" && $res1["status"] != "admin" && $res1["status"] != "updater") $errors[] = "Вы не являетесь старостой";
    if ($res2["name"] != $group) $errors[] = "Такой группы не существует";
    if ($res3["tag"] != $tag) $errors[] = "Такого предмета не существует";
    $array = explode("/", $path);
    if (empty($errors))
    {
        $group = $res1["fgroup"];
        move_uploaded_file($_FILES["image"]['tmp_name'], $file);
        mysqli_query($connect, "INSERT INTO `Images` (`groups`, `tag`, `date`, `path`) VALUES (\"$group\", \"$tag\",\"".date("Y-m-d")."\",\"$file\")");
        echo "succsess";
    }
    else echo array_shift($errors);
} else echo array_shift($errors);

?>
