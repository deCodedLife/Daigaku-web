<?php
header('Content-Type: text/html; charset=UTF-8');
$connect = mysqli_connect("localhost", "id10026645_administrator", "8895304025dr", "id10026645_school");
$errors  = array();

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

if ( isset($_GET["messages"]) && isset($_GET["token"]))
{
    $date  = date("d/m/Y");
    $token = $_GET["token"];
    $array = explode( "*", base64_decode($_GET["token"]) );
    $login = $array[0];
    $passw = $array[1];
    $ttime = $array[2];
    if ( $ttime != $date) $errors[] = "Ваш токен устарел";
    if ( trim ($token) == "")  $errors[] = "Токен не может быть пустым!";
    if ( empty($errors) )
    {
        $request = mysqli_query  ($connect, "select * FROM `users` WHERE name = \"$login\"");
        $res = mysqli_fetch_assoc($request);
        if ( $passw != $res["pass"] ) $errors[] = "きみはばかです Не пытайтесь взломать систему";
        if ( empty( $errors ) ) {
            $group = $res["fgroup"];
            $obj->messages = array();
            $request = mysqli_query  ($connect, "select * FROM `messages` WHERE `groups` = \"$group\" ");
            while ( ($res = mysqli_fetch_assoc($request)) )
            {
                $objs->message = $res["message"];
                $objs->pick = $res["pick"];
                $objs->operator = $res["operator"];
                $objs->date = $res["date"];
                $obj->messages[] = $objs;
                $objs = null;
            }
        }
    }
} else if (isset($_GET["tags"]) && isset($_GET["token"]))
{
    $date  = date("d/m/Y");
    $token = $_GET["token"];
    $group = $_GET["group"];
    $check = preg_split('//u', $group,  NULL, PREG_SPLIT_NO_EMPTY);
    $array = explode( "*", base64_decode($_GET["token"]) );
    $login = $array[0];
    $passw = $array[1];
    $ttime = $array[2];
    if ( isset( $_GET["group"] ) ) {
        for ($i = 0; $i < count($agroup); $i++)
        {
            if ( !in_array($agroup[$i], $symbols) )
            $errors[] = 'Имя группы содержит недопустимые символы';
        }
    }
    if ( $ttime != $date) $errors[] = "Ваш токен устарел";
    if ( trim ($token) == "")  $errors[] = "Токен не может быть пустым!";
    if ( empty($errors) )
    {
        $request = mysqli_query  ($connect, "select * FROM `users` WHERE name = \"$login\"");
        $res = mysqli_fetch_assoc($request);
        if ( $passw != $res["pass"] ) $errors[] = "きみはばかです Не пытайтесь взломать систему";
        if ( empty( $errors ) ) {
            if ( !isset( $_GET["group"] ) ) $group = $res["fgroup"];
            $obj->tags   = array();
            if ( isset($_GET["all"]) ) {
                if ( $res["status"] != "admin" ) $errors[] = "У вас нет прав на выполнение этого";
                $request = mysqli_query ($connect, "select * FROM `tags`");
            }
            else
                $request = mysqli_query  ($connect, "select * FROM `tags` WHERE groups = \"$group\"");

            while ( ($res = mysqli_fetch_assoc($request)) ) {
                $nobj->tag = $res["tag"];
                $nobj->static = $res["static"];
                $obj->tags[] = $nobj;
                $nobj = null;
            }
        }
    }
} else if ( isset( $_GET["groups"] ) ) {
    $obj->groups = array();
    $request = mysqli_query ( $connect, "select * FROM `groups`" );
    while ( ( $res = mysqli_fetch_assoc( $request ) ) ) {
        $dobj->group    = $res["name"];
        $dobj->operator = $res["operator"];
        $dobj->curator  = $res["curator"];
        $obj->groups[]  = $dobj;
        $dobj = null;
    }

} else if ( isset($_GET["profile"]) )
{
    $date  = date("d/m/Y");
    $token = $_GET["token"];
    $array = explode( "*", base64_decode($_GET["token"]) );
    $login = $array[0];
    $passw = $array[1];
    $ttime = $array[2];
    if ( $ttime != $date) $errors[] = "Ваш токен устарел";
    if ( trim ($token) == "")  $errors[] = "Токен не может быть пустым!";
    if ( empty($errors) )
    {
        $request = mysqli_query  ($connect, "select * FROM `users` WHERE name = \"$login\"");
        $checked = false;
        while ( ($res = mysqli_fetch_assoc($request)) )
        {
            $utoken = base64_encode($res["name"]."*".$res["pass"]."*".$date);
            if ( $token != $utoken ) $errors[] = "Неверный токен!";
            else
            {
                if ( $_GET["profile"] != 1 ) {
                    if ( $res["status"] != "admin" ) $errors[] = "Не имеете прав доступа";
                    else {
                        $request = mysqli_query($connect, "select * FROM `users` WHERE name = \"".$_GET["profile"]."\"");
                        $res = mysqli_fetch_assoc( $request );
                    }
                }
                if ( empty( $errors ) ) {
                    $checked = true;
                    $obj->name  = $res["name"];
                    $obj->status= $res["status"];
                    $obj->group = $res["fgroup"];
                    $obj->profile=$res["profile"];
                    $obj->ia_curator = $res["is_curator"];
                    $obj->curatorTag = $res["curatorTag"];
                }
            }
        }
    }
} else if ( isset($_GET["users"]) ) {
    $obj->users = array();
    if ( isset( $_GET["group"] ) ) {
      $group = $_GET["group"];
      $group = " fgroup = \"$group\" AND ";
    } else $group = "";
    if ( isset( $_GET["s"] ) )      $request = mysqli_query ($connect, "select * FROM `users` where " .$group. " (status = \"student\" OR status = \"updater\")");
    else if ( isset( $_GET["c"] ) ) $request = mysqli_query ($connect, "select * FROM `users` where status = \"curator\"");
    else if ( isset ($_GET["a"] ) ) $request = mysqli_query ($connect, "select * FROM `users` where status = \"admin\"");
    while ( ($res = mysqli_fetch_assoc($request)) ) $obj->users[] = $res["name"];
} else if ( isset( $_GET["currentUser"] ) && isset($_GET["token"] ) )
{
    $currentUser = $_GET["currentUser"];
    $token = $_GET["token"];
    $array = explode( "*", base64_decode( $token ) );
    $login = $array[0];
    $passw = $array[1];
    $utime = $array[2];
    $date  = date("d/m/Y");
    if ( $utime != $date ) $errors[] = "Ваш токен устарел";
    if ( trim ( $token ) == "")  $errors[] = "Токен не может быть пустым!";
    if ( empty( $errors ) )
    {
      $request = mysqli_query  ($connect, "select * FROM `users` WHERE name = \"$login\"");
      $res = mysqli_fetch_assoc($request);
      if ( $passw != $res["pass"] ) $errors[] = "きみはばかです Не пытайтесь взломать систему";
      if ( empty( $errors ) ) {
        $request = mysqli_query( $connect, "select * FROM `users` where name = \"$currentUser\" " );
        $resource = mysqli_fetch_assoc( $request );
        $obj->username = $resource["name"];
        $obj->profile  = $resource["profile"];
        //$obj->is_blocked = $resource["blockList"];
      }
    }
} else if ( isset($_GET["tasks"]) && isset($_GET["token"]) ) {
    $token = $_GET["token"];
    $date_ = date("d/m/Y");
    $array = explode( "*", base64_decode( $token ) );
    $login = $array[0];
    $passw = $array[1];
    $utime = $array[2];
    if ( $utime != $date_ ) $errors[] = "Ваш токен устарел";
    if ( trim ( $token ) == "")  $errors[] = "Токен не может быть пустым!";
    if ( empty( $errors ) )
    {
      $request = mysqli_query  ($connect, "select * FROM `users` WHERE name = \"$login\"");
      $res = mysqli_fetch_assoc($request);
      if ( $passw != $res["pass"] ) $errors[] = "きみはばかです Не пытайтесь взломать систему";
      if ( empty( $errors ) ) {
          if ( isset( $_GET["group"] ) ) $group = $_GET["group"];
          else $group = $res["fgroup"];
          $obj->tasks = array();
          if ( isset( $_GET["self"] ) ) $request = mysqli_query($connect, "select * FROM `Tasks` WHERE groups = \"$group\" AND operator = \"$login\" " );
          else $request = mysqli_query  ($connect, "select * FROM `Tasks` WHERE groups = \"$group\"");
          while ( ($res = mysqli_fetch_assoc($request)) ) {
              $objs->group = $res["groups"];
              $objs->tag   = $res["tag"];
              $objs->task  = $res["task"];
              $objs->attached = $res["attached"];
              $objs->date_to  = $res["date_to"];
              $objs->operator = $res["operator"];
              $objs->finished = $res["finished"];
              $objs->id = $res["id"];
              $obj->tasks[] = $objs;
              $objs = null;
          }
      }
    }
}else $errors[] = "Ничего не указано!";

if ( empty($errors) ) echo json_encode($obj, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
else echo array_shift($errors);

?>
