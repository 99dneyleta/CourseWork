<?php
session_start();
include_once("functionality.php");
clearSessionAndCookie();
header("Location: index.php");
?>