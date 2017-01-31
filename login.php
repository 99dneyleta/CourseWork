<?php
session_start();
if (isset($_SESSION['user']) || isset($_COOKIE['user'])) {
    header("Location: user.php");
}

if (isset($_POST['username'])) {

    include_once("dbConnect.php");
    include_once("functionality.php");

    // Set the posted data from the form into local variables
    $usname = strip_tags($_POST['username']);
    $paswd = strip_tags($_POST['password']);
    // escape variables for security
    $usname = mysqli_real_escape_string($dbCon, $usname);
    $paswd = mysqli_real_escape_string($dbCon, $paswd);


    $sql = "SELECT id, username, password, first_name, last_name, email, image FROM members WHERE username = '$usname' AND activated = '1' LIMIT 1";
    $query = mysqli_query($dbCon, $sql);
    $row = mysqli_fetch_row($query);

    $uid = $row[0];
    $dbUsname = $row[1];
    $dbPassword = $row[2];
    $fn = $row[3];
    $ln = $row[4];
    $em = $row[5];
    $im = $row[6];
    // Check if the username and the password they entered was correct
    if ($usname == $dbUsname && password_verify($paswd,$dbPassword)) {

        $user = new User($usname, $uid, $fn, $ln, $em, $im);
        generateSessionAndCookie($user);
        // Now direct to users feed
        header("Location: user.php");
    } else {
        echo "<h2>Ops that username or password combination was incorrect.
		<br /> Please try again.</h2>";
    }

}
?>
<!--
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Basic login system</title>
    <style type="text/css">
        html {
            font-family: Verdana, Geneva, sans-serif;
        }
        h1 {
            font-size: 24px;
            text-align: center;
        }
        #wrapper {
            position: absolute;
            width: 100%;
            top: 30%;
            margin-top: -50px;/* half of #content height*/
        }
        #form {
            margin: auto;
            width: 200px;
            height: 100px;
        }
    </style>
</head>

<body>
<div id="wrapper">
    <h1>Simple PHP Login</h1>
    <form id="form" action="login.php" method="post" enctype="multipart/form-data">
        Username: <input type="text" name="username" /> <br />
        Password: <input type="password" name="password" /> <br />
        <input type="submit" value="Login" name="Submit" />
    </form>
    <a href="signup.php"> Sign UP </a>
</body>
</html>
-->
<!Doctype html>
<html>
<head>
    <title>LogIn</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="/styles.css">
    <style>
        ::-webkit-input-placeholder { /* WebKit browsers input color*/
            color:    white;

        }

    </style>
</head>
<body style="position: relative; min-height: 100%; top: 0px;">
<div class = "logo-row" style="margin-top: 25px;">
    <div class = "logo-position" style="margin: 200px 0 150px 0;">
        <a class = "logo"> <img src="/img/logo.png" alt="Logo">
        </a>
    </div>
</div>

<form action="login.php" name="signIn" method="post" enctype="multipart/form-data">
    <div style="margin-bottom: 5vh">
        <input class="input-on-start" type="text" name="username" placeholder="Nickname"/>
    </div>
    <div style="margin-bottom: 15vh">
        <input class="input-on-start" type="password" name="password" placeholder="Password"/>
    </div>
    <div style="margin-bottom: 19vh">
        <input type="submit" value="Sign In" id="butt"/>
    </div>

    <div style="margin-bottom: 3vh">
        <a  id="sign-up"href="signup.php">Registration</a>
    </div>
</form>

</body>
</html>