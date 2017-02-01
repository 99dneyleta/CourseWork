<?php
    session_start();

    include_once("dbConnect.php");
    include_once("functionality.php");

    if ( isset($_POST['username']) && isset($_POST["password"])) {

        ////////READING ALL INFORMATION (SECURE FOR SQL)///////////////

        $username = mysqli_real_escape_string($dbCon, $_POST['username']);
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT, array('cost' => 12));
        $email = mysqli_real_escape_string($dbCon, $_POST['e-mail']);

        ////////////////////WRITING TO DB/////////////////////


        $sql = "INSERT INTO members ".
            "(username, password, email) ".
            "VALUES ( '$username','$password','$email' );";

        if( !mysqli_query($dbCon, $sql ) ) {
             die("Sorry, database is offline, try again later.");
        }

        $sql = "SELECT id FROM members WHERE username = '$username' AND activated = '1' LIMIT 1";
        $query = mysqli_query($dbCon, $sql);
        $row = mysqli_fetch_row($query);
        $uid = $row[0];

        $user = User::withBasicStuff($username, $uid, $email);
        generateSessionAndCookie($user);

        header("Location: profileData.php");
    }
?>

<!Doctype html>
<html style="">
<head>
    <title>LogIn</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="/styless.css">
    <style>
        ::-webkit-input-placeholder { /* WebKit browsers input color*/
            color:    black;


        }

    </style>
    <script>
        <!--
            function validateForm() {
                var x = document.forms["signUp"]["username"].value;
                if ( x == null || x == "" ) {
                    alertt();
                    return false;
                }
                x = document.forms["signUp"]["password"].value;
                if ( x == null || x == "" ) {
                    alertt();
                    return false;
                }
                x = document.forms["signUp"]["e-mail"].value;
                if ( x == null || x == "" || !x.includes("@")) {
                    alertt();
                    return false;
                }
                return true;
            }
            function alertt() {
                document.getElementById("alert").setAttribute("style", "display: block;");
            }
        //-->
    </script>
</head>


<body link="white" vlink="white" style="background-color: white;">


<header>
    <div class = "back-arrow"><a href="login.php"><img src="img/back-arrow.svg" alt="back arrow"></a></div>
    <div class = "next-link"><p><a href="/profileData.php"></a></p></div>
</header>
<form style="background-color: white;" name="signUp" action="signup.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">


    <div style="margin-bottom: 5vh; ">
        <input class="input-half" style="background-color: white; border-color:#580EAD; width:70vw;"type="text"  name="username" placeholder="Nickname"/>
    </div>
    <div style="margin-bottom: 5vh;">
        <input class="input-half" style="background-color: white;color: black; border-color:#580EAD;width:70vw;" type="password" name="password" placeholder="Password"/>
    </div>
    <div style="margin-bottom: 15vh;">
        <input class="input-half" style="background-color: white; border-color:#580EAD;color: black;width:70vw;" type="email" name="e-mail" placeholder="E-mail"/>
    </div>
    <div class="alert" style="display: none;" id="alert">
        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
        <strong>Please, fill all fields correctly!</strong>
    </div>
    <div style="margin-bottom: 19vh">
        <input type="submit" value="Sign Up" id="butt"/>
    </div>

    <div style="margin-bottom: 3vh">

    </div>
</form>
</body>
</html>
