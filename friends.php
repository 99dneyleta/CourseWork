<?php
session_start();
include_once("functionality.php");
include_once("dbConnect.php");

$user = getUser();
if (!isset($user)) {
    header("Location: index.php");
}

$result = null;
$founded = array();
$user->updateFriends($dbCon);
if ( isset($user->friends) ) {

    foreach ($user->friends as $friend) {

        $sql = "SELECT username, first_name, last_name, image " .
            "FROM members " .
            "WHERE username='" . $friend . "' ;";
        $query = mysqli_query($dbCon, $sql);

        if (!mysqli_affected_rows($dbCon)) {

        } else {
            $rowData = mysqli_fetch_row($query);

            $u = User::withUsername($rowData[0]);
            $u->firstname = $rowData[1];
            $u->lastname = $rowData[2];
            $u->image = $rowData[3];
            array_push($founded, $u);
        }
    }
    $result = true;
}

$user->update($dbCon, false);
?>


<!Doctype html>
<html>
<head>
    <title>Profile</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="/base.css?v=<?=time();?>">
    <link rel="stylesheet" href="/friends.css?v=<?=time();?>">
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

            title.style.left = '40vw';
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
    <div class="header-title" id="title" style="display: block; left: 40vw;">Friends</div>
    <div class = "search"><a href="search.php"><img class="search" src="img/search.svg"></a></div>
</header>
<div class="header-empty"></div>

<!-- HERE ENDS HEADER -->


<?php
if ( isset($_GET['search']) && !$result ) {
    echo "<div class='no-result'>No results founded</div>";
} elseif( isset($result) ) {
    foreach ($founded as $usr) {
        $img = ( isset($usr->image) )?  "images/".$usr->image : "/img/space-for-avatar.png";
        echo "<a href='userFriendInfo.php?username=".$usr->username."'><div class='result'>
                    <img src='".$img."' class=\"image-tiny\" >
                    <div class=\"username\" >".$usr->username." </div>
                    <div class=\"fullname\" >".$usr->getName()." </div>
              </div></a>";
    }
}
?>

</body>
</html>