<?php
class User {
    var $username;
    var $uid = null;
    var $firstname = null;
    var $lastname = null;
    var $email;
    var $gender = null;
    var $image = null;
    var $status = "online";

    var $hobby = null;
    var $city = null;
    var $books = null;
    var $music = null;

    var $friends = array();
    var $outgoingRequests = array();
    var $incomingRequests = array();

    function __construct() {

    }

    public static function withUsername($username) {
        $instance = new self();
        $instance->username = $username;
        return $instance;
    }

    public static function withBasicStuff($username, $uid, $email) {
        $instance = new self();
        $instance->username = $username;
        $instance->uid = $uid;
        $instance->email = $email;
        return $instance;
    }

    function getName()
    {
        if (!isset($this->firstname) && !isset($this->lastname)) {
            return $this->username;
        } else {
            return $this->firstname . " " . $this->lastname;
        }
    }

    function toString() {
        return "".$this->firstname." ".$this->lastname." (".$this->username.")";
    }

    function update($dbCon, $forceUpdate) {
        if ( $forceUpdate == false && isset($_COOKIE['new'])) {
            return true;
        }
        $sql = "SELECT id, first_name, last_name, email, image, gender, hobby, city, books, music FROM members WHERE username = '$this->username' AND activated = '1' LIMIT 1";
        $query = mysqli_query($dbCon, $sql);
        $row = mysqli_fetch_row($query);
        if ( mysqli_error($dbCon) ) {
            return false;
        }
        $this->uid = $row[0];
        $this->firstname = $row[1];
        $this->lastname = $row[2];
        $this->email = $row[3];
        $this->image = $row[4];
        $this->gender = $row[5];
        $this->hobby = $row[6];
        $this->city = $row[7];
        $this->books = $row[8];
        $this->music = $row[9];

        generateSessionAndCookie($this);
        $this->getOnline($dbCon);
        setcookie("new", "old", time() + 600, "/");
    }

    function upgrade($dbCon) {
        $sql = "UPDATE members " .
            "SET ";

        if ( isset($this->firstname)){
            $sql = $sql . "first_name='".$this->firstname."', ";
        } else {
            $sql = $sql . "first_name=NULL, ";
        }
        if ( isset($this->lastname)){
            $sql = $sql . "last_name='".$this->lastname."', ";
        } else {
            $sql = $sql . "last_name=NULL, ";
        }
        if ( isset($this->gender)){
            $sql = $sql . "gender='".$this->gender."', ";
        } else {
            $sql = $sql . "gender=NULL, ";
        }
        if ( isset($this->image)){
            $sql = $sql . "image='".$this->image."', ";
        } else {
            $sql = $sql . "image=NULL, ";
        }
        if ( isset($this->email)) {
            $sql = $sql . "email='".$this->email."', ";
        } else {
            $sql = $sql . "email=NULL, ";
        }
        $sql = $sql . "username='".$this->username."' ".
                "WHERE id='$this->uid';";

        if( !mysqli_query($dbCon, $sql) ) {
            die($sql);
        }
        setcookie("new", "old", time() + 600, "/");
        return $this->getOnline($dbCon);
    }

    function upgradeDetail($dbCon) {
        $sql = "UPDATE members " .
            "SET ";

        if ( isset($this->hobby)){
            $sql = $sql . "hobby='".$this->hobby."', ";
        } else {
            $sql = $sql . "hobby=NULL, ";
        }
        if ( isset($this->city)){
            $sql = $sql . "city='".$this->city."', ";
        } else {
            $sql = $sql . "city=NULL, ";
        }
        if ( isset($this->books)){
            $sql = $sql . "books='".$this->books."', ";
        } else {
            $sql = $sql . "books=NULL, ";
        }
        if ( isset($this->music)){
            $sql = $sql . "music='".$this->music."' ";
        } else {
            $sql = $sql . "music=NULL ";
        }

        $sql = $sql . "WHERE id='$this->uid';";

        if( !mysqli_query($dbCon, $sql) ) {
            $this->hobby = null;
            $this->city = null;
            $this->books = null;
            $this->music = null;
            return $sql;
        }
        $this->getOnline($dbCon);
        return null;
    }

