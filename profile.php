<?php
session_start();
include_once("functionality.php");

$user = getUser();
if (!isset($user)) {
    header("Location: index.php");
}
$user->update(true);
$user->updateFriends();
$user->updateRequests();

generateSessionAndCookie($user);
?>


<!Doctype html>
<html xmlns="http://www.w3.org/1999/html">
<head>
    <title>Profile</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="/base.css?v=<?php echo time();?>">
    <link rel="stylesheet" href="/profile.css?v=<?php echo time();?>">
    <style>
        ::-webkit-input-placeholder { /* WebKit browsers input color*/
            color:    black;
        }
    </style>

    <script src="jquery-1.11.3.min.js"></script>
    <script src="jquery.mobile-1.4.5.min.js"></script>
    <script>
        $(document).on("pagecreate","body",function(){
            $("body").on("swipe",function(){
                show_menu();
            });
        });
    </script>
</head>


<body>

<!-- HERE GOES MENU -->

<script>
    function show_menu() {
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

            title.style.left = '61vw';
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

            title.style.left = '38vw';
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
<div ></div>

<header style="margin-bottom: 50px;">
    <div class="dash" id="dash" onclick="show_menu()">
        <div id="alone">
        <span></span>
        <span <?php if ( $user->hasNews()) { echo "style='background: #ff1001'"; } ?> ></span>
        <span></span>
        </div>

    </div>
    <div class="header-title" id="title" style="display: block;">Profile</div>
    <div class = "search"><a href="search.php"><img class="search" src="img/search.svg"></a></div>
</header>
<div class="header-empty"></div>

<!-- HERE ENDS HEADER -->

<div id = "container-for-photo">
    <div class="profile-image">
        <img class="profile-image" src=" <?php if ( isset($user->image) ) {echo "images/".$user->image;} else {echo "img/space-for-avatar.png";} ?> " >
    </div>
    <div class="status" >
        <?php echo $user->status; ?>
    </div>

    <div class="all-info">

        <?php if ( isset($user->firstname)) {echo "<div class=\"text\" >First name: ".$user->firstname."</div><br>";} ?>

        <?php if ( isset($user->lastname)) {echo "<div class=\"text\" >Last name: ".$user->lastname."</div><br>";} ?>

        <?php if ( isset($user->username)) {echo "<div class=\"text\" >username: ".$user->username."</div><br>";} ?>

        <?php if ( isset($user->gender) ) {echo "<div class=\"gender\" >".$user->gender."</div><br>";} ?>

    </div>
    <div class="detail" >
        <a href="detailInfo.php" class="detail">Detail info</a>
    </div>

</div>

</body>
</html>