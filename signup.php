<?php
    session_start();

    include_once("functionality.php");

    $occupied = null;
    if ( isset($_POST['username']) && isset($_POST["password"])) {

        ////////READING ALL INFORMATION (SECURE FOR SQL)///////////////

        $username = mysqli_real_escape_string($dbCon, $_POST['username']);
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT, array('cost' => 12));
        $email = mysqli_real_escape_string($dbCon, $_POST['e-mail']);

        ////////////////////CHECKING IF USERNAME IS OCCUPIED////////

        $sql = "SELECT id FROM members WHERE username='" . $username . "';";
        $row = mysqli_fetch_row(mysqli_query($dbCon, $sql));
        if (isset($row[0])) {
            $occupied = true;
        } else {

            ////////////////////WRITING TO DB/////////////////////


            $sql = "INSERT INTO members " .
                "(username, password, email) " .
                "VALUES ( '$username','$password','$email' );";

            if (!mysqli_query($dbCon, $sql)) {
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
    }
?>

<!Doctype html>
<html style="">
<head>
    <title>Sign up</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="/styles.css?v=<?php echo time();?>">
    <style>
        ::-webkit-input-placeholder { /* WebKit browsers input color*/
            color:    black;
        }
    </style>
    <script>
        <!--
            function scorePassword(pass) {
                var score = 0;
                if (!pass)
                    return score;

                // award every unique letter until 5 repetitions
                var letters = new Object();
                for (var i=0; i<pass.length; i++) {
                    letters[pass[i]] = (letters[pass[i]] || 0) + 1;
                    score += 5.0 / letters[pass[i]];
                }

                // bonus points for mixing it up
                var variations = {
                    digits: /\d/.test(pass),
                    lower: /[a-z]/.test(pass),
                    upper: /[A-Z]/.test(pass),
                    nonWords: /\W/.test(pass)
                };

                variationCount = 0;
                for (var check in variations) {
                    variationCount += (variations[check] == true) ? 1 : 0;
                }
                score += (variationCount - 1) * 10;

                return parseInt(score);
            }
            function passwordValidation(input) {
                var score = scorePassword(input.value);

                if (score > 80) {
                    input.style.backgroundColor = '#0f0';
                } else if (score > 20) {
                    input.style.backgroundColor = '#fff';
                } else {
                    input.style.backgroundColor = '#f00';
                }

            }

            function emailValidation(input) {
                var x = input.value;
                if ( x == null || x == "" || !(x.includes("@") && x.includes(".") )) {
                    input.style.backgroundColor = "#f00";
                } else {
                    input.style.backgroundColor = "#fff";
                }
            }

            function validateForm() {
                var x = document.forms["signUp"]["username"].value;
                if ( x == null || x == "" ) {
                    alertt();
                    return false;
                }
                x = document.forms["signUp"]["password"].value;
                if ( x == null || x == "" || scorePassword(x) < 20) {
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
                if ( document.getElementById('alert-occ') ) {
                    document.getElementById('alert-occ').style.display = 'none';
                }
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
        <input class="input-half" style="background-color: white; border-color:#580EAD; width:70vw;"type="text"  name="username" placeholder="Username"/>
    </div>
    <div style="margin-bottom: 5vh;">
        <input class="input-half" style="background-color: white;color: black; border-color:#580EAD;width:70vw;" type="password" name="password" onchange="passwordValidation(this)" onkeyup="passwordValidation(this)" placeholder="Password"/>
    </div>
    <div style="margin-bottom: 15vh;">
        <input class="input-half" style="background-color: white; border-color:#580EAD;color: black;width:70vw;" type="email" name="e-mail" onchange="emailValidation(this)" onkeyup="emailValidation(this)" placeholder="E-mail"/>

    </div>
    <div class="alert" style="display: none;" id="alert">
        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
        <strong>Please, fill all fields correctly!</strong>
    </div>
    <?php
        if ( $occupied ) {
            echo "
                <div class=\"alert\" style=\"display: block;\" id=\"alert-occ\">
                    <span class=\"closebtn\" onclick=\"this.parentElement.style.display='none';\">&times;</span>
                    This username is occupied. Please choose another.
                </div>
            ";
        }
    ?>
    <div style="margin-bottom: 19vh">
        <input type="submit" value="Sign Up" id="butt"/>
    </div>

    <div style="margin-bottom: 3vh">

    </div>
</form>
</body>
</html>
