<?php

include_once("dbConnect.php");

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
        $sql = "SELECT id FROM members WHERE username='$username' ;";
        if ( !$query = mysqli_query($GLOBALS['dbCon'], $sql)) {
            die("No such username");
        }
        $row = mysqli_fetch_row($query);
        $instance->uid = $row[0];
        $instance->updateBasic();
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

    public function __toString() {
        return "".$this->firstname." ".$this->lastname." (".$this->username.")";
    }

    function update($forceUpdate) {
        $this->getOnline();
        if ( $forceUpdate == false && isset($_COOKIE['new'])) {
            return true;
        }
        $sql = "SELECT id, first_name, last_name, email, image, gender, hobby, city, books, music, online FROM members WHERE username = '$this->username' AND activated = '1' LIMIT 1";
        $query = mysqli_query($GLOBALS['dbCon'], $sql);
        $row = mysqli_fetch_row($query);
        if ( mysqli_error($GLOBALS['dbCon']) ) {
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
        $lastLogged = $row[10];
        if ( round(abs(time() - $lastLogged) / 60,2) > 10) {
            $this->status = "offline";
        }

        //generateSessionAndCookie($this);

        //setcookie("new", "old", time() + 600, "/");
    }

    function updateBasic() {
        $sql = "SELECT username, first_name, last_name, image, gender, city, online FROM members WHERE id='$this->uid' AND activated = '1' LIMIT 1";
        if (!$query = mysqli_query($GLOBALS['dbCon'], $sql)) {
            return $sql;
        }
        $row = mysqli_fetch_row($query);
        if ( mysqli_error($GLOBALS['dbCon']) ) {
            return "updateBasic";
        }
        $this->username = $row[0];
        $this->firstname = $row[1];
        $this->lastname = $row[2];
        $this->image = $row[3];
        $this->gender = $row[4];
        $this->city = $row[5];
        $lastLogged = $row[6];
        if ( round(abs(time() - $lastLogged) / 60,2) > 10) {
            $this->status = "offline";
        }
    }

    function upgrade() {
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

        mysqli_query($GLOBALS['dbCon'], "SET NAMES UTF8");
        if( !mysqli_query($GLOBALS['dbCon'], $sql) ) {
            die(mysqli_error($GLOBALS['dbCon']));
        }
        setcookie("new", "old", time() + 600, "/");
        return $this->getOnline();
    }

    function upgradeDetail() {
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

        if( !mysqli_query($GLOBALS['dbCon'], $sql) ) {
            $this->hobby = null;
            $this->city = null;
            $this->books = null;
            $this->music = null;
            return $sql;
        }
        $this->getOnline();
        return null;
    }

    function updateRequests() {
        $this->incomingRequests = array();
        $this->outgoingRequests = array();
        $sql = "SELECT username FROM members m, requests r WHERE m.id = r.from_id AND r.to_id='$this->uid' AND r.decline=0 ;";
        $query = mysqli_query($GLOBALS['dbCon'], $sql);
        if ( !mysqli_affected_rows($GLOBALS['dbCon']) ) {
            $this->incomingRequests = array();
        } else {
            while($rowData = mysqli_fetch_array($query,MYSQLI_NUM)) {
                array_push($this->incomingRequests, $rowData[0]);
            }
        }

        $sql = "SELECT username FROM members m, requests r WHERE m.id = r.to_id AND r.from_id='$this->uid';";
        $query = mysqli_query($GLOBALS['dbCon'], $sql);
        if ( !mysqli_affected_rows($GLOBALS['dbCon']) ) {
            $this->outgoingRequests = array();
        } else {
            while($rowData = mysqli_fetch_array($query,MYSQLI_NUM)) {
                array_push($this->outgoingRequests, $rowData[0]);
            }
        }

        //generateSessionAndCookie($this);
        $this->getOnline();
        $this->update(false);
        //setcookie("new", "old", time() + 600, "/");
    }

    function sendRequest($uid, $username) {
        $this->updateFriends();
        foreach ($this->friends as $friend) {
            if ( strtolower($friend) == strtolower($username)) {
                $this->removeRequest($uid, $username);
                return true;
            }
        }




        $sql = "SELECT decline FROM requests WHERE from_id='$this->uid' AND to_id='$uid' ;";
        $query = mysqli_query($GLOBALS['dbCon'], $sql);
        if ( isset(mysqli_fetch_row($query)[0]) ) {
            $this->updateRequests();
            return false;
        }

        $sql = "SELECT decline FROM requests WHERE to_id='$this->uid' AND from_id='$uid' ;";
        $query = mysqli_query($GLOBALS['dbCon'], $sql);
        if ( isset(mysqli_fetch_row($query)[0]) ) {
            $this->acceptRequest($uid, $username);
            $this->updateRequests();
            return false;
        }

        $sql = "INSERT INTO requests " .
            "(from_id, to_id) " .
            "VALUES ( '$this->uid','$uid' );";

        if (!mysqli_query($GLOBALS['dbCon'], $sql)) {
            die("Sorry, database is offline, try again later.");
        }

        array_push($this->outgoingRequests, $username);

        $this->getOnline();
        return true;
    }

    function removeRequest($uid, $username) {
        $this->getOnline();

        $sql = "SELECT decline FROM requests WHERE from_id='$this->uid' AND to_id='$uid' ;";
        $query = mysqli_query($GLOBALS['dbCon'], $sql);
        if ( mysqli_fetch_row($query)[0] == "1" ) {
            return false;
        }


        $sql = "DELETE FROM requests " .
            "WHERE from_id='$this->uid' AND to_id='$uid' ;";

        if (!mysqli_query($GLOBALS['dbCon'], $sql)) {
            die("Sorry, database is offline, try again later.");
        }
        $sql = "DELETE FROM requests " .
            "WHERE to_id='$this->uid' AND from_id='$uid' ;";

        if (!mysqli_query($GLOBALS['dbCon'], $sql)) {
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

    function acceptRequest( $uid, $username) {

        $this->updateFriends();
        foreach ($this->friends as $friend) {
            if ( strtolower($friend) == strtolower($username)) {
                $this->removeRequest( $uid, $username);
                return true;
            }
        }


        $sql = "SELECT decline FROM requests WHERE from_id='$this->uid' AND to_id='$uid' ;";
        $query = mysqli_query($GLOBALS['dbCon'], $sql);
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
        $friends = explode("|",mysqli_fetch_row(mysqli_query($GLOBALS['dbCon'], $sql))[0]);
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
        if ( !mysqli_query($GLOBALS['dbCon'], $sql) ) {
            die("Full!");
        }

        $this->removeRequest($uid, $username);
        array_push($this->friends, $username);
        $this->upgradeFriends();

        $this->getOnline();

    }

    function declineRequest($uid, $username) {
        $sql = "UPDATE requests SET decline=1 WHERE from_id='$uid' AND to_id='$this->uid' ;";
        if ( !mysqli_query($GLOBALS['dbCon'], $sql)) {
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
        $this->getOnline();
    }

    function removeFromFriends($uid, $username) {
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

        if (!mysqli_query($GLOBALS['dbCon'], $sql)) {
            die("Sorry, database is offline, try again later.");
        }

        array_push($this->incomingRequests, $username);

        $sql = "SELECT friends FROM members WHERE id='$uid' ;";
        $friends = explode("|",mysqli_fetch_row(mysqli_query($GLOBALS['dbCon'], $sql))[0]);
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
        if ( !mysqli_query($GLOBALS['dbCon'], $sql) ) {
            die("Full!");
        }

        $this->upgradeFriends();

        if ( !$this->deleteConversationWith($uid) ) {
            $myfile = fopen("logs.txt", "a");
            $txt = "Delete conversation: ".$this." with ".$username."(time: ". date("d-m-Y; H:i:s", time()).")\n"."-----------------------------------\n";
            fputs($myfile, $txt);
            fclose($myfile);
        }

    }

    function updateFriends() {
        $sql = "SELECT friends FROM members WHERE id='$this->uid' ;";
        $query = mysqli_query($GLOBALS['dbCon'], $sql);
        $row = mysqli_fetch_row($query);
        $this->friends = explode("|", $row[0]);
        $this->updateRequests();
        $this->getOnline();
    }

    function upgradeFriends() {
        $sql = "UPDATE members SET friends='".implode("|", $this->friends)."' WHERE id='$this->uid' ;";
        if ( !mysqli_query($GLOBALS['dbCon'], $sql)) {
            die("DataBase is offline, try again later");
        }
        $this->getOnline();
    }

    function getOnline() {
        if (  isset($_COOKIE['new'])) {
            return true;
        }
        $sql = "UPDATE members " .
            "SET online='".date('d-m-Y; H:i:s',time())."'".
            "WHERE username='$this->username';";
        if( !mysqli_query($GLOBALS['dbCon'], $sql) ) {
            return false;
        }
        setcookie("new", "old", time() + 600, "/");
        return true;
    }

    function getAllConversations() {
        $all = array();

        $sql = "SELECT id FROM conversations WHERE (participant1=".$this->uid." OR participant2=".$this->uid.") AND confirm=1 ORDER BY last_time DESC;";

        if ( !$query = mysqli_query($GLOBALS['dbCon'], $sql)) {
            return null;
        } else {
            while ($row = mysqli_fetch_array($query, MYSQLI_NUM)) {
                array_push($all, $row[0]);
            }
        }
        $conv = array();

        foreach ($all as $id) {
            $con = Conversation::withID($this, $id);
            array_push($conv, $con);
        }

        return $conv;
    }

    function getPendingConversations() {
        $all = array();

        $sql = "SELECT id FROM conversations WHERE (participant1=".$this->uid." OR participant2=".$this->uid.") AND confirm=0 ORDER BY last_time DESC;";

        if ( !$query = mysqli_query($GLOBALS['dbCon'], $sql)) {
            return null;
        } else {
            while ($row = mysqli_fetch_array($query, MYSQLI_NUM)) {
                array_push($all, $row[0]);
            }
        }
        $conv = array();

        foreach ($all as $id) {
            $con = Conversation::withID($this, $id);
            array_push($conv, $con);
        }

        return $conv;
    }

    function isFriend($username) {
        foreach ($this->friends as $friend) {
            if ( $friend == $username) return true;
        }
        return false;
    }

    function getConversationWithUser($user) {
        return Conversation::withUser($user, $this);
    }

    function hasPendingFriends() {
        $this->updateRequests();
        return count($this->incomingRequests) > 0;
    }

    function hasPendingMessages() {
        $sql = "SELECT id FROM conversations WHERE (participant1=".$this->uid." OR participant2=".$this->uid.") AND confirm=0 ORDER BY last_time DESC;";

        if ( !$query = mysqli_query($GLOBALS['dbCon'], $sql)) {
            return null;
        } else {
            return mysqli_affected_rows($GLOBALS['dbCon']) > 0;
        }
    }

    function hasNewMessages() {
        $sql = "SELECT id FROM messages WHERE to_id=".$this->uid." AND readd IS NULL ;";

        if ( !$query = mysqli_query($GLOBALS['dbCon'], $sql)) {
            return null;
        } else {
            return mysqli_affected_rows($GLOBALS['dbCon']) > 0;
        }
    }

    function hasNews() {
        return $this->hasNewMessages() || $this->hasPendingFriends() || $this->hasPendingMessages();
    }

    function deleteConversationWith($id) {
        $sql = "DELETE FROM messages WHERE ( to_id=".$this->uid." AND from_id=".$id.") OR (from_id=".$this->uid." AND to_id=".$id.") ; ";
        if ( !mysqli_query($GLOBALS['dbCon'], $sql)) {
            return false;
        }
        $sql = "DELETE FROM conversations WHERE ( participant1=".$this->uid." AND participant2=".$id.") OR (participant2=".$this->uid." AND participant1=".$id.") ; ";
        if ( !mysqli_query($GLOBALS['dbCon'], $sql)) {
            return false;
        }

        return true;
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
        $this->text = $text;
    }

    function pushToDB() {
        $att = ($this->attachment)? "NULL" : "'$this->attachment'";
        $sql = "INSERT INTO messages (from_id, to_id, textt, attach, timee, readd) VALUES ('".$this->fromUser->uid."', '".$this->toUser->uid."', '$this->text', ".$att.", '$this->time', NULL) ;";
        if ( !mysqli_query($GLOBALS['dbCon'], $sql) ) {
            return $sql;
        }

        $sql = "SELECT id FROM messages WHERE from_id='".$this->fromUser->uid."' AND to_id='".$this->toUser->uid."' ORDER BY id DESC LIMIT 1; ";
        if ( !$query = mysqli_query($GLOBALS['dbCon'], $sql)) {
            return "cannot push!: ".$sql;
        }
        $row = mysqli_fetch_array($query);
        $this->id = $row[0];
    }
}

class Conversation {
    var $id = null;
    var $me = null;
    var $interlocutor = null;
    var $messages = array();
    var $confirm = null;
    var $reverse = false;

    function __construct() { }

    public static function withUser($user, $me) {
        $instance = new self();
        $instance->interlocutor = $user;
        $instance->me = $me;
        $instance->loadBasics();
        return $instance;
    }

    public static function withID($self, $id) {
        $instance = new self();
        $instance->id = $id;
        $instance->interlocutor = new User();
        $instance->me = $self;

        $sql = "SELECT participant1, participant2 FROM conversations WHERE id=".$instance->id." ;";
        if ( !$query = mysqli_query($GLOBALS['dbCon'], $sql)) {
            return null;
        } else {
            $res = mysqli_fetch_row($query);
            if ( $self->uid == $res[0]) {
                $instance->interlocutor->uid = $res[1];
            } else {
                $instance->interlocutor->uid = $res[0];
            }
        }

        $instance->interlocutor->updateBasic();


        //$instance->loadBasics();
        return $instance;
    }

    function loadBasics() {

        $myMess = array();
        $toMeMess = array();

        $sql = "SELECT id, textt, attach, timee, readd FROM messages WHERE from_id=".$this->me->uid." AND to_id=".$this->interlocutor->uid."  ORDER BY id DESC LIMIT 20;";
        if ( !$query = mysqli_query($GLOBALS['dbCon'], $sql)) {
            return "544: ".$sql;
        }

        while ( $mess = mysqli_fetch_array($query, MYSQLI_NUM) ) {
            //getting all my messages
            array_push($myMess, new Message($mess[0], $this->me, $this->interlocutor, $mess[3], $mess[1], $mess[2], $mess[4]));
        }

        $sql = "SELECT id, textt, attach, timee, readd FROM messages WHERE to_id=".$this->me->uid." AND from_id=".$this->interlocutor->uid." AND readd IS NOT NULL ORDER BY id DESC LIMIT 20;";
        if ( !$query = mysqli_query($GLOBALS['dbCon'], $sql)) {
            return "497";
        }

        while ( $mess = mysqli_fetch_array($query, MYSQLI_NUM) ) {
            //getting all messages to me
            array_push($toMeMess, new Message($mess[0], $this->interlocutor, $this->me, $mess[3], $mess[1], $mess[2], $mess[4]));
        }

        $result = "to('$this->me'): ".count($toMeMess)." from('$this->interlocutor') : ".count($myMess)."  ";

        mergeSort($myMess, $toMeMess, $this->messages);

        $result .= "afterMerge: " . count($this->messages);

        $sql = "SELECT id, confirm FROM conversations WHERE participant1='".$this->me->uid."' AND participant2='".$this->interlocutor->uid."' ;";
        if ( !$query = mysqli_query($GLOBALS['dbCon'], $sql)) {
            return "Load Basics: ".$sql;
        }
        if ( mysqli_affected_rows($GLOBALS['dbCon']) ) {
            $row = mysqli_fetch_row($query);
            $this->id = $row[0];
            $this->confirm = $row[1];
            $this->reverse = false;
        } else {

            $sql = "SELECT id, confirm FROM conversations WHERE participant2='" . $this->me->uid . "' AND participant1='" . $this->interlocutor->uid . "' ;";
            if ( !$query = mysqli_query($GLOBALS['dbCon'], $sql)) {
                return "Load Basics: ".$sql;
            }
            if (mysqli_affected_rows($GLOBALS['dbCon'])) {
                $row = mysqli_fetch_row($query);
                $this->id = $row[0];
                $this->confirm = $row[1];
                $this->reverse = true;
            } else {

                $sql = "INSERT INTO conversations (participant1, participant2, last_time, confirm) VALUES ('" . $this->me->uid . "', '" . $this->interlocutor->uid . "', '".date('d-m-Y; H:i:s',time())."' , 0) ;";
                if (!mysqli_query($GLOBALS['dbCon'], $sql)) {
                    return "524: " . $sql;
                }

                $sql = "SELECT id, confirm FROM conversations WHERE participant1='" . $this->me->uid . "' AND participant2='" . $this->interlocutor->uid . "' ;";
                if (!$query = mysqli_query($GLOBALS['dbCon'], $sql)) {
                    return "529";
                }
                if (mysqli_affected_rows($GLOBALS['dbCon'])) {
                    $row = mysqli_fetch_row($query);
                    $this->id = $row[0];
                    $this->confirm = $row[1];
                    $this->reverse = false;
                } else {
                    return "DataBase is offline now, try again later";
                }
            }
        }

        return $result . $this->loadUnread();
    }

    function loadUnread() {
        $unread = array();

        $sql = "SELECT id, textt, attach, timee, readd FROM messages WHERE to_id=".$this->me->uid." AND from_id=".$this->interlocutor->uid." AND readd IS NULL ORDER BY id DESC;";
        if ( !$query = mysqli_query($GLOBALS['dbCon'], $sql)) {
            return "Unread: ".$sql;
        }
        while ( $mess = mysqli_fetch_array($query, MYSQLI_NUM) ) {
            //getting all my messages
            array_push($unread, new Message($mess[0], $this->interlocutor, $this->me, $mess[3], $mess[1], $mess[2], $mess[4]));
        }

        $loadedMess = $this->messages;
        $this->messages = array();

        mergeSort($unread, $loadedMess, $this->messages);

        $sql = "UPDATE messages SET readd='".date('d-m-Y; H:i:s',time())."' WHERE to_id=".$this->me->uid." AND from_id=".$this->interlocutor->uid." ;";
        if ( !mysqli_query($GLOBALS['dbCon'], $sql)) {
            return "unread update: ".$sql;
        }


        return " after unread: ". count($this->messages);
    }

    function addMessage($text, $att) {
        if ( ($this->confirm == 0 && $this->reverse) || ($this->confirm == 2 && !$this->reverse ) ){
            echo "not confirmed!";
            return ;
        }
        $mess = new Message(0, $this->me, $this->interlocutor, date('d-m-Y; H:i:s',time()), $text, $att, null);
        $mess->pushToDB();

        $sql = "UPDATE conversations SET last_time='".date('d-m-Y; H:i:s',time())."', confirm=1 WHERE id=".$this->id." ;";
        if ( !mysqli_query($GLOBALS['dbCon'], $sql) ) {
            echo $sql;
        }

        array_unshift($this->messages, $mess);
    }

    function deleteMessage($messId) {
        $sql = "DELETE FROM messages WHERE id='$messId' ;";
        mysqli_query($GLOBALS['dbCon'], $sql);

        $i = count($this->messages);
        while ($i) {
            $x = array_shift($this->messages);
            if ( $x->id != $messId ){
                array_push($this->messages, $x);
            }
            --$i;
        }
    }

    function howMuchUnread() {
        $sql = "SELECT id FROM messages WHERE to_id=".$this->me->uid." AND from_id=".$this->interlocutor->uid." AND readd IS NULL;";
        if ( !$query = mysqli_query($GLOBALS['dbCon'], $sql)) {
            return null;
        } else {
            return mysqli_affected_rows($GLOBALS['dbCon']);
        }
    }

    function lastMessage() {
        $sql = "SELECT textt FROM messages WHERE (from_id=".$this->me->uid." AND to_id=".$this->interlocutor->uid." ) OR (to_id=".$this->me->uid." AND from_id=".$this->interlocutor->uid." ) ORDER BY id DESC LIMIT 1;";
        if ( !$query = mysqli_query($GLOBALS['dbCon'], $sql)) {
            echo "678: ".$sql;
        }
        $text = mysqli_fetch_row($query)[0];
        if ( strlen($text) > 10 ) {
            $text = substr($text, 0, 10)."...";
        }
        return $text;
    }

    function gotConfirmation() {
        $sql = "UPDATE conversations SET confirm=1 WHERE id=".$this->id." ;";
        if ( !mysqli_query($GLOBALS['dbCon'], $sql)) {
            return "sql: cannot confirm! ";
        }
    }

    function denyConfirmation() {
        $sql = "UPDATE conversations SET confirm=2 WHERE id=".$this->id." ;";
        if ( !mysqli_query($GLOBALS['dbCon'], $sql)) {
            return "sql: cannot deny! ";
        }
        $sql = "DELETE FROM messages WHERE (from_id=".$this->interlocutor->uid." AND to_id=".$this->me->uid." ) OR (to_id=".$this->interlocutor->uid." AND from_id=".$this->me->uid." ) ;";
        if ( !mysqli_query($GLOBALS['dbCon'], $sql)) {
            return "sql: cannot delete! ";
        }
    }

    public function __toString() {
        return $this->me." conversation(id='$this->id') with ".$this->interlocutor. "\nTotal mess: " . count($this->messages);
    }

}



function generateSessionAndCookie($user) {
    $_SESSION['user'] = serialize($user);
    setcookie("user", serialize($user), time() + (86400 * 1), "/"); // 86400 = 1 day
    $user->getOnline();
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

function proceedImageUpdate($user, $image) {

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
            //generateSessionAndCookie($user);

            $uid = $user->uid;

            $sql = "UPDATE members " .
                "SET image='$file_name' " .
                "WHERE id='$uid';";

            if (!mysqli_query($GLOBALS['dbCon'], $sql)) {
                die("DataBase is offline");
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

function mergeSort(&$first, &$second, &$output) {
    if ( !count($first) ) {
        if ( !count($second) ) {
            return ;
        } else {
            $output = $second;
            return ;
        }
    }
    if ( !count($second) ) {
        if ( !count($first) ) {
            return ;
        } else {
            $output = $first;
            return ;
        }
    }

    //merge sort between messages
    while (count($first) && count($second)) {
        $x = array_shift($first);
        $y = array_shift($second);

        ///////here time is 'date('d-m-Y; H:i:s',time());', comparable

        if ( $x->time > $y->time ){
            array_push($output, $x );
            array_unshift($second, $y);
        } else {
            array_push($output, $y );
            array_unshift($first, $x);
        }
    }

    while (count($first)) {
        $x = array_shift($first);
        array_push($output, $x);
    }

    while (count($second)) {
        $x = array_shift($second);
        array_push($output, $x);
    }

}
?>