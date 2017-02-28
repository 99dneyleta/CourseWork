<?php
session_start();
include_once("functionality.php");
header('Content-Type: text/html; charset=utf-8');

$user = getUser();
if (!$user) {
    header("Location: index.php");
}
if ( isset($_POST['wasloaded'])){

    if ( isset($_POST['firstname']) &&  mysqli_real_escape_string($dbCon, $_POST['firstname']) != "") {
        $user->firstname = mysqli_real_escape_string($dbCon, $_POST['firstname']);
    } else {
        $user->firstname = null;
    }
    if ( isset($_POST['lastname']) && mysqli_real_escape_string($dbCon, $_POST['lastname']) != "") {
        $user->lastname = mysqli_real_escape_string($dbCon, $_POST['lastname']);
    } else {
        $user->lastname = null;
    }
    if ( isset($_FILES)) {
        proceedImageUpdate($user, "file");
    } else {
        $user->image = null;
    }
    if ( isset($_POST['gender']) && mysqli_real_escape_string($dbCon, $_POST['gender']) != "") {
        $user->gender = mysqli_real_escape_string($dbCon, $_POST['gender']);
    } else {

        $user->gender = null;
    }

    $user->upgrade();
    generateSessionAndCookie($user);

    header("Location: profile.php");
}

?>


<!Doctype html>
<html style="">
<head>
    <title>LogIn</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="./styles.css?v<?=time();?>">
    <style>
        ::-webkit-input-placeholder { /* WebKit browsers input color*/
            color:    black;
        }
        .inputfile {
            width: 0.1px;
            height: 0.1px;
            opacity: 0;
            overflow: hidden;
            position: absolute;
            z-index: -1;
        }


    </style>
    <script>
        function submit() {
            document.getElementById('uploadInfo').submit();
        }
        function reloadImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    document.getElementById("avatar").setAttribute("src", e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }
        function radioChange(radio) {
            if ( radio.value != "male") {
                document.getElementById('lbl_m').setAttribute("style", "color: #888;");
                document.getElementById('lbl_f').setAttribute("style", "color: #580EAD;");
            } else {
                document.getElementById('lbl_f').setAttribute("style", "color: #888;");
                document.getElementById('lbl_m').setAttribute("style", "color: #580EAD;");
            }
        }
    </script>

</head>


<body style="background-color: white;">

<header style="margin-bottom: 50px;">
    <div class = "back-arrow"><a href="/signup.php"><img src="img/back-arrow.svg" alt="back arrow"></a></div>
    <div class = "next-link"><p><a href="javascript:void(0);" onclick="submit()">Next</a></p></div>
</header>

<form id="uploadInfo" action="profileData.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="wasloaded" value="1">

    <div id = "container-for-photo">
        <div id = "Name">
            <input type="file" name="file" id="file" class="inputfile" accept="image/*" onchange="reloadImage(this)"/>
            <label for="file"><img src=" <?php if ( isset($user->image) ) {echo "images/".$user->image;} else {echo "/img/space-for-avatar.png";} ?> " alt="avatar" id="avatar"></label>

        </div>
        <div id = "Name"><input class="input-half" style="background-color: white; border-color:#580EAD; width:70vw;position: absolute;
    bottom: 0;"type="text"  name="lastname" placeholder="Last Name" <?php if ( isset($user->lastname)) {echo "value='".$user->lastname."'";} ?>/></div>

        <div id = "Name"><input class="input-half" style="
                    position:absolute;margin-top:5%;background-color: white; border-color:#580EAD; width:70vw;"type="text"  name="firstname" placeholder="First Name" <?php if ( isset($user->firstname)) {echo "value='".$user->firstname."'";} ?> /></div>

    </div>

    <div class="floating-box"><label for="file" id="lbl_file">Add Photo</label></div>
    <div class="floating-box-gender">
        <input type="radio" id="male" name="gender" value="male" onchange="radioChange(this)"  <?php if ( $user->gender == "male" ) {echo "checked";} ?> >
        <label for="male" style="color: <?php if ( $user->gender != "male" ) {echo "#888;";} else {echo "#580EAD;";} ?>" id="lbl_m">Male</label>

        <input type="radio" id="female" name="gender" value="female" onchange="radioChange(this)"  <?php if ( $user->gender == "female" ) {echo "checked";} ?> >
        <label for="female" style="color: <?php if ( $user->gender != "female" ) {echo "#888;";} else {echo "#580EAD;";} ?>" id="lbl_f">Female</label>
    </div>
</form>
</body>
</html>