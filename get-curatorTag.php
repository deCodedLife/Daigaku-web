<?php
$sdate  = date("d/m/Y");
$uptag = $_GET["tag"];
$token = $_GET["token"];
$sltag = preg_split('//u', $uptag,  NULL, PREG_SPLIT_NO_EMPTY);
$array = explode("*", base64_decode( $token ) );
$login = $array[0];
$passw = $array[1];
$udate = $array[2];

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
if ( $sdate != $udate ) $errors[] = "Ваш токен устарел";
for ($i = 0; $i < count($sltag); $i++)
{
    if ( !in_array($sltag[$i], $symbols) )
    $errors[] = 'Имя предмета содержит недопустимые символы';
}

if ( empty( $errors ) ) {
    $connect = mysqli_connect("localhost", "your_database_login", "your_database_password", "database_name");
    $request = mysqli_query  ($connect, "select * FROM `users` WHERE name = \"$login\"");
    $res = mysqli_fetch_assoc($request);
    if ( $passw != $res["pass"] )    $errors[] = "きみはばかです Не пытайтесь взломать систему";
    if ( $res["status"] != "admin" ) $errors[] = "У вас недостаточно прав для этого";
    if ( empty( $errors ) ) {
        $request = mysqli_query ($connect, "select name FROM `users` WHERE curatorTag = \"$uptag\"");
        $res= mysqli_fetch_assoc($request);
        echo $res["name"];
    } else echo array_shift( $errors );
} else echo array_shift( $errors );
?>
