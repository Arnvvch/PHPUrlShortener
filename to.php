<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URL Shortener</title>
    <link rel="stylesheet" href="include/style.css">
</head>
<body>
<?php
require_once 'include/db.php';
include_once 'include/config.php';

if(isset($_GET['id'])){
    $uid = $_GET['id'];

    if(!preg_match('|^[0-9a-zA-Z]{1,6}$|', $uid)){
        #die('That is not a valid short url');
    }

    $st = $db->prepare('SELECT * FROM "Shortener" WHERE ID = :id');
    $st->bindValue(':id', $uid);
    $ret = $st->execute();

    $ext_r = false;
    $ext_l = false;
    $int_r = false;
    $adult = false;

    while($row = $ret->fetchArray(SQLITE3_ASSOC)){
        $url = $row['URL'];
        $v = $row['VISITS'];
        $ext_r = $row['EXT_R'];
        $ext_l = $row['EXT_L'];
        $int_r = $row['INT_R'];
        $adult = $row['ADULT'];
        $v = $v + 1;
    }

    if($ext_r == 'true'){
        if($ext_l != false){
            $url = $ext_l . $url;
        }else{
            $url = $def_ext_l . $url;
        }
    }

    if(empty($url)){
        echo '<div class="alert-r"><image src="include/error.svg" style="color:red;">Could not find short URL.</div>';
        exit;
    }

    $st = $db->prepare('UPDATE "Shortener" SET VISITS = :v WHERE ID = :id');
    $st->bindValue(':v', $v);
    $st->bindValue(':id', $uid);
    $ret = $st->execute();

    //header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' .  $url);
    exit;
}
?>
</body>
</html>