    function updateRequests($dbCon) {
        $this->incomingRequests = array();
        $this->outgoingRequests = array();
        $sql = "SELECT username FROM members m, requests r WHERE m.id = r.from_id AND r.to_id='$this->uid' AND r.decline=0 ;";
        $query = mysqli_query($dbCon, $sql);
        if ( !mysqli_affected_rows($dbCon) ) {
            $this->incomingRequests = array();
        } else {
            while($rowData = mysqli_fetch_array($query,MYSQLI_NUM)) {
                array_push($this->incomingRequests, $rowData[0]);
            }
        }

        $sql = "SELECT username FROM members m, requests r WHERE m.id = r.to_id AND r.from_id='$this->uid';";
        $query = mysqli_query($dbCon, $sql);
        if ( !mysqli_affected_rows($dbCon) ) {
            $this->outgoingRequests = array();
        } else {
            while($rowData = mysqli_fetch_array($query,MYSQLI_NUM)) {
                array_push($this->outgoingRequests, $rowData[0]);
            }
        }

        generateSessionAndCookie($this);
        $this->getOnline($dbCon);
        $this->update($dbCon, false);
        setcookie("new", "old", time() + 600, "/");
    }

    function sendRequest($dbCon, $uid, $username) {
        $this->updateFriends($dbCon);
        foreach ($this->friends as $friend) {
            if ( strtolower($friend) == strtolower($username)) {
                $this->removeRequest($dbCon, $uid, $username);
                return true;
            }
        }




        $sql = "SELECT decline FROM requests WHERE from_id='$this->uid' AND to_id='$uid' ;";
        $query = mysqli_query($dbCon, $sql);
        if ( isset(mysqli_fetch_row($query)[0]) ) {
            $this->updateRequests($dbCon);
            return false;
        }

        $sql = "SELECT decline FROM requests WHERE to_id='$this->uid' AND from_id='$uid' ;";
        $query = mysqli_query($dbCon, $sql);
        if ( isset(mysqli_fetch_row($query)[0]) ) {
            $this->acceptRequest($dbCon, $uid, $username);
            $this->updateRequests($dbCon);
            return false;
        }

        $sql = "INSERT INTO requests " .
            "(from_id, to_id) " .
            "VALUES ( '$this->uid','$uid' );";

        if (!mysqli_query($dbCon, $sql)) {
            die("Sorry, database is offline, try again later.");
        }

        array_push($this->outgoingRequests, $username);

        $this->getOnline($dbCon);
        return true;
    }

    function removeRequest($dbCon, $uid, $username) {
        $this->getOnline($dbCon);

        $sql = "SELECT decline FROM requests WHERE from_id='$this->uid' AND to_id='$uid' ;";
        $query = mysqli_query($dbCon, $sql);
        if ( mysqli_fetch_row($query)[0] == "1" ) {
            return false;
        }


        $sql = "DELETE FROM requests " .
            "WHERE from_id='$this->uid' AND to_id='$uid' ;";

        if (!mysqli_query($dbCon, $sql)) {
            die("Sorry, database is offline, try again later.");
        }
        $sql = "DELETE FROM requests " .
            "WHERE to_id='$this->uid' AND from_id='$uid' ;";

        if (!mysqli_query($dbCon, $sql)) {
            die("Sorry, database is offline, try again later.");
        }

        $i = count($this->outgoingRequests);
        while ($i) {
            $x = array_shift($this->outgoingRequests);
            if ( $x != $username) {
                array_push($this->outgoingRequests, $x);
            }
            $i--;
        }

    }

    function acceptRequest($dbCon, $uid, $username) {

        $this->updateFriends($dbCon);
        foreach ($this->friends as $friend) {
            if ( strtolower($friend) == strtolower($username)) {
                $this->removeRequest($dbCon, $uid, $username);
                return true;
            }
        }


        $sql = "SELECT decline FROM requests WHERE from_id='$this->uid' AND to_id='$uid' ;";
        $query = mysqli_query($dbCon, $sql);
        if ( mysqli_fetch_row($query)[0] == "1" ) {
            $i = count($this->incomingRequests);
            while ($i) {
                $x = array_shift($this->incomingRequests);
                if ( $x != $username) {
                    array_push($this->incomingRequests, $x);
                }
                $i--;
            }
            return false;
        }

        $sql = "SELECT friends FROM members WHERE id='$uid' ;";
        $friends = explode("|",mysqli_fetch_row(mysqli_query($dbCon, $sql))[0]);
        $i = count($friends);
        while ($i) {
            $x = array_shift($friends);
            if ( $x != $username) {
                array_push($friends, $x);
            }
            $i--;
        }
        array_push($friends, $this->username);
        $friends = implode("|", $friends);
        $sql = "UPDATE members SET friends='$friends' WHERE id='$uid' ;";
        if ( !mysqli_query($dbCon, $sql) ) {
            die("Full!");
        }

        $this->removeRequest($dbCon, $uid, $username);
        array_push($this->friends, $username);
        $this->upgradeFriends($dbCon);

        $this->getOnline($dbCon);

    }

