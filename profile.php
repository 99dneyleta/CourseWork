<?php
session_start();
include_once("functionality.php");
include_once("dbConnect.php");

$user = getUser();
if (!isset($user)) {
    header("Location: index.php");
}
$user->update($dbCon, true);
$user->updateFriends($dbCon);
$user->updateRequests($dbCon);
?>


<!Doctype html>
<html>
<head>
    <title>Profile</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="/base.css?v=<?=time();?>">
    <link rel="stylesheet" href="/profile.css?v=<?=time();?>">
    <style>
        ::-webkit-input-placeholder { /* WebKit browsers input color*/
            color:    black;
        }
    </style>

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
    <a href="javascript:void(0);"><div class="menu_button"><img src="img/chats.svg" class="menu_image">Chats</div></a>
    <a href="friends.php"><div class="menu_button"><img src="img/friends.svg" class="menu_image">Friends</div></a>
    <?php if ( count($user->incomingRequests) || count($user->outgoingRequests)) {
        echo "<a href=\"requests.php\"><div class=\"menu_button\"><img src=\"img/requests.svg\" class=\"menu_image\">Requests</div></a>";
    } ?>
    <a href="profileData.php"><div class="menu_button"><img src="img/settings.svg" class="menu_image">Settings</div></a>
    <a href="logout.php"><div class="menu_button"><img src="img/logout.svg" class="menu_image">Log out</div></a>
</div>

<!-- HERE ENDS MENU -->
<!-- HERE GOES HEADER-->

<header style="margin-bottom: 50px;">
    <div class="dash" id="dash" onclick="show_menu()">
        <div id="alone">
        <span></span>
        <span></span>
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
        <div class="text" >
            <?php if ( isset($user->firstname)) {echo "First name: ".$user->firstname;} ?>
        </div>
<br>
        <div class="text" >
            <?php if ( isset($user->lastname)) {echo "Last name: ".$user->lastname;} ?>
        </div>
<br>
        <div class="text" >
            <?php if ( isset($user->username)) {echo "username: ".$user->username;} ?>
        </div>
<br>
        <div class="gender" >
            <?php if ( isset($user->gender) ) {echo $user->gender;} ?>
        </div>
<br>


    </div>
    <div class="detail" >
        <a href="detailInfo.php" class="detail">Detail info</a>
    </div>

</div>



</body>
</html>