<?php
session_start();
include_once("Brain/functionality.php");

$user = getUser();
if (!isset($user)) {
    header("Location: index.php");
}
$user->update(true);
$user->updateFriends();
$user->updateRequests();
$userForeign = $user;

generateSessionAndCookie($user);

$listed = false;

include "View/profile.phtml";

