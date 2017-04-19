<?php
/*
$file = fopen("/Users/deniz/DB/db.config", "r");
$str = fgets($file);
fclose($file);
$arr = unserialize($str);
//die(var_dump($arr));
$dbCon = mysqli_connect($arr["host"], $arr['username'], $arr["password"], $arr["database"]);
*/
//just connector (driver) to mysql on my mac with my login and password to a specific database (Users)
$dbCon = mysqli_connect("eu-cdbr-west-01.cleardb.com", "b38032f194eeca", "74f878ef", "heroku_a42b2b8203907e2");

//this lines should never appear, just in case of connection error, thought it is not an execute file, just for include
if ( mysqli_connect_error() ) {
    //die means "stop doing enything with message in ( here )
    //oh, dot (.) means + for strings
    die("Error: ".mysqli_connect_error());
}
?>
