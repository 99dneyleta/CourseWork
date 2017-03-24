<?php
session_start();
include_once("Brain/functionality.php");

if (isset($_SESSION['user']) || isset($_COOKIE['user'])) {
    header("Location: profile.php");
}

$error = null;
//if user trying to login
if (isset($_POST['username'])) {

    // Set the posted data from the form into local variables
    $username = strip_tags($_POST['username']);
    $password = strip_tags($_POST['password']);
    // escape variables for security
    $username = mysqli_real_escape_string($dbCon, $username);
    $password = mysqli_real_escape_string($dbCon, $password);


    $sql = "SELECT password FROM members WHERE username = '$username' AND activated = '1' LIMIT 1";
    $query = mysqli_query($dbCon, $sql);
    $row = mysqli_fetch_row($query);
    $dbPassword = $row[0];

    // Check if the password they entered was correct
    if (password_verify($password, $dbPassword)) {

        $user = User::withUsername($username);
        $user->update(true);
        generateSessionAndCookie($user);
        // Now direct to users feed
        header("Location: profile.php");
    } else {
        $error = 1;
    }

}

include "View/login.phtml";