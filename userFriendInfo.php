<?php
session_start();
include_once("Brain/functionality.php");

$user = getUser();
if (!isset($user)) {
    header("Location: index.php");
}
$user->updateFriends();
$user->updateRequests();
$userForeign = null;

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
    $userForeign = User::withUsername($username);
    $userForeign->firstname = $row[0];
    $userForeign->lastname = $row[1];
    $userForeign->image = $row[2];
    $userForeign->gender = $row[3];
    $userForeign->city = $row[4];

    $lastLogged = $row[5];

    $userForeign->uid = $row[6];

    if ( round(abs(time() - $lastLogged) / 60,2) > 10) {
        $userForeign->status = "offline";
    }
}


if ( isset($_POST['req'])) {
    switch ($_POST['req']) {
        case "send":
            $user->sendRequest($userForeign->uid, $userForeign->username);
            break;
        case "sent":
            $user->removeRequest($userForeign->uid, $userForeign->username);
            break;
        case "accept":
            $user->acceptRequest($userForeign->uid, $userForeign->username);
            break;
        case "decline":
            $user->declineRequest($userForeign->uid, $userForeign->username);
            break;
        case "remove":
            $user->removeFromFriends( $userForeign->uid, $userForeign->username);
            break;
        default: break;
    }
}
$user->updateFriends();
$user->updateRequests();


$user->update(false);

//include "View/userFriendInfo.phtml";

$foreign = true;
$friend = true;

include "View/profile.phtml";
