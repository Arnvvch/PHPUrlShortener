<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Link Shortener</title>
    <link rel="stylesheet" href="include/style.css">
    <link rel="shortcut icon" href="include/logo.svg" type="image/x-icon">
</head>
<body>
    <header>
        <div class="box">
            <a href=""><img class="logo" src="include/logo.svg" alt=""><h2>Link Shortener</h2></a>
            <div class="github">
                <a href="https://github.com/ArnchON/PHPUrlShortener"><img class="logo" src="include/github.svg" alt="">Project on Github</a>
            </div>
                
        </div>
    </header>
<?php
include_once 'include/db.php';
include_once 'include/config.php';

session_start();

if(isset($_GET['a'])){
    if($_GET['a'] == 'logout'){
        session_destroy();
        echo '<a href="?"><button>Log Back In</button></a>';
        exit;
    }
}

if(empty($_SESSION["auth"])){
    $inputpwd = false;
    if(isset($_POST['pwd'])){
        $inputpwd = $_POST['pwd'];
        if(password_verify($inputpwd, $hash)){
            $_SESSION["auth"] = true;
        }else{
            if(strlen($inputpwd) <= 0){
                echo '<div class="alert-r"><img src="include/error.svg" style="color:red;">No Password enterd. Enter a new password to get a new hash. You can then change it in config.php.</div>';
            }else{
                $newhash = password_hash($_POST['pwd'], PASSWORD_DEFAULT);
                echo '<div class="alert-r"><img src="include/error.svg" style="color:red;">Wrong password. Change hash to "' . $newhash . '" in config.php to change your password to the password you entered if you forgot your password!</div>';    
            }
        }
    }
}

$auth = false;

if(isset($_SESSION["auth"])){
    $auth = $_SESSION["auth"];
}

if($auth == true){
    echo '

<div class="logout">
    <a href="?a=logout">
        <button>Log Out</button>
    <a>
</div>

';

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


    if(isset($_POST['cid']) && isset($_POST['cid_e']) && !empty($_POST['cid'])){
        $nl = $_POST['cid'];
    }else{
        $nl = generateRandomString($length);
    }

    if(isset($_POST['adult'])){
        $adult = "true";
    }else{
        $adult = "false";
    }

    if(isset($_POST['int_r'])){
        $int_r = "true";
    }else{
        $int_r = "false";
    }

    if(isset($_POST['ext_r'])){
        $ext_r = "true";
    }else{
        $ext_r = "false";
    }

    if(!empty($_POST['url'])){
        $statement = $db->prepare('INSERT INTO "Shortener" (ID, URL, VISITS, ADULT, EXT_R, EXT_L, INT_R) VALUES (:uid, :url, "0", :adult, :ext_r, :ext_l, :int_r)');
        $statement->bindValue(':uid', $nl);
        $statement->bindValue(':url', $_POST['url']);
        $statement->bindValue(':adult', $adult);
        $statement->bindValue(':ext_r', $ext_r);
        $statement->bindValue(':ext_l', $_POST['ext_l']);
        $statement->bindValue(':int_r', $ext_r);
        $statement->execute();
        echo '<div class="alert-g">Sucsess, your short link is: [' . $self . 'to.php?url=' . $nl . '] .</div>';
    }

    if(isset($_GET['a'])){
        if($_GET['a'] == 'delete'){
            if(isset($_GET['delid'])){
                $statement = $db->prepare('DELETE FROM "Shortener" WHERE id= :delid');
                $statement->bindValue(':delid', $_GET['delid']);
                $statement->execute();
            }
        }
    }
 
    echo '

<form action="" method="post" class="container">
    <h1>Create New Short Link</h1>
            
    <div class="row">
        <h3 style="min-width:350px;margin-right:80px;">Link to Shorten</h3>
        <input type="text" name="url" class="item" placeholder=" Type URL Here">
    </div>
    <div class="row">
        <h3 style="min-width:350px;">Enable Custom Link</h3>
        <input type="checkbox" name="cid_e" class="item" style="min-width:50px;max-width:50px;">
        <input type="text" name="cid" value="' . generateRandomString($length) . '" class="item" placeholder="..... Custom Short Link .....">
    </div>
    </div>
    <div class="row">
        <h3 style="min-width:350px;">Enable Adult Ads & Warning</h3>
        <input type="checkbox" name="adult" class="item" style="min-width:50px;max-width:50px;">
    </div>
    <div class="row">
        <h3 style="min-width:350px;">Enable External Redirect Ad</h3>
        <input type="checkbox" name="ext_r" class="item" style="min-width:50px;max-width:50px;">
        <input type="text" name="ext_l" value="' . $def_ext_l . '" class="item" placeholder="..... External Redirect Link .....">
    </div>
    <div class="row">
        <h3 style="min-width:350px;">Enable Internal Redirect Ad</h3>
        <input type="checkbox" name="int_r" class="item" style="min-width:50px;max-width:50px;"> (COMING SOON...)
    </div>
    <input type="submit" value="Submit" class="item">
</form>

';


        
    $st = $db->prepare('SELECT * FROM "Shortener"');
    $ret = $st->execute();
    
    echo '

<div class="container">
    <table>
        <tr>
            <th>URL</th>
            <th>ID</th>
            <th>URL</th>
            <th>VISITS</th>
            <th>AD INFO</th>
            <th>DELETE</th>
        </tr>
        <tr>
';
    while($row = $ret->fetchArray(SQLITE3_ASSOC)){

        echo '

            <td>
                <input type="text" value="' . $self . $row['ID'] . '" id="' . $row['ID'] . '" style="display:none;">

                <div class="tooltip">
                    <button onclick="inCopy(\'' . $row['ID'] . '\')" onmouseout="outCopy(\'' . $row['ID'] . '\')" style="width:100px;">
                        <span class="tooltiptext" id="' . $row['ID'] . 'TT">Copy to clipboard</span>
                        Copy
                    </button>
                </div>
            </td>
        
';

            echo '
            <td>' . $row['ID'] . '</td>
';

            echo '
            <td>' . $row['URL'] . '</td>
';

            echo '
            <td>' . $row['VISITS'] . '</td>
';

            echo '
            <td>Adult ads and warning : ' . $row['ADULT']  . ' | External redirect : ' . $row['EXT_R'] . ' | External redirect link : ' . $row['EXT_L'] . ' | Internal redirect : ' . $row['INT_R']. '</td>
';

echo '

            <td>
                <a href="?a=delete&delid=' . $row['ID'] . '"><button style="width:120px;">Delete</button></a>
            </td>

';

            echo '
        </tr>
';

    }

        echo '
    </table>
</div>

';

}else{
    echo '

<h1>LOGIN</h1>
<form action="" method="post" class="container">
    <input type="password" name="pwd" class="item" placeholder="..... Password .....">
    <input type="submit" value="Login" class="item">
</form>
    
';
    
}

    ?>

    <script>
        function inCopy(urlid) {
            var copyText = document.getElementById(urlid);
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(copyText.value);
            

            var tooltip = document.getElementById(`${urlid}TT`);
            tooltip.innerHTML = `Copied:  ${copyText.value}`;
        }

        function outCopy(urlid) {
            var tooltip = document.getElementById(`${urlid}TT`);
            tooltip.innerHTML = `Copy to clipboard`;
        }
    </script>

</body>
</html>