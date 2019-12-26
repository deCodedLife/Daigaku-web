<?php

header('Content-Type: text/html; charset=UTF-8');
$errors  = array();
$date  = date("d/m/Y");
$token = $_GET["token"];
$array = explode ("*", base64_decode($token));
$login = $array[0];
$ttime = $array[2];
$uptag = $_GET["tag"];
$dates = $_GET["date"];
$udate = preg_split('//u', $dates,  NULL, PREG_SPLIT_NO_EMPTY);
$utag  = preg_split('//u', $uptag,  NULL, PREG_SPLIT_NO_EMPTY);

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

for ($i = 0; $i < count($utag); $i++)
{
    if ( !in_array($utag[$i], $symbols) )
    $errors[] = 'Имя предмета содержит недопустимые символы';
}
if ( isset( $dates ) ) {
  for ($i = 0; $i < count($udate); $i++)
  {
      if ( !in_array($udate[$i], $symbols) )
      $errors[] = 'Дата содержит недопустимые символы';
  }
}

if ( trim( $token ) == "" ) $errors[] = "Отсутствует токен";
if ( trim( $uptag ) == "" && !isset( $dates ) ) $errors[] = "Отсутствует предмет";
if ( trim( $dates ) == "" && !isset( $uptag ) ) $errors[] = "Отсутствует дата";
if ( $ttime != $date) $errors[] = "Токен устарел";

if ( empty($errors))
{
    $connect = mysqli_connect("localhost", "id10026645_administrator", "8895304025dr", "id10026645_school");
    $request = mysqli_query  ($connect, "select * FROM `users` WHERE name = \"$login\"");
    $res = mysqli_fetch_assoc($request);
    if ( $res["name"] != $login ) $errors[] = "Пользователь не существует!";
    $ctoken = base64_encode($res["name"]."*".$res["pass"]."*".$date);
    if ($ctoken != $token) $errors[] = "Удачи во взломе...";
    if (empty ($errors))
    {
        $group = $res["fgroup"];
        if ( isset($dates) ) $request = mysqli_query  ($connect, "select * FROM `Images` WHERE groups = \"$group\" AND date = \"$dates\";");
        else $request = mysqli_query  ($connect, "select * FROM `Images` WHERE groups = \"$group\" AND tag = \"$uptag\";");
        $obj->images = array();
        while ( ($res = mysqli_fetch_assoc($request)) )
        {
            $dobj->image = $res["path"];
            $dobj->date  = $res["date"];
            if ( isset( $dates ) ) $dobj->tag = $res["tag"];
            $obj->images[] = $dobj;
            $dobj = null;
        }
        echo json_encode($obj, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
    } else echo array_shift($errors);
} else echo array_shift($errors);

?>
