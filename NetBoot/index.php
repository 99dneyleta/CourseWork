<?php
session_start();
if(!isset($_COOKIE['user'])) {
    header("Location: login.php");
} else {
    $_SESSION['user'] = $_COOKIE['user'];
    header("Location: profile.php");
}
?>