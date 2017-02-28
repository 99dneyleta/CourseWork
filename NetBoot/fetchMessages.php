<?php
/*
 * File that fetches messages and returns only new ones
 */

include_once("functionality.php");
if ( !isset($_POST['uid']) || !isset($_POST['partUid']) || !isset($_POST['lastId']) ) {
    echo "";
} else {
    //creating 2 users with basic info and conversation between them
    $me = new User();
    $me->uid = $_POST['uid'];

    $part = new User();
    $part->uid = $_POST['partUid'];

    $me->updateBasic();
    $part->updateBasic();

    $conversation = new Conversation();
    $conversation->me = $me;
    $conversation->interlocutor = $part;

    $lastId = $_POST['lastId'];
    if ( $lastId == 0) {
        $conversation->loadBasics();
    } else {
        //here would be good, if loadUnread function worked)
        $conversation->loadBasics();
    }

    $mess = "";

    $maxId = $lastId;

    //very easy: using standard syntax for each messages adding them for returning in backwards
    foreach ($conversation->messages as $message) {
        $class = ( $message->fromUser->uid == $me->uid )? "outgoing" : "incoming";
        $att = ($message->attachment)? "See attachment" : "";
        if ($message->id > $lastId) {
            if ( $message->id > $maxId) { $maxId = $message->id; }
            $currentMessage = "<div id='message' ><div id='$message->id' class='$class' >".$message->text."</div><a href=\"".$message->attachment."\" class='attach' >".$att."</a></div>";
            $mess = $currentMessage . " " .$mess;
        }
    }

    //returning last loaded message's Id with all messages
    echo $maxId."◊".$mess;

}
?>