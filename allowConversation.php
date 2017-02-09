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
    echo $conversation->loadBasics();

    echo $conversation->gotConfirmation();

}
?>