    function declineRequest($dbCon, $uid, $username) {
        $sql = "UPDATE requests SET decline=1 WHERE from_id='$uid' AND to_id='$this->uid' ;";
        if ( !mysqli_query($dbCon, $sql)) {
            die("DataBase is offline, try again later");
        }

        $i = count($this->incomingRequests);
        while ($i) {
            $x = array_shift($this->incomingRequests);
            if ( $x != $username) {
                array_push($this->incomingRequests, $x);
            }
            $i--;
        }
        $this->getOnline($dbCon);
    }

    function removeFromFriends($dbCon, $uid, $username) {
        $i = count($this->friends);
        while ($i>=0) {
            $x = array_shift($this->friends);
            if ( strtolower($x) != strtolower($username)) {
                array_push($this->friends, $x);
            }
            $i--;
        }


        $sql = "INSERT INTO requests " .
            "(to_id, from_id) " .
            "VALUES ( '$this->uid','$uid' );";

        if (!mysqli_query($dbCon, $sql)) {
            die("Sorry, database is offline, try again later.");
        }

        array_push($this->incomingRequests, $username);

        $sql = "SELECT friends FROM members WHERE id='$uid' ;";
        $friends = explode("|",mysqli_fetch_row(mysqli_query($dbCon, $sql))[0]);
        $i = count($friends);
        while ($i) {
            $x = array_shift($friends);
            if ( strtolower($x) != strtolower($this->username)) {
                array_push($friends, $x);
            }
            $i--;
        }
        $friends = implode("|", $friends);
        $sql = "UPDATE members SET friends='$friends' WHERE id='$uid' ;";
        if ( !mysqli_query($dbCon, $sql) ) {
            die("Full!");
        }

        $this->upgradeFriends($dbCon);

    }

    function updateFriends($dbCon) {
        $sql = "SELECT friends FROM members WHERE id='$this->uid' ;";
        $query = mysqli_query($dbCon, $sql);
        $row = mysqli_fetch_row($query);
        $this->friends = explode("|", $row[0]);
        $this->updateRequests($dbCon);
        $this->getOnline($dbCon);
    }

    function upgradeFriends($dbCon) {
        $sql = "UPDATE members SET friends='".implode("|", $this->friends)."' WHERE id='$this->uid' ;";
        if ( !mysqli_query($dbCon, $sql)) {
            die("DataBase is offline, try again later");
        }
        $this->getOnline($dbCon);
    }

    function getOnline($dbCon) {
        if (  isset($_COOKIE['new'])) {
            return true;
        }
        $sql = "UPDATE members " .
            "SET online='".time()."'".
            "WHERE username='$this->username';";
        if( !mysqli_query($dbCon, $sql) ) {
            return false;
        }
        return true;
    }



    function getConversationWithUser($user, $dbCon) {
        return Conversation::withUser($user, $this, $dbCon);
    }

}

class Message {
    var $id = null;
    var $fromUser = null;
    var $toUser = null;
    var $time = null;
    var $read = null;
    var $text = null;
    var $attachment = null;

    function __construct($id, $fromUser, $toUser, $time, $text, $att, $read)
    {
        $this->id = $id;
        $this->fromUser = $fromUser;
        $this->toUser = $toUser;
        $this->time = $time;
        $this->attachment = $att;
        $this->read = $read;
    }

    //TODO: toString() for JS
}

class Conversation {
    var $me = null;
    var $interlocutor = null;
    var $messages = array();

    function __construct() { }

    public static function withUser($user, $me, $dbCon) {
        $instance = new self();
        $instance->interlocutor = $user;
        $instance->me = $me;
        $instance->loadBasics($dbCon);
        return $instance;
    }

