<?php
$dbCon = mysqli_connect("localhost", "root", "pass", "Users");
if ( mysqli_connect_error() ) {
    die("Error: ".mysqli_connect_error());
}
?>