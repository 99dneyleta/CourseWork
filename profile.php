<?php
session_start();
include_once("functionality.php");
include_once("dbConnect.php");

$user = getUser();
if (!isset($user)) {
    header("Location: index.php");
}
$user->update($dbCon, true);
?>


<!Doctype html>
<html style="">
<head>
    <title>Profile</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="/profile.css">
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
        var im = document.getElementById("dashes");
        var title = document.getElementById("title");
        var dash = document.getElementById("dash");
        if ( el.style.display == 'none' ) {
            el.style.display = 'inline';
            title.style.display = 'none';
            dash.style.paddingLeft = "45vw";
            im.setAttribute("src", "img/Dash_64_3.svg");
        } else {
            el.style.display = 'none';
            title.style.display = 'block';
            dash.style.paddingLeft = "0";
            im.setAttribute("src", "img/Dash_64.svg");
        }
    }
</script>
<div class="menu" id="menu" style="display: none;">
    <img class="profile-image-tiny" src=" <?php if ( isset($user->image) ) {echo "images/".$user->image;} else {echo "/img/space-for-avatar.png";} ?> " >
    <div class="menu_header"><?php if ( isset($user->username)) {echo $user->username;} else { echo "dev/null/";}?></div>
    <div class="menu_status">Online</div>
    <a href="javascript:void(0);"><div class="menu_button"><img src="img/chats.svg" class="menu_image">Chats</div></a>
    <a href="profile.php"><div class="menu_button_selected"><img src="img/profile.svg" class="menu_image">Profile</div></a>
    <a href="javascript:void(0);"><div class="menu_button"><img src="img/settings.svg" class="menu_image">Settings</div></a>
    <a href="logout.php"><div class="menu_button"><img src="img/logout.svg" class="menu_image">Log out</div></a>
</div>

<!-- HERE ENDS MENU -->

<header style="margin-bottom: 50px;">
    <div class = "dash" id="dash"><a href="javascript:void(0);" onclick="show_menu()"><img id="dashes" src="img/Dash_64.svg" alt="back arrow"></a></div>
    <div class="header-title" id="title" style="display: block;">Profile</div>
    <div class = "unused-box-for-decoration"><p><a href="#"></a></p></div>
</header>

    <div id = "container-for-photo">
        <div class="profile-image">
            <img class="profile-image" src=" <?php if ( isset($user->image) ) {echo "images/".$user->image;} else {echo "/img/space-for-avatar.png";} ?> " >
        </div>
        <div class="status" >
            <?php echo $user->status; ?>
        </div>
        <div class="text" >
            <?php if ( isset($user->firstname)) {echo "First name: '".$user->firstname."'";} ?>
        </div>

        <div class="text" >
            <?php if ( isset($user->lastname)) {echo "Last name: '".$user->lastname."'";} ?>
        </div>

        <div class="text" >
            <?php if ( isset($user->username)) {echo "username: '".$user->username."'";} ?>
        </div>

        <div class="text" >
            <?php if ( isset($user->email)) {echo "@mail: '".$user->email."'";} ?>
        </div>

    </div>

    <div class="text">
        <?php if ( isset($user->gender) ) {echo $user->gender;} ?>
    </div>

</body>
</html>