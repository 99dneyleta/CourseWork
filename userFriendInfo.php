<?php
session_start();
include_once("functionality.php");

$user = getUser();
if (!isset($user)) {
    header("Location: index.php");
}
$user->updateFriends();
$user->updateRequests();
$usr = null;

if ( !isset($_GET['username'])) {
    die("Go away");
} else {
    $username = trim(mysqli_real_escape_string($dbCon, $_GET['username']));
    $sql = "SELECT first_name, last_name, image, gender, city, online, id FROM members WHERE username = '$username' AND activated = '1' LIMIT 1";
    $query = mysqli_query($dbCon, $sql);
    $row = mysqli_fetch_row($query);
    if ( count($row) == 0) {
        die("Go away");
    }
    if ( mysqli_error($dbCon) ) {
        die("Go away");
    }
    $usr = User::withUsername($username);
    $usr->firstname = $row[0];
    $usr->lastname = $row[1];
    $usr->image = $row[2];
    $usr->gender = $row[3];
    $usr->city = $row[4];

    $lastLogged = $row[5];

    $usr->uid = $row[6];

    if ( round(abs(time() - $lastLogged) / 60,2) > 10) {
        $usr->status = "offline";
    }
}


if ( isset($_POST['req'])) {
    switch ($_POST['req']) {
        case "send":
            $user->sendRequest($usr->uid, $usr->username);
            break;
        case "sent":
            $user->removeRequest($usr->uid, $usr->username);
            break;
        case "accept":
            $user->acceptRequest($usr->uid, $usr->username);
            break;
        case "decline":
            $user->declineRequest($usr->uid, $usr->username);
            break;
        case "remove":
            $user->removeFromFriends( $usr->uid, $usr->username);
            break;
        default: break;
    }
}
$user->updateFriends();
$user->updateRequests();


$user->update(false);
?>


<!Doctype html>
<html>
<head>
    <title>Info</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="/base.css?v=<?=time();?>">
    <link rel="stylesheet" href="/profile.css?v=<?=time();?>">
    <link rel="stylesheet" href="/info.css?v=<?=time();?>">
    <style>
        ::-webkit-input-placeholder { /* WebKit browsers input color*/
            color:    black;
        }
        .text {
            max-width: 96%;
        }
    </style>

</head>

<body>

<!-- HERE GOES HEADER-->

<header style="margin-bottom: 50px;">
    <div class="dash"><a href="friends.php"><img src="img/back.svg"></a></div>
    <div class="header-title" id="title" style="display: block; left: 44vw;">Info</div>
    <div class = "search"><a href="search.php"><img class="search" src="img/search.svg"></a></div>
</header>
<div class="header-empty"></div>

<!-- HERE ENDS HEADER -->

<div id = "container-for-photo">
    <div class="profile-image">
        <img class="profile-image" src=" <?php if ( isset($usr->image) ) {echo "images/".$usr->image;} else {echo "/img/space-for-avatar.png";} ?> " >
    </div>
    <div class="<?php if ( $usr->status == "online" ) { echo "status"; } else { echo "offline"; };?>" >
        <?php echo $usr->status; ?>
    </div>

    <?php
        if ( $user->isFriend($usr->username)) {
            echo "<a href='chat.php?user=".$usr->username."' class='startChat'>Write message</a>";
        }
    ?>

    <form method="post" action="userFriendInfo.php?username=<?php echo $_GET['username']?>" >
        <input type="submit"
        <?php
        $isExist = false;
        $i = 0;
        while ( !$isExist && $i < count($user->outgoingRequests) ) {
            if ( $user->outgoingRequests[$i] == $usr->username) $isExist = true;
            ++$i;
        }
        if ( $isExist ) {
            echo " value=\"Request sent\" class=\"sent\" > <input type='hidden' value='sent' name='req'>";
        } else {
            $isExist = false;
            $i = 0;
            while ( !$isExist && $i < count($user->incomingRequests) ) {
                if ( $user->incomingRequests[$i] == $usr->username) $isExist = true;
                ++$i;
            }
            if ( $isExist ) {
                echo " value=\"Accept\" class=\"accept\" > <input type='hidden' value='accept' name='req'>
                    </form> <form method='post' action='userFriendInfo.php?username=".$_GET['username']."' >
                        <input type='submit' value='Decline' class='decline'>
                        <input type='hidden' value='decline' name='req'>
                    
                    ";
            } else {
                $isExist = false;
                $i = 0;
                while ( !$isExist && $i < count($user->friends) ) {
                    if ( strtolower($user->friends[$i]) == strtolower($usr->username)) $isExist = true;
                    ++$i;
                }
                if ( $isExist ) {
                    echo " value=\"Remove from friends\" class=\"remove\" > <input type='hidden' value='remove' name='req'> ";
                } else {
                    echo " value=\"Add to friends\" class=\"send\" > <input type='hidden' value='send' name='req'>";
                }
            }
        }
        ?>
    </form>




    <div class="all-info">

        <?php if ( isset($usr->firstname)) {echo "<div class=\"text\" >First name: ".$usr->firstname." </div><br>";} ?>

        <?php if ( isset($usr->lastname)) {echo "<div class=\"text\" >Last name: ".$usr->lastname."</div> <br>";} ?>

        <?php if ( isset($usr->username)) {echo "<div class=\"text\" >username: ".$usr->username."</div><br>";} ?>

        <?php if ( isset($usr->city)) {echo "<div class=\"text\" ><span>City: ".$usr->city."</span></div>";} ?>

        <?php if ( isset($user->gender) ) {echo " <div class=\"gender\" >".$user->gender."</div>";} ?>

    </div>





</div>

</body>
</html>