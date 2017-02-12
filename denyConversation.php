<?php
include_once("functionality.php");
if ( !isset($_POST['uid']) || !isset($_POST['partUid'])) {
    return "error: uid: ".$_POST['uid']." partUid: ".$_POST['partUid'];
} else {
    $me = new User();
    $me->uid = $_POST['uid'];

    $part = new User();
    $part->uid = $_POST['partUid'];

    $me->updateBasic();
    $part->updateBasic();

    $conversation = new Conversation();
    $conversation->me = $me;
    $conversation->interlocutor = $part;
    $txt = $conversation->loadBasics();

    $txt .= $conversation->denyConfirmation();

    $myfile = fopen("logs.txt", "a");
    $txt = "deny --> ".$txt." (time: ". date("d-m-Y; H:i:s", time()).")\n"."-----------------------------------\n\n";
    fputs($myfile, $txt);
    fclose($myfile);

    header("Location: chats.php");
}
?>