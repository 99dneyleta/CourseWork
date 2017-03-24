<?php
session_start();
include_once("Brain/functionality.php");
header('Content-Type: text/html; charset=utf-8');

$user = getUser();
if (!$user) {
    header("Location: index.php");
}
if ( isset($_POST['wasloaded'])){

    if ( isset($_POST['firstname']) &&  mysqli_real_escape_string($dbCon, $_POST['firstname']) != "") {
        $user->firstname = mysqli_real_escape_string($dbCon, $_POST['firstname']);
    } else {
        $user->firstname = null;
    }
    if ( isset($_POST['lastname']) && mysqli_real_escape_string($dbCon, $_POST['lastname']) != "") {
        $user->lastname = mysqli_real_escape_string($dbCon, $_POST['lastname']);
    } else {
        $user->lastname = null;
    }
    if ( isset($_FILES)) {
        proceedImageUpdate($user, "file");
    } else {
        $user->image = null;
    }
    if ( isset($_POST['gender']) && mysqli_real_escape_string($dbCon, $_POST['gender']) != "") {
        $user->gender = mysqli_real_escape_string($dbCon, $_POST['gender']);
    } else {

        $user->gender = null;
    }

    $user->upgrade();
    generateSessionAndCookie($user);

    header("Location: profile.php");
}

include "View/profileData.phtml";