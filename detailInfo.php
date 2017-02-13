<?php
//standard
session_start();
include_once("functionality.php");

$user = getUser();
if (!isset($user)) {
    header("Location: index.php");
}

//error in case of sql error
$error = null;

//if page has been already loaded and pressed the Update button
if ( isset($_POST['didLoad'])) {
    //using trim, mysqli_real_escape_string reading all info
    $user->hobby = trim(mysqli_real_escape_string($dbCon, $_POST['hobby']));
    $user->city = trim(mysqli_real_escape_string($dbCon, $_POST['city']));
    $user->books = trim(mysqli_real_escape_string($dbCon, $_POST['books']));
    $user->music = trim(mysqli_real_escape_string($dbCon, $_POST['music']));

    $error = $user->upgradeDetail();
}

$user->update(false);
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

    <script type="text/javascript" src="jquery-1.4.4.min.js"></script>
    <script type="text/javascript" src="jgestures.min.js"></script>
    <script type="text/javascript">
        $(function(){
            $('body').bind('swipeone', function(){show_menu();});
        });
    </script>
</head>

<body>

<!-- HERE GOES MENU -->
<script>
    function show_menu(){

        var el = document.getElementById("menu");
        var title1 = document.getElementById("title1");
        var title2 = document.getElementById("title2");
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

            title1.style.animation= 'moveforward-title 0.3s';
            title1.style.webkitAnimationTimingFunction = 'ease-in';
            title1.style.webkitAnimation = 'moveforward-title 0.3s';

            title2.style.animation= 'moveforward-title-first 0.3s';
            title2.style.webkitAnimationTimingFunction = 'ease-in';
            title2.style.webkitAnimation = 'moveforward-title-first 0.3s';

            dashes.className = "open";

            dash.style.left = '45vw';

            title1.style.left = '66vw';

            title2.style.left = 0;
        } else {

            el.style.animation= 'moveback 0.3s';
            el.style.webkitAnimationTimingFunction = 'ease-out';
            el.style.webkitAnimation = 'moveback 0.3s';

            dash.style.animation= 'moveback-dash 0.3s';
            dash.style.webkitAnimationTimingFunction = 'ease-out';
            dash.style.webkitAnimation = 'moveback-dash 0.3s';

            title1.style.animation= 'moveback-title 0.3s';
            title1.style.webkitAnimationTimingFunction = 'ease-out';
            title1.style.webkitAnimation = 'moveback-title 0.3s';

            title2.style.animation= 'moveback-title-first 0.3s';
            title2.style.webkitAnimationTimingFunction = 'ease-out';
            title2.style.webkitAnimation = 'moveback-title-first 0.3s';

            setTimeout(function(){
                el.style.display = 'none';
            }, 290);

            dashes.className = "";

            dash.style.left = '0';

            title1.style.left = '55vw';

            title2.style.left = '32vw';
        }
    }
</script>
<div class="menu" id="menu" style="display: none;">
    <a href="profile.php">
        <img class="profile-image-tiny" src=" <?php if ( isset($user->image) ) {echo "images/".$user->image;} else {echo "img/space-for-avatar.png";} ?> " >
        <div class="menu_header"><?php if ( isset($user->username)) {echo $user->username;} else { echo "dev/null/";}?></div>
        <div class="menu_status">Online</div>
    </a>
    <a href="chats.php"><div class="menu_button"><img src="img/chats.svg" class="menu_image">Chats<?php if ( $user->hasPendingMessages() || $user->hasNewMessages()) { echo "<div id=notify></div>"; } ?></div></a>
    <a href="friends.php"><div class="menu_button"><img src="img/friends.svg" class="menu_image">Friends<?php if ( $user->hasPendingFriends()) { echo "<div id=notify></div>"; } ?></div></a>
    <a href="profileData.php"><div class="menu_button"><img src="img/settings.svg" class="menu_image">Settings</div></a>
    <a href="logout.php"><div class="menu_button"><img src="img/logout.svg" class="menu_image">Log out</div></a>
</div>

<!-- HERE ENDS MENU -->
<!-- HERE GOES HEADER-->

<header style="margin-bottom: 50px;">
    <div class="dash" id="dash" onclick="show_menu()">
        <div id="alone">
            <span></span>
            <span <?php if ( $user->hasNews()) { echo "style='background: #ff1001'"; } ?> ></span>
            <span></span>
        </div>

    </div>
    <div class="header-title" id="title1" style="display: block; left: 55vw;">info</div>
    <div class="header-title" id="title2" style="display: block; left: 32vw;">Detail</div>
    <div class = "search"><a href="search.php"><img class="search" src="img/search.svg"></a></div>
</header>

<div class="header-empty"></div>

<?php if ( $error ) {echo getAlert("", $error); } ?>

<form name="detail" method="post" action="detailInfo.php" >
    <input type="hidden" name="didLoad" value="true">

    <div id="label">My current city</div>
    <textarea name="city" rows="3"><?php if ( isset($user->city)) {echo $user->city;} ?></textarea>

    <div id="label">My hobby</div>
    <textarea name="hobby" rows="3"><?php if ( isset($user->hobby)) {echo $user->hobby;} ?></textarea>

    <div id="label">My favorite books</div>
    <textarea name="books" rows="3"><?php if ( isset($user->books)) {echo $user->books;} ?></textarea>

    <div id="label">My favorite music</div>
    <textarea name="music" rows="3"><?php if ( isset($user->music)) {echo $user->music;} ?></textarea>

    <input type="submit" value="Update">

</form>
</body>
</html>