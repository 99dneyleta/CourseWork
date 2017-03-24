<?php
session_start();

include_once("Brain/functionality.php");

$occupied = null;
if ( isset($_POST['username']) && isset($_POST["password"])) {

    ////////READING ALL INFORMATION (SECURE FOR SQL)///////////////

    $username = mysqli_real_escape_string($dbCon, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT, array('cost' => 12));
    $email = mysqli_real_escape_string($dbCon, $_POST['e-mail']);

    ////////////////////CHECKING IF USERNAME IS OCCUPIED////////

    $sql = "SELECT id FROM members WHERE username='" . $username . "';";
    $row = mysqli_fetch_row(mysqli_query($dbCon, $sql));
    if (isset($row[0])) {
        $occupied = true;
    } else {

        ////////////////////WRITING TO DB/////////////////////


        $sql = "INSERT INTO members " .
            "(username, password, email) " .
            "VALUES ( '$username','$password','$email' );";

        if (!mysqli_query($dbCon, $sql)) {
            die("Sorry, database is offline, try again later.");
        }

        $sql = "SELECT id FROM members WHERE username = '$username' AND activated = '1' LIMIT 1";
        $query = mysqli_query($dbCon, $sql);
        $row = mysqli_fetch_row($query);
        $uid = $row[0];

        $user = User::withBasicStuff($username, $uid, $email);
        generateSessionAndCookie($user);

        header("Location: profileData.php");
    }
}

include "View/signup.phtml";