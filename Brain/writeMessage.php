<?php
include_once("functionality.php");
if ( !isset($_POST['uid']) || !isset($_POST['partUid']) || !isset($_POST['text']) || !isset($_POST['attachment']) ) {
    return "error: uid: ".$_POST['uid']." partUid: ".$_POST['partUid']." text: ".$_POST['text']." att: ".$_POST['attachment'];
} else {
    $me = new User();
    $me->uid = $_POST['uid'];

    $part = new User();
    $part->uid = $_POST['partUid'];

    $me->updateBasic();
    $part->updateBasic();

    $text = mysqli_real_escape_string($GLOBALS['dbCon'], $_POST['text']);
    $att = mysqli_real_escape_string($GLOBALS['dbCon'], $_POST['attachment']);

    $conversation = new Conversation();
    $conversation->me = $me;
    $conversation->interlocutor = $part;
    $conversation->loadBasics();

    $conversation->addMessage($text, $att);

}
?>