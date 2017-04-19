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

if ( !isset($_POST['myId']) || !isset($_POST['username']) || !isset($_POST['mode'])) {
    return false;
}

$myId = $_POST['myId'];
$username = $_POST['username'];
$mode = $_POST['mode'];

switch ($mode) {
    case "Add":
        $idSelect = new Select("id");
        $idSelect->Where("username = '".$username."'")->Limit(1);
        $DB = new DB();
        $DB->setTable("members");
        try {
            $idUser = $DB->selectAAA($idSelect)[0]['id'];
        } catch (Error $error) {
            return false;
        }

        $select = new Select("id");
        $select->Where("participant1 = '".$idUser."'")->AndWhere("participant2 = '".$myId."'")->OrWhere("participant2 = '".$idUser."' AND "."participant1 = '".$myId."'")->AndWhere("confirm = 2");

        $DB->setTable("conversations");

        try {
            $DB->selectAAA($select);
        } catch (Error $error) {
            $DB->insert(["participant1" => $myId, "participant2" => $idUser, "confirm" => 2]);
            return true;
        }
        return false;

        break;
    case "Remove":
        $idSelect = new Select("id");
        $idSelect->Where("username = '".$username."'")->Limit(1);
        $DB = new DB();
        $DB->setTable("members");
        try {
            $idUser = $DB->selectAAA($idSelect)[0]['id'];
        } catch (Error $error) {
            return false;
        }


        $DB = new DB();
        $DB->setTable("conversations");
        try {
            $DB->delete("(participant2 = '" . $idUser . "' AND " . "participant1 = '" . $myId . "') OR " . "(participant1 = '" . $idUser . "' AND " . "participant2 = '" . $myId . "')");
        } catch (Error $error) {
            return false;
        }
        return true;
        break;
    default:
        return false;
        break;
}