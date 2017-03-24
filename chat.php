<?php
//session start - standard syntax for user-interface web page
session_start();
include_once("Brain/functionality.php");

//getting user from cache or session
$user = getUser();
if (!isset($user)) {
    // if user do not exist, locate page to load index.php
    header("Location: index.php");
}
//update some basic info
$user->update(false);

//load all conversation with current user
//in case you haven't opened this page properly (with right parameter) I stops loading page and display only some text
$interlocutor = null;
if ( !isset($_GET['user']) ) {
    die("Go! What are you thinking you are doing?");
} else {
    //getting username from field by method GET
    $interlocutor = User::withUsername($_GET['user']);
}

//creating conversation
$conversation = $user->getConversationWithUser($interlocutor);

//getting confirmation sign
$confirm = $conversation->confirm;

//if not permitted to write go to hell
if ( $confirm == 2 && !$conversation->reverse) {
    header("Location: chats.php");
}

include "View/chat.phtml";



