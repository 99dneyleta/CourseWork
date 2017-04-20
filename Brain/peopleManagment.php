<?php
/**
 * Created by PhpStorm.
 * User: deniz
 * Date: 19/04/2017
 * Time: 4:03 PM
 */
include "./functionality.php";
include "DB.php";
include "Select.php";

$user = getUser();
$userForeign = null;

if ( !isset($_POST['username'])) {
    return "No username";
} else {
    $username = $_POST['username'];//trim(mysqli_real_escape_string($GLOBALS['dbCon'], $_POST['username']));

    $select = new Select("id");
    $select->Where("username = '".$username."'")->AndWhere("activated='1'")->Limit(1);

    $db = new DB();
    $db->setTable("members");

    try {
        $row = $db->selectAAA($select);
    } catch (Error $error) {
        die($error);
    }
    /*
    $sql = "SELECT id FROM members WHERE username = '$username' AND activated = '1' LIMIT 1";
    $query = mysqli_query($GLOBALS['dbCon'], $sql);
    $row = mysqli_fetch_row($query);
    if ( count($row) == 0) {
        die ("Count row = 0 (".$username.")");
    }
    if ( mysqli_error($GLOBALS['dbCon']) ) {
        die ("Error:".mysqli_error($GLOBALS['dbCon'])." (".$username.")");
    }
    */
    $userForeign = User::withUsername($username);
    $userForeign->uid = $row[0]['id'];
}


if ( isset($_POST['req'])) {
    switch ($_POST['req']) {
        case "send":
            if ( $user->sendRequest($userForeign->uid, $userForeign->username) ){
                die("true");
            } else {
                die("false");
            }

            break;
        case "sent":
            if( $user->removeRequest($userForeign->uid, $userForeign->username)){
                die("true");
            } else {
                die("false");
            }
            break;
        case "accept":
            if ( $user->acceptRequest($userForeign->uid, $userForeign->username) ) {
                $idSelect = new Select("username");
                $idSelect->Where("id = '".$userForeign->uid."'")->Limit(1);
                $DB = new DB();
                $DB->setTable("members");
                try {
                    $username = $DB->selectAAA($idSelect)[0]['username'];
                } catch (Error $error) {
                    die($error);
                }


                $DB = new DB();
                $DB->setTable("conversations");
                try {
                    $DB->delete("(participant2 = '" . $userForeign->uid . "' AND " . "participant1 = '" . $user->uid . "')");
                } catch (Error $error) {
                    die($error);
                }
                die("true");
            } else {
                die("false");
            }
            break;
        case "decline":
            if ( $user->declineRequest($userForeign->uid, $userForeign->username) ) {
                die("true");
            } else {
                die("false");
            }
            break;
        case "remove":
            if ( $user->removeFromFriends( $userForeign->uid, $userForeign->username)){
                die("true");
            } else {
                die("false");
            }
            break;
        default:
            return "No such option: ".$_POST['req'];
            break;
    }
} else {
    return "Not set param 'req'";
}