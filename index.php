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

        .tr {
            color: rgb(75, 75, 75);
        }
          
        tr:nth-child(odd) {
            background-color: rgb(75, 75, 75);
            color: white;
        }

        .container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 80vh;
        }

        .item {
            margin: 1rem;
            height: 1.8rem;
            width: 60vw;
            border-radius: 16px;
            border: none;
            color: rgb(75, 75, 75);
            box-shadow: 0 0 5px white;
            font-size: large;
        }

        .alert-r { 
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

        .alert-g { 
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
include_once 'include/db.php';
include_once 'include/config.php';

$self = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

function generateRandomString($length) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

if(isset($_POST['pwd'])){
    $nl = generateRandomString($length);
    if(password_verify($_POST['pwd'], $hash)){
        if(isset($_POST['ads'])){
            $ads = "true";
        }else{
            $ads = "false";
        }

        if(!empty($_POST['url'])){
            $statement = $db->prepare('INSERT INTO "Shortener" (ID, URL, VISITS, ADS) VALUES (:uid, :url, "0", :ads)');
            $statement->bindValue(':uid', $nl);
            $statement->bindValue(':url', $_POST['url']);
            $statement->bindValue(':ads', $ads);
            $statement->execute();
            echo '<div class="alert-g">Sucsess, your short link is: [' . $self . $nl . '] .</div>';
        }else{
            $st = $db->prepare('SELECT * FROM "Shortener"');
            $ret = $st->execute();
        
            echo '
            <table>
                <tr>
                    <th>ID</th>
                    <th>URL</th>
                    <th>VISITS</th>
                    <th>ADS</th>
                </tr>
            ';
            while($row = $ret->fetchArray(SQLITE3_ASSOC)){
                echo '<tr>';
                echo '<td>' . $row['ID'] . '</td>';
                echo '<td>' . $row['URL'] . '</td>';
                echo '<td>' . $row['VISITS'] . '</td>';
                echo '<td>' . $row['ADS'] . '</td>';
                echo '</tr>';
            }

            echo '
            </table>
            ';
        }
    }else{
        echo '<div class="alert-r">Wrong password.</div>';
    }
}else{
    echo '<div class="alert-r">No Password enterd.</div>';
}
?>
    <form action="" method="post" class="container">
        <input type="text" name="url" class="item" placeholder=" URL">
        <input type="password" name="pwd" class="item" placeholder=" Password">
        <h3>Enable ads:</h3><input type="checkbox" name="ads" class="item">
        <input type="submit" value="Submit" class="item">
    </form>
<?php
    echo $links;
?>
</body>
</html>