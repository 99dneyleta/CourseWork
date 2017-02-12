<?php
include_once("functionality.php");
if ( !isset($_POST['uid']) || !isset($_POST['partUid']) || !isset($_POST['lastId']) ) {
    echo "";
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

    $lastId = $_POST['lastId'];
    if ( $lastId == 0) {
        $conversation->loadBasics();
    } else {
        $conversation->loadBasics();
    }


    $mess = "";

    $maxId = $lastId;

    foreach ($conversation->messages as $message) {
        $class = ( $message->fromUser->uid == $me->uid )? "outgoing" : "incoming";
        $att = ($message->attachment)? "See attachment" : "";
        if ($message->id > $lastId) {
            if ( $message->id > $maxId) { $maxId = $message->id; }
            $currentMessage = "<div id='message' ><div id='$message->id' class='$class' >".$message->text."</div><a href=\"".$message->attachment."\" class='attach' >".$att."</a></div>";
            $mess = $currentMessage . " " .$mess;
        }
    }


    echo $maxId."◊".$mess;

}
?>