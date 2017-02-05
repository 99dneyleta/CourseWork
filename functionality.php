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
        return null;
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