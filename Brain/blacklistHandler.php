<?php
/**
 * Created by PhpStorm.
 * User: deniz
 * Date: 19/04/2017
 * Time: 7:12 PM
 */
include "functionality.php";
include "Select.php";
include "DB.php";

if ( !isset($_POST['myId']) || !isset($_POST['userId']) || !isset($_POST['mode'])) {
    return false;
}

$myId = $_POST['myId'];
$userId = $_POST['userId'];
$mode = $_POST['mode'];

switch ($mode) {
    case "Add":
        $idSelect = new Select("username");
        $idSelect->Where("id = '".$userId."'")->Limit(1);
        $DB = new DB();
        $DB->setTable("members");
        try {
            $username = $DB->selectAAA($idSelect)[0]['username'];
        } catch (Error $error) {
            die($error);
        }
        $idSelect = new Select("username");
        $idSelect->Where("id = '".$myId."'")->Limit(1);
        $DB = new DB();
        $DB->setTable("members");
        try {
            $myUsername = $DB->selectAAA($idSelect)[0]['username'];
        } catch (Error $error) {
            die($error);
        }

        $userToDelete = User::withUsername($username);
        $userToDelete->updateFriends();
        $userToDelete->removeFromFriends($myId, $myUsername);
        $userToDelete->upgradeFriends();
        $userToDelete->deleteConversationWith($myId);
        $userToDelete->removeRequest($myId, $myUsername);

        $select = new Select("id");
        $select->Where("participant1 = '".$myId."'")->AndWhere("participant2 = '".$userId."'")->AndWhere("confirm = 2");

        $DB->setTable("conversations");

        try {
            $DB->selectAAA($select);
        } catch (Error $error) {
            $DB->insert(["participant1" => $myId, "participant2" => $userId, "confirm" => 2]);
            die("true");
        }
        die("Error: ".$select);

        break;
    case "Remove":
        $idSelect = new Select("username");
        $idSelect->Where("id = '".$userId."'")->Limit(1);
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
            $DB->delete("(participant2 = '" . $userId . "' AND " . "participant1 = '" . $myId . "')");
        } catch (Error $error) {
            die($error);
        }
        die("true");
        break;
    default:
        die("false");
        break;
}