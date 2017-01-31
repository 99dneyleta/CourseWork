<?php
    session_start();

    include_once("dbConnect.php");
    include_once("functionality.php");

    if ( isset($_POST['username']) && isset($_POST["password"])) {

        ////////READING ALL INFORMATION (SECURE FOR SQL)///////////////

        $username = mysqli_real_escape_string($dbCon, $_POST['username']);
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT, array('cost' => 12));
        $email = mysqli_real_escape_string($dbCon, $_POST['e-mail']);
        /*
        $file_name = null;
        if(isset($_FILES['image'])){
            $errors= $_FILES['image']['error'];
            $file_name = $_FILES['image']['name'];
            //$file_size = $_FILES['image']['size'];
            $file_tmp = $_FILES['image']['tmp_name'];
            //$file_type = $_FILES['image']['type'];
            $file_ext=strtolower(end(explode('.',$_FILES['image']['name'])));

            $extensions= array("jpeg","jpg","png");

            if(in_array($file_ext,$extensions)=== false){
                $errors[]="extension not allowed, please choose a JPEG or PNG file.";
            }
/*
            if($file_size > 2097152) {
                $errors[]='File size must be excately 2 MB';
            }
*
            if(empty($errors)==true) {
                $file_name = generateRandomString(5).'.'.$file_ext;
                if( !rename($_FILES['image']['tmp_name'],"/Users/Denis/".$file_name) ) {
                    die("move!".$_FILES['image']['tmp_name']."  to "."/Users/Denis/".$file_name);
                }
            }else{
                //print_r($errors);
                die($errors);
            }
        } else {
            die("where is photo?");
        }
*/

        ////////////////////WRITING TO DB/////////////////////


        $sql = "INSERT INTO members ".
            "(username, password, email, activated) ".
            "VALUES ( '$username','$password','$email','1' );";
        $retval = mysqli_query($dbCon, $sql );

        if(! $retval ) {
             die("Something went wrong...");
        }

        $sql = "SELECT id FROM members WHERE username = '$username' AND activated = '1' LIMIT 1";
        $query = mysqli_query($dbCon, $sql);
        $row = mysqli_fetch_row($query);
        $uid = $row[0];

        $user = new User($username, $uid, $email);
        generateSessionAndCookie($user);

        header("Location: user.php");
    }
?>
<!--
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sign Up</title>
    <script>
        <!--
          function validateForm() {
              var x = document.forms["sign"]["username"].value;
              if ( x == null || x == "" ) {
                  alert("Username must be filled out");
                  return false;
              }
              x = document.forms["sign"]["password"].value;
              if ( x == null || x == "" ) {
                  alert("Password must be filled out");
                  return false;
              }
              var y = document.forms["sign"]["password2"].value;
              if ( x != y ) {
                  alert("Passwords doesn't match!");
                  return false;
              }
              return true;
          }
        //--
    </script>
</head>
<body>
    <h2>Please, enter your information:</h2>
    <form name="sign" action="signup.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()" >
        <label for="uname">Userame</label>
        <input type="text" name="username" id="uname">
        <br>
        <label for="pass">Please create strong password</label>
        <input type="password" name="password" id="pass">
        <br>
        <label for="pass2">Confirm</label>
        <input type="password" name="password2" id="pass2">
        <br>
        <label for="firstname">First Name</label>
        <input type="text" name="firstname" id="firstname">
        <br>
        <label for="lastname">Last Name</label>
        <input type="text" name="lastname" id="lastname">
        <br>
        <label for="mail">Email</label>
        <input type="email" name="email" id="mail">

        <input type="file" name="image" />

        <input type="submit" value="Sign Up!">
    </form>
</body>

</html>
-->

<!Doctype html>
<html style="">
<head>
    <title>LogIn</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="/styles.css">
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
                    alert("Username must be filled out");
                    return false;
                }
                x = document.forms["signUp"]["password"].value;
                if ( x == null || x == "" ) {
                    alert("Password must be filled out");
                    return false;
                }
                x = document.forms["signUp"]["e-mail"].value;
                if ( x == null || x == "" || !x.includes("@")) {
                    return false;
                }
                return true;
            }
        //-->
    </script>
</head>


<body link="white" vlink="white" style="background-color: white;">
<header>
    <div class = "back-arrow"><a href="login.php"><img src="img/back-arrow.svg" alt="back arrow"></a></div>
    <div class = "next-link"><p><a href="/ProfileData.html">Next</a></p></div>
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
    <div style="margin-bottom: 19vh">
        <input type="submit" value="Sign Up" id="butt"/>
    </div>

    <div style="margin-bottom: 3vh">

    </div>
</form>
</body>
</html>
