<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URL Shortener</title>
    <style>
        body{
            background-color: lightgrey;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }
          
        th, td {
            text-align: left;
            padding: 8px;
        }
          
        tr:nth-child(odd) {
            background-color: Lightgreen;
        }

        .container{
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 80vh;
        }

        .item{
            margin: 1rem;
            height: 1.8rem;
            width: 60vw;
            border-radius: 16px;
            border: none;
            color: rgb(75, 75, 75);
            box-shadow: 0 0 5px white;
            font-size: large;
        }

        .alert-r{ 
            padding: 1rem;
            color: red;
            font-weight: bold;
            font-size: large;
            border-radius: 8px;
            border: red 4px solid;
            background-color: white;
            margin: 1rem;
            box-shadow:  0 0 5px red;
        }

        .alert-g{ 
            padding: 1rem;
            color:green;
            font-weight: bold;
            font-size: large;
            border-radius: 8px;
            border: green 4px solid;
            background-color: white;
            margin: 1rem;
            box-shadow:  0 0 5px green;
        }
    </style>
</head>
<body>
<?php
require_once 'include/db.php';
include_once 'include/config.php';

if(isset($_GET['url'])){
    $uid = $_GET['url'];

    if(!preg_match('|^[0-9a-zA-Z]{1,6}$|', $uid)){
        die('That is not a valid short url');
    }

    $st = $db->prepare('SELECT * FROM "Shortener" WHERE ID = :id');
    $st->bindValue(':id', $uid);
    $ret = $st->execute();

    while($row = $ret->fetchArray(SQLITE3_ASSOC)){
        $url = $row['URL'];
        $v = $row['VISITS'];
        $ads = $row['ADS'];
        $v = $v + 1;
    }

    if($ads == 'true'){
        $url = $ad . $url;
    }

    if(empty($url)){
        echo '<div class="alert-r">Could not find short URL.</div>';
        exit;
    }

    $st = $db->prepare('UPDATE "Shortener" SET VISITS = :v WHERE ID = :id');
    $st->bindValue(':v', $v);
    $st->bindValue(':id', $uid);
    $ret = $st->execute();

    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' .  $url);
    exit;
}
?>
</body>
</html>