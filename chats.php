<?php
session_start();
include_once("functionality.php");

$user = getUser();
if (!isset($user)) {
    header("Location: index.php");
}

$result = null;
$founded = $user->getAllConversations();
$pending = $user->getPendingConversations();
$user->update(false);

?>


<!Doctype html>
<html>
<head>
    <title>Chats</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="/base.css?v=<?=time();?>">
    <link rel="stylesheet" href="/chats.css?v=<?=time();?>">
    <style>
        ::-webkit-input-placeholder { /* WebKit browsers input color*/
            color:    black;
        }
    </style>

</head>


<body>

<!-- HERE GOES MENU -->

<script>

    function show_right(left, right, leftHeader, rightHeader) {
        var r = document.getElementById(right);
        var l = document.getElementById(left);
        var rh = document.getElementById(rightHeader);
        var lh = document.getElementById(leftHeader);

        if ( r.style.display == "block") {
            return ;
        }
        r.style.display = "block";

        rh.style.backgroundColor = "#580EAD";
        rh.style.color = "#0EADA7";
        lh.style.backgroundColor = "#0EADA7";
        lh.style.color = "#580EAD";

        l.style.position = "fixed";
        r.style.position = "fixed";

        l.style.animation= 'moveforward-content-left 0.3s';
        l.style.webkitAnimationTimingFunction = 'ease-in';
        l.style.webkitAnimation = 'moveforward-content-left 0.3s';

        r.style.animation= 'moveforward-content-right 0.3s';
        r.style.webkitAnimationTimingFunction = 'ease-in';
        r.style.webkitAnimation = 'moveforward-content-right 0.3s';

        setTimeout(function () {
            l.style.display = "none";
            l.style.position = "absolute";
            r.style.position = "absolute";
        }, 290);

    }

    function show_left(left, right, leftHeader, rightHeader) {
        var r = document.getElementById(right);
        var l = document.getElementById(left);
        var rh = document.getElementById(rightHeader);
        var lh = document.getElementById(leftHeader);

        if ( l.style.display == "block") {
            return ;
        }

        l.style.display = "block";

        lh.style.backgroundColor = "#580EAD";
        lh.style.color = "#0EADA7";
        rh.style.backgroundColor = "#0EADA7";
        rh.style.color = "#580EAD";

        l.style.position = "fixed";
        r.style.position = "fixed";

        l.style.animation= 'moveback-content-left 0.3s';
        l.style.webkitAnimationTimingFunction = 'ease-in';
        l.style.webkitAnimation = 'moveback-content-left 0.3s';

        r.style.animation= 'moveback-content-right 0.3s';
        r.style.webkitAnimationTimingFunction = 'ease-in';
        r.style.webkitAnimation = 'moveback-content-right 0.3s';

        setTimeout(function () {
            r.style.display = "none";
            l.style.position = "absolute";
            r.style.position = "absolute";
        }, 290);
    }


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

            title.style.left = '65vw';
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
    <a href="chats.php"><div class="menu_button"><img src="img/chats.svg" class="menu_image">Chats</div></a>
    <a href="friends.php"><div class="menu_button"><img src="img/friends.svg" class="menu_image">Friends</div></a>
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
    <div class="header-title" id="title" style="display: block; left: 40vw;">Chats</div>
    <div class = "search"><a href="search.php"><img class="search" src="img/search.svg"></a></div>
</header>
<div class="header-empty"></div>

<!-- HERE ENDS HEADER -->


<div style="display: inline-flex; width: 100%;" >
    <div id="left-hand" onclick="show_left('exists', 'pending', 'left-hand', 'right-hand')">Exists</div>
    <div id="right-hand" onclick="show_right('exists', 'pending', 'left-hand', 'right-hand')">Pending</div>
</div>

<div id="exists" style="display: block;">
    <?php
    if ( count($founded) == 0 ) {
        echo "<div class='no-result'>No friends founded</div>";
    } else {
        foreach ($founded as $usr) {
            $count = $usr->howMuchUnread();
            if($count) {
                $count = "<div class='unread'>$count</div>";
            } else {
                $count = "";
            }
            $last = $usr->lastMessage();
            $img = ( isset($usr->image) )?  "images/".$usr->interlocutor->image : "/img/space-for-avatar.png";
            echo "<a href='chat.php?user=".$usr->interlocutor->username."'><div class='result'>
                    <img src='".$img."' class=\"image-tiny\" >
                    <div class='inf' >
                    <div class=\"username\" >".$usr->interlocutor->getName()." </div>
                    <div class=\"last-message\" >".$last." </div>
                    </div>
                    $count
              </div></a>";
        }
    }
    ?>

</div>


<div id="pending" style="display: none;">
    <?php
    if ( count($pending) == 0 ) {
        echo "<div class='no-result'>No requests founded</div>";
    } else {
        foreach ($pending as $usr) {
            $count = $usr->howMuchUnread();
            if($count) {
                $count = "<div class='unread'>$count</div>";
            } else {
                $count = "";
            }
            $last = $usr->lastMessage();
            $img = ( isset($usr->image) )?  "images/".$usr->interlocutor->image : "/img/space-for-avatar.png";
            echo "<a href='chat.php?user=".$usr->interlocutor->username."'><div class='result'>
                    <img src='".$img."' class=\"image-tiny\" >
                    <div class='inf' >
                    <div class=\"username\" >".$usr->interlocutor->getName()." </div>
                    <div class=\"last-message\" >".$last." </div>
                    </div>
                    $count
              </div></a>";
        }
    }
    ?>
</div>



</body>
</html>