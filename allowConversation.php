<?php
/*
 * file in case new friend wants to write a message
 * then he wrote and receiver can decide weather accept chat or decline
 * Using method POST with fields 'uid' as id of user who accepts/declines and 'partUid' as user who wants to chat
 */

//including functionality
include_once("functionality.php");

if ( !isset($_POST['uid']) || !isset($_POST['partUid'])) {
    //returning value for logging
    echo "error: uid: ".$_POST['uid']." partUid: ".$_POST['partUid'];
} else {
    //creating var user as myself and friend
    $me = new User();
    $me->uid = $_POST['uid'];

    $part = new User();
    $part->uid = $_POST['partUid'];

    //updateBasic fetches only useful and public information
    $me->updateBasic();
    $part->updateBasic();

    //creating conversation with me as me and interlocutor as friend
    $conversation = new Conversation();
    $conversation->me = $me;
    $conversation->interlocutor = $part;

    //loading basic info with only one useful field: id; maybe make sense to call different function, but I'm good with that)
    echo $conversation->loadBasics();

    //special function in case user confirm chat
    echo $conversation->gotConfirmation();

}
?>