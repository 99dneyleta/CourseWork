<?php
if (isset($_POST['page']) && isset($_POST['error'])) {
    $myfile = fopen("logs.txt", "a");
    $txt = $_POST['page']." --> ".$_POST['error']." (time: ". date("d-m-Y; H:i:s", time()).")\n"."-----------------------------------\n\n";
    fputs($myfile, $txt);
    fclose($myfile);
}