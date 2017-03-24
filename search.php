<?php
session_start();
include_once("Brain/functionality.php");

$user = getUser();
if (!isset($user)) {
    header("Location: index.php");
}

$result = null;
$founded = array();
if ( isset($_GET['search']) && trim(mysqli_real_escape_string($dbCon, $_GET['search'])) != "") {

    $searchString = mysqli_real_escape_string($dbCon, $_GET['search']);
    $sql = "SELECT username, first_name, last_name, image, online ".
           "FROM members ".
           "WHERE username LIKE '".$searchString."%' LIMIT 100;";
    $query = mysqli_query($dbCon, $sql);

    if ( !mysqli_affected_rows($dbCon) ) {
        $result = 1;
    } else {
        while($rowData = mysqli_fetch_array($query,MYSQLI_NUM)) {
            $exist = false;

            if ( strcmp($rowData[0], $user->username) == 0 ) {
                $exist = true;
                break;                                                  //    <<<<<<<<<  LOOK HERE !!!!!!!!
            }
            if ( !$exist ) {
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

    $sql = "SELECT username, first_name, last_name, image, online ".
        "FROM members ".
        "WHERE first_name LIKE '".$searchString."%' LIMIT 100;";
    $query = mysqli_query($dbCon, $sql);

    if ( !mysqli_affected_rows($dbCon) ) {
        $result = 2;
    } else {
        while($rowData = mysqli_fetch_array($query,MYSQLI_NUM)) {
            $exist = false;
            foreach ($founded as $usr) {
                if ( strcmp($rowData[0], $usr->username) == 0 || strcmp($rowData[0], $user->username) == 0 ) {
                    $exist = true;
                    break;                                                  //    <<<<<<<<<  LOOK HERE !!!!!!!!
                }
            }
            if ( strcmp($rowData[0], $user->username) == 0 ) {
                $exist = true;
                break;                                                  //    <<<<<<<<<  LOOK HERE !!!!!!!!
            }
            if ( !$exist ) {
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

    $sql = "SELECT username, first_name, last_name, image, online ".
        "FROM members ".
        "WHERE last_name LIKE '".$searchString."%' LIMIT 100;";
    $query = mysqli_query($dbCon, $sql);

    if ( !mysqli_affected_rows($dbCon) ) {
        $result = 3;
    } else {
        while($rowData = mysqli_fetch_array($query,MYSQLI_NUM)) {
            $exist = false;
            foreach ($founded as $usr) {
                if ( strcmp($rowData[0], $usr->username) == 0 || strcmp($rowData[0], $user->username) == 0) {
                    $exist = true;
                    break;                                                  //    <<<<<<<<<  LOOK HERE !!!!!!!!
                }
            }
            if ( strcmp($rowData[0], $user->username) == 0 ) {
                $exist = true;
                break;                                                  //    <<<<<<<<<  LOOK HERE !!!!!!!!
            }
            if ( !$exist ) {
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

    if ( $result ) {
        if ( isset($founded[0])) {
            $result = true;
        } else {
            $result = false;
        }
    }
}

$user->update(false);

include "View/search.phtml";