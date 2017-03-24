<?php
//ok, very standard
session_start();
include_once("Brain/functionality.php");

$user = getUser();
if (!isset($user)) {
    header("Location: index.php");
}

$result = null;
$founded = array();
$income = array();
$outcome = array();
$user->updateFriends();

//if user got friends
if ( isset($user->friends) ) {

    //loading basic info
    foreach ($user->friends as $friend) {

        $sql = "SELECT username, first_name, last_name, image, online " .
            "FROM members " .
            "WHERE username='" . $friend . "' ;";
        $query = mysqli_query($dbCon, $sql);

        if (!mysqli_affected_rows($dbCon)) {

        } else {
            $rowData = mysqli_fetch_row($query);

            $u = User::withUsername($rowData[0]);
            $u->firstname = $rowData[1];
            $u->lastname = $rowData[2];
            $u->image = $rowData[3];
            $lastLogged = $rowData[4];
            if ( round(abs(time() - $lastLogged) / 60,2) > 10) {
                $u->status = "offline";
            }
            array_push($founded, $u);
        }
    }
    $result = true;
}

//same with requests
if ( isset($user->incomingRequests) ) {

    foreach ($user->incomingRequests as $friend) {

        $sql = "SELECT username, first_name, last_name, image, online " .
            "FROM members " .
            "WHERE username='" . $friend . "' ;";
        $query = mysqli_query($dbCon, $sql);

        if (!mysqli_affected_rows($dbCon)) {

        } else {
            $rowData = mysqli_fetch_row($query);

            $u = User::withUsername($rowData[0]);
            $u->firstname = $rowData[1];
            $u->lastname = $rowData[2];
            $u->image = $rowData[3];
            $lastLogged = $rowData[4];
            if ( round(abs(time() - $lastLogged) / 60,2) > 10) {
                $u->status = "offline";
            }
            array_push($income, $u);
        }
    }
}

if ( isset($user->outgoingRequests) ) {

    foreach ($user->outgoingRequests as $friend) {

        $sql = "SELECT username, first_name, last_name, image, online " .
            "FROM members " .
            "WHERE username='" . $friend . "' ;";
        $query = mysqli_query($dbCon, $sql);

        if (!mysqli_affected_rows($dbCon)) {

        } else {
            $rowData = mysqli_fetch_row($query);

            $u = User::withUsername($rowData[0]);
            $u->firstname = $rowData[1];
            $u->lastname = $rowData[2];
            $u->image = $rowData[3];
            $lastLogged = $rowData[4];
            if ( round(abs(time() - $lastLogged) / 60,2) > 10) {
                $u->status = "offline";
            }
            array_push($outcome, $u);
        }
    }
}

$user->update(false);

include "View/friends.phtml";