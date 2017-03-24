<?php
//standard
session_start();
include_once("Brain/functionality.php");

$user = getUser();
if (!isset($user)) {
    header("Location: index.php");
}

//error in case of sql error
$error = null;

//if page has been already loaded and pressed the Update button
if ( isset($_POST['didLoad'])) {
    //using trim, mysqli_real_escape_string reading all info
    $user->hobby = trim(mysqli_real_escape_string($dbCon, $_POST['hobby']));
    $user->city = trim(mysqli_real_escape_string($dbCon, $_POST['city']));
    $user->books = trim(mysqli_real_escape_string($dbCon, $_POST['books']));
    $user->music = trim(mysqli_real_escape_string($dbCon, $_POST['music']));

    $error = $user->upgradeDetail();
}

$user->update(false);

include "View/detailInfo.phtml";