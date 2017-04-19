<?php
/**
 * Created by PhpStorm.
 * User: deniz
 * Date: 19/04/2017
 * Time: 7:50 PM
 */
include "./Brain/functionality.php";
include_once "./Brain/DB.php";
include_once "./Brain/Select.php";

session_start();

$user = getUser();
$list = array();

$uid = $user->uid;
$select = new Select("participant1");
$select->Where("participant2 = '".$uid."'")->AndWhere("confirm = 2");

$db = new DB();
$db->setTable("conversations");
$result = null;
try {
    $result = $db->selectAAA($select);
} catch (Error $error) {
    //die($error);
}

if ( $result != null ) {
    foreach ($result as $blackName) {
        $select = new Select('username');
        $select->Where("id = ".$blackName['participant1']);
        $db->setTable("members");
        try {
            $username = $db->selectAAA($select);
        } catch (Error $error) {
            die($error);
        }
        $userFor = User::withUsername($username[0]['username']);
        $userFor->updateBasic();
        array_push($list, $userFor);
    }
}

$select = new Select("participant2");
$select->Where("participant1 = '".$uid."'")->AndWhere("confirm = 2");

$db = new DB();
$db->setTable("conversations");
$result = null;
try {
    $result = $db->selectAAA($select);
} catch (Error $error) {
    //die($error);
}

if ( $result != null ) {
    foreach ($result as $blackName) {
        $select = new Select('username');
        $select->Where("id = ".$blackName['participant2']);
        $db->setTable("members");
        try {
            $username = $db->selectAAA($select);
        } catch (Error $error) {
            die($error);
        }
        $userFor = User::withUsername($username[0]['username']);
        $userFor->updateBasic();
        array_push($list, $userFor);
    }
}

include "./View/blacklist.phtml";