<?php
/**
 * Created by PhpStorm.
 * User: deniz
 * Date: 19/04/2017
 * Time: 4:03 PM
 */
include "./functionality.php";


$user = getUser();
$userForeign = null;

if ( !isset($_GET['username'])) {
    return "No username";
} else {
    $username = trim(mysqli_real_escape_string($GLOBALS['dbCon'], $_GET['username']));
    $sql = "SELECT id FROM members WHERE username = '$username' AND activated = '1' LIMIT 1";
    $query = mysqli_query($GLOBALS['dbCon'], $sql);
    $row = mysqli_fetch_row($query);
    if ( count($row) == 0) {
        return "Count row = 0 (".$username.")";
    }
    if ( mysqli_error($GLOBALS['dbCon']) ) {
        return "Error:".mysqli_error($GLOBALS['dbCon'])." (".$username.")";
    }
    $userForeign = User::withUsername($username);
    $userForeign->uid = $row[0];
}


if ( isset($_POST['req'])) {
    switch ($_POST['req']) {
        case "send":
            return $user->sendRequest($userForeign->uid, $userForeign->username);
            break;
        case "sent":
            return $user->removeRequest($userForeign->uid, $userForeign->username);
            break;
        case "accept":
            return $user->acceptRequest($userForeign->uid, $userForeign->username);
            break;
        case "decline":
            return $user->declineRequest($userForeign->uid, $userForeign->username);
            break;
        case "remove":
            return $user->removeFromFriends( $userForeign->uid, $userForeign->username);
            break;
        default:
            return "No such option: ".$_POST['req'];
            break;
    }
} else {
    return "Not set param 'req'";
}