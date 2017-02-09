<?php
session_start();
include_once("functionality.php");

$user = getUser();
if (!isset($user)) {
    header("Location: index.php");
}

$result = null;
$founded = array();
$income = array();
$outcome = array();
$user->updateFriends();

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

if ( isset($user->incomingRequests) ) {

    foreach ($user->incomingRequests as $friend) {

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
            array_push($income, $u);
        }
    }
}

if ( isset($user->outgoingRequests) ) {

    foreach ($user->outgoingRequests as $friend) {

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
            array_push($outcome, $u);
        }
    }
}

$user->update(false);
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


    var current = 1;

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

    function showFriends() {
        switch (current) {
            case 1:
                break;
            case 2:
                show_left("friends", "income", "friends-hand", "left-hand");
                break;
            case 3:
                show_left("friends", "outcome", "friends-hand", "right-hand");
                break;
            default: break;
        }
        current = 1;
    }

    function showIncome() {
        switch (current) {
            case 1:
                show_right("friends", "income", "friends-hand", "left-hand");
                break;
            case 2:
                break;
            case 3:
                show_left("income", "outcome", "left-hand", "right-hand");
                break;
            default: break;
        }
        current = 2;
    }

    function showOutcome() {
        switch (current) {
            case 1:
                show_right("friends", "outcome", "friends-hand", "right-hand");
                break;
            case 2:
                show_right("income", "outcome", "left-hand", "right-hand");
                break;
            case 3:
                break;
            default: break;
        }
        current = 3;
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
    <div class="header-title" id="title" style="display: block; left: 40vw;">Friends</div>
    <div class = "search"><a href="search.php"><img class="search" src="img/search.svg"></a></div>
</header>
<div class="header-empty"></div>

<!-- HERE ENDS HEADER -->


<div style="display: inline-flex; width: 100%;" >
    <div id="friends-hand" onclick="showFriends()">Friends</div>
    <div id="left-hand" onclick="showIncome()">Incoming</div>
    <div id="right-hand" onclick="showOutcome()">Outgoing</div>
</div>

<div id="friends" style="display: block;">
<?php
if ( count($founded) == 0 ) {
    echo "<div class='no-result'>No friends founded</div>";
} else {
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

</div>


<div id="income" style="display: none;">
    <?php
    if ( count($income) == 0 ) {
        echo "<div class='no-result'>No requests founded</div>";
    } else {
        foreach ($income as $usr) {
            $img = ( isset($usr->image) )?  "images/".$usr->image : "/img/space-for-avatar.png";
            echo "<a href='userFriendInfo.php?username=".$usr->username."'><div class='result'>
                    <img src='".$img."' class=\"image-tiny\" >
                    <div class=\"username\" >".$usr->username." </div>
                    <div class=\"fullname\" >".$usr->getName()." </div>
              </div></a>";
        }
    }
    ?>
</div>

<div id="outcome" style="display: none;">
    <?php
    if ( count($outcome) == 0 ) {
        echo "<div class='no-result'>No requests founded</div>";
    } else {
        foreach ($outcome as $usr) {
            $img = ( isset($usr->image) )?  "images/".$usr->image : "/img/space-for-avatar.png";
            echo "<a href='userFriendInfo.php?username=".$usr->username."'><div class='result'>
                    <img src='".$img."' class=\"image-tiny\" >
                    <div class=\"username\" >".$usr->username." </div>
                    <div class=\"fullname\" >".$usr->getName()." </div>
              </div></a>";
        }
    }
    ?>
</div>


</body>
</html>