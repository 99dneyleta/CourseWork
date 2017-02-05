<?php
session_start();
include_once("functionality.php");
include_once("dbConnect.php");

$user = getUser();
if (!isset($user)) {
    header("Location: index.php");
}

$error = null;

if ( isset($_POST['didLoad'])) {
    $user->hobby = trim(mysqli_real_escape_string($dbCon, $_POST['hobby']));
    $user->inspiration = trim(mysqli_real_escape_string($dbCon, $_POST['inspiration']));
    $user->books = trim(mysqli_real_escape_string($dbCon, $_POST['books']));
    $user->music = trim(mysqli_real_escape_string($dbCon, $_POST['music']));

    $error = $user->upgradeDetail($dbCon);
}

$user->update($dbCon, false);
?>

<!Doctype html>
<html>
<head>
    <title>Detail info</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="/base.css?v=<?=time();?>">
    <link rel="stylesheet" href="/detail.css?v=<?=time();?>">
    <style>
        ::-webkit-input-placeholder { /* WebKit browsers input color*/
            color:    black;
        }
    </style>

</head>

<body>

<!-- HERE GOES MENU -->
<script>
    function show_menu(){

        var el = document.getElementById("menu");
        var title = document.getElementById("title");
        var dash = document.getElementById("dash");
        var dashes = document.getElementById("alone");
        if ( el.style.display == 'none' ) {

            el.style.display = 'inline';
            el.style.animation= 'moveforward 0.3s';
            el.style.webkitAnimationTimingFunction = 'ease-in';
            el.style.webkitAnimation = 'moveforward 0.3s';

            dash.style.animation= 'moveforward-dash 0.3s';
            dash.style.webkitAnimationTimingFunction = 'ease-in';
            dash.style.webkitAnimation = 'moveforward-dash 0.3s';

            title.style.animation= 'moveforward-title 0.3s';
            title.style.webkitAnimationTimingFunction = 'ease-in';
            title.style.webkitAnimation = 'moveforward-title 0.3s';

            dashes.className = "open";

            dash.style.left = '45vw';

            title.style.left = '59vw';
        } else {

            el.style.animation= 'moveback 0.3s';
            el.style.webkitAnimationTimingFunction = 'ease-out';
            el.style.webkitAnimation = 'moveback 0.3s';

            dash.style.animation= 'moveback-dash 0.3s';
            dash.style.webkitAnimationTimingFunction = 'ease-out';
            dash.style.webkitAnimation = 'moveback-dash 0.3s';

            title.style.animation= 'moveback-title 0.3s';
            title.style.webkitAnimationTimingFunction = 'ease-out';
            title.style.webkitAnimation = 'moveback-title 0.3s';

            setTimeout(function(){
                el.style.display = 'none';
            }, 290);

            dashes.className = "";

            dash.style.left = '0';

            title.style.left = '32vw';
        }
    }
</script>
<div class="menu" id="menu" style="display: none;">
    <a href="profile.php">
        <img class="profile-image-tiny" src=" <?php if ( isset($user->image) ) {echo "images/".$user->image;} else {echo "/img/space-for-avatar.png";} ?> " >
        <div class="menu_header"><?php if ( isset($user->username)) {echo $user->username;} else { echo "dev/null/";}?></div>
        <div class="menu_status">Online</div>
    </a>
    <a href="javascript:void(0);"><div class="menu_button"><img src="img/chats.svg" class="menu_image">Chats</div></a>
    <a href="javascript:void(0);"><div class="menu_button"><img src="img/friends.svg" class="menu_image">Friends</div></a>
    <a href="profileData.php"><div class="menu_button"><img src="img/settings.svg" class="menu_image">Settings</div></a>
    <a href="logout.php"><div class="menu_button"><img src="img/logout.svg" class="menu_image">Log out</div></a>
</div>

<!-- HERE ENDS MENU -->

<header style="margin-bottom: 50px;">
    <div class="dash" id="dash" onclick="show_menu()">
        <div id="alone">
            <span></span>
            <span></span>
            <span></span>
        </div>

    </div>
    <div class="header-title" id="title" style="display: block; left: 32vw;">Detail info</div>
    <div class = "unused-box-for-decoration"><p><a href="#"></a></p></div>
</header>

<div class="header-empty"></div>

<?php if ( $error ) {echo getAlert("", $error); } ?>

<form name="detail" method="post" action="detailInfo.php" >
    <input type="hidden" name="didLoad" value="true">

    <div id="label">My hobby</div>
    <textarea name="hobby" rows="3"><?php if ( isset($user->hobby)) {echo $user->hobby;} ?></textarea>

    <div id="label">My inspiration</div>
    <textarea name="inspiration" rows="3"><?php if ( isset($user->inspiration)) {echo $user->inspiration;} ?></textarea>

    <div id="label">My favorite books</div>
    <textarea name="books" rows="3"><?php if ( isset($user->books)) {echo $user->books;} ?></textarea>

    <div id="label">My favorite music</div>
    <textarea name="music" rows="3"><?php if ( isset($user->music)) {echo $user->music;} ?></textarea>

    <input type="submit" value="Update">

</form>
</body>
</html>