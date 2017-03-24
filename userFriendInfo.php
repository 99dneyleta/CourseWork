<?php
session_start();
include_once("Brain/functionality.php");

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

include "View/userFriendInfo.phtml";