<?php
//just connector (driver) to mysql on my mac with my login and password to a specific database (Users)
$dbCon = mysqli_connect("localhost", "root", "pass", "Users");

//this lines should never appear, just in case of connection error, thought it is not an execute file, just for include
if ( mysqli_connect_error() ) {
    //die means "stop doing enything with message in ( here )
    //oh, dot (.) means + for strings
    die("Error: ".mysqli_connect_error());
}
?>