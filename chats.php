<?php
/*
 * here goes standard syntax with 2 variables $founded (opened chats) and $pending (waiting for confirmation) which I use later in html
 */
session_start();
include_once("Brain/functionality.php");

$user = getUser();
if (!isset($user)) {
    header("Location: index.php");
}

$result = null;
$founded = $user->getAllConversations();
$pending = $user->getPendingConversations();

$user->update(false);

include "View/chats.phtml";