    function loadBasics($dbCon) {

        $myMess = array();
        $toMeMess = array();

        $sql = "SELECT id, text, attach, time, read FROM messages WHERE from_id=".$this->me->uid." AND to_id=".$this->interlocutor->uid." ODRER BY id DESC LIMIT 20;";
        $query = mysqli_query($dbCon, $sql);
        while ( $mess = mysqli_fetch_array($query, MYSQLI_NUM) ) {
            //getting all my messages
            array_push($myMess, new Message($mess[0], $this->me, $this->interlocutor, $mess[3], $mess[1], $mess[2], $mess[4]));
        }

        $sql = "SELECT id, text, attach, time, read FROM messages WHERE to_id=".$this->me->uid." AND from_id=".$this->interlocutor->uid." ODRER BY id DESC LIMIT 20;";
        $query = mysqli_query($dbCon, $sql);
        while ( $ess = mysqli_fetch_array($query, MYSQLI_NUM) ) {
            //getting all messages to me
            array_push($toMeMess, new Message($mess[0], $this->me, $this->interlocutor, $mess[3], $mess[1], $mess[2], $mess[4]));
        }

        if ( !count($myMess) ) {
            if ( !count($toMeMess) ) {
                return ;
            } else {
                $this->messages = $toMeMess;
            }
        }
        if ( !count($toMeMess) ) {
            if ( !count($myMess) ) {
                return ;
            } else {
                $this->messages = $myMess;
            }
        }

        //merge sort between messages
        while (count($myMess) && count($toMeMess)) {
            $x = array_shift($myMess);
            $y = array_shift($toMeMess);

            ///////here time is 'date('d-m-Y; H:i:s',time());', comparable

            if ( $x->time < $y->time ){
                array_push($this->messages, $x );
                array_unshift($toMeMess, $y);
            } else {
                array_push($this->messages, $y );
                array_unshift($myMess, $x);
            }
        }
    }

    //TODO: add message, read message, delete message
}



function generateSessionAndCookie($user) {
    $_SESSION['user'] = serialize($user);
    setcookie("user", serialize($user), time() + (86400 * 1), "/"); // 86400 = 1 day
}

function getUser() {
    if (isset($_SESSION['user'])) {
        return unserialize($_SESSION['user']);
    } elseif (isset($_COOKIE['user'])) {
        return unserialize($_COOKIE['user']);
    }
    return null;

}

function clearSessionAndCookie() {
    session_destroy();
    if (isset($_COOKIE['user'])) {
        unset($_COOKIE['user']);
        setcookie('user', '', time() - 3600, '/'); // empty value and old timestamp
    }
    if (isset($_COOKIE['new'])) {
        unset($_COOKIE['new']);
        setcookie('new', '', time() - 3600, '/'); // empty value and old timestamp
    }
}

function generateSession($user) {
    session_start();
    session_destroy();
    session_start();
    $_SESSION['user'] = serialize($user);
}

function generateCookie($user) {
    setcookie("user", serialize($user), time() + (86400 * 1), "/"); // 86400 = 1 day
}

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function proceedImageUpdate($user, $image, $dbCon) {

    if (isset($_FILES) && isset($_FILES[$image])){
        if ( $_FILES[$image]['error'] != "0") {
            return $_FILES[$image]['error'];
        }

        $file_name = $_FILES[$image]['name'];
        $file_size = $_FILES[$image]['size'];
        $file_tmp = $_FILES[$image]['tmp_name'];
        $dot = '.';
        $split = explode($dot,$file_name);
        $ex = end($split);
        $file_ext=strtolower($ex);

        $extensions= array("jpeg","jpg","png");

        if(in_array($file_ext,$extensions)=== false){
            return "extension not allowed, please choose a JPEG or PNG file.";
        }

        if($file_size > 10485760) {
            return 'File size must be less than 10 MB';
        }

        if(empty($errors)==true) {
            $file_name = generateRandomString() . '.' . $file_ext;

            if (!move_uploaded_file($file_tmp, "images/" . $file_name)) {
                return "Cannot move " . $_FILES['image']['tmp_name'] . " to " . "/Users/Denis/" . $file_name;
            }

            //Check if file was before
            $oldFilename = "images/" . $user->image;
            if (file_exists($oldFilename)) {
                unlink($oldFilename);
            }

            $user->image = $file_name;
            generateSessionAndCookie($user);

            $uid = $user->uid;

            $sql = "UPDATE members " .
                "SET image='$file_name' " .
                "WHERE id='$uid';";

            if (!mysqli_query($dbCon, $sql)) {
                return "SQL";
            }
        }
    }
    return null;
}

function getAlert($strong, $msg) {
    $result = '<div class="alert" id="alert">'.
              '<span class="closebtn" onclick="this.parentElement.style.display='.'\'none\''.';">&times;</span>'.
              '<strong>'.$strong.' </strong> '.$msg.'</div>';
    return $result;
}
?>