<?php
session_start();
include_once("Brain/functionality.php");
clearSessionAndCookie();
header("Location: index.php");
?>