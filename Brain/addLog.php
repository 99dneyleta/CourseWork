<?php
/*
 * File for adding logs in case of bugs with functions
 * Using method POST
 * Receive 2 values 'page' ang 'error' message
 * writing to my local file 'logs.txt' in 'append' mode
 */

if (isset($_POST['page']) && isset($_POST['error'])) {
    $myfile = fopen("logs.txt", "a");

    //dot as + for strings, date(format, time), time() returns non-understandable numbers, so use format "d-m-y; H:i:s"
    $txt = $_POST['page']." --> ".$_POST['error']." (time: ". date("d-m-Y; H:i:s", time()).")\n"."-----------------------------------\n";

    //writing (appending) to file
    fputs($myfile, $txt);
    fclose($myfile);
}