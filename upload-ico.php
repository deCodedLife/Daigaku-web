<?php
header('Content-Type: text/html; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
$errors = array();
$date  = date("d/m/Y");
$token = $_GET["token"];
$path  = $_GET["path"];
$apath = preg_split('//u', $path,  NULL, PREG_SPLIT_NO_EMPTY);
$local = "img/";
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

for ($i = 0; $i < count($apath); $i++)
{
    if ( !in_array($apath[$i], $symbols) )
    $errors[] = 'Имя пути содержит недопустимые символы';
}

$array = explode("*", base64_decode($token));
$login = $array[0];
$passw = $array[1];
$time  = $array[2];

if ( trim($path)  == "" ) $errors[] = "Вы не выбрали путь";
if ( $time != $date )     $errors[] = "Выш токен устарел.";
if ( count($path) > 30 )  $errors[] = "Имя картинки слишком большое";
if ($_FILES["image"]["size"] > 500120000) $errors[] = "Картинка слишком большая";
if ( !isset($_FILES["image"]) ) $errors[] = "Картинка не передана";

$name = $login . "." . $imageFileType;
$file = $local . $name;

if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "bmp" && $imageFileType != "ico" && $imageFileType != "gif" )
    $errors[] = "Недопустимый формат файла";

if ( empty($errors) )
{
    $connect = mysqli_connect("localhost", "id10026645_administrator", "8895304025dr", "id10026645_school");
    $request = mysqli_query ($connect, "select * FROM `users` WHERE name = \"$login\"");
    $res = mysqli_fetch_assoc($request);
    if ( $res["pass"] != $passw ) $errors[] =  "You are idiot hacker";
    $array = explode("/", $path);
    if (empty($errors))
    {
        if ( $res["profile"] != "" ) {
          $fname = explode( "http://work-backend.000webhostapp.com/school/", $res["profile"] );
          unlink( $fname[1] );
        }
        move_uploaded_file($_FILES["image"]['tmp_name'], $file);
        mysqli_query($connect, "update `users` SET `profile` = \"http://work-backend.000webhostapp.com/school/$file\" WHERE name = \"$login\" ");
        echo "succsess";
    }
    else echo array_shift($errors);
} else echo array_shift($errors);

?>
