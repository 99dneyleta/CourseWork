<?php
/**
 * Created by PhpStorm.
 * User: deniz
 * Date: 09/02/2017
 * Time: 11:31 AM
 */

include("functionality.php");
include "Select.php";
include "DB.php";

$username = "admin";
$myId = 3;

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

echo "already";