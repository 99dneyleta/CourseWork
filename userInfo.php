<?php
session_start();
include_once("Brain/functionality.php");

$user = getUser();
if (!isset($user)) {
    header("Location: index.php");
}
$user->updateFriends();
$user->updateRequests();
$userForeign = null;
$mode = "";

if ( !isset($_GET['username'])) {
    die("Go away");
} else {
    $username = $_GET['username'];
    $userForeign = User::withUsername($username);
    $userForeign->updateBasic();
    $isExist = false;
    $i = 0;
    while ( !$isExist && $i < count($user->outgoingRequests) ) {
        if ( $user->outgoingRequests[$i] == $username) $isExist = true;
        ++$i;
    }
    if ( $isExist ) {
        $mode = "sent";
    } else {
        $isExist = false;
        $i = 0;
        while ( !$isExist && $i < count($user->incomingRequests) ) {
            if ( $user->incomingRequests[$i] == $username) $isExist = true;
            ++$i;
        }
        if ( $isExist ) {
            $mode = "received";
        } else {
            $isExist = false;
            $i = 0;
            while ( !$isExist && $i < count($user->friends) ) {
                if ( strtolower($user->friends[$i]) == strtolower($username)) $isExist = true;
                ++$i;
            }
            if ( $isExist ) {
                $mode = "friend";
            } else {
                $mode = "none";
            }
        }
    }
}

//include "View/userFriendInfo.phtml";

$foreign = true;

include "View/profile.phtml";
