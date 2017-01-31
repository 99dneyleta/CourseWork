<?php
session_start();
include_once("functionality.php");
clearSessionAndCookie();

if (isset($_SESSION['user'])) {
    $msg = "You are now logged out";
} else {
    $msg = "<h2>Could not log you out</h2>";
}
header("Location: index.php");
?>