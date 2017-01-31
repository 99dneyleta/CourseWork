<?php
class User {
    var $username;
    var $uid;
    var $firstname = null;
    var $lastname = null;
    var $email;
    var $image = null;

    function __construct($username, $uid, $email ) {
        $this->username = $username;
        $this->uid = $uid;
        $this->email = $email;

    }

    function getUsername() {
        return $this->username;
    }

    function getUid() {
        return $this->uid;
    }

    function getImage() {
        return $this->image;
    }

    function toString() {
        return "".$this->firstname." ".$this->lastname." (".$this->username.")";
    }

    function update($dbCon, $forceUpdate) {
        if ( $forceUpdate == false && isset($_COOKIE['new'])) {
            return ;
        }
        $sql = "SELECT id, first_name, last_name, email, image FROM members WHERE username = '$this->username' AND activated = '1' LIMIT 1";
        $query = mysqli_query($dbCon, $sql);
        $row = mysqli_fetch_row($query);
        $this->uid = $row[0];
        $this->firstname = $row[1];
        $this->lastname = $row[2];
        $this->email = $row[3];
        $this->image = $row[4];

        generateSessionAndCookie($this);

        setcookie("new", "old", time() + 3600, "/");
    }
}

function generateSessionAndCookie($user) {
    $_SESSION['user'] = serialize($user);
    setcookie("user", serialize($user), time() + (86400 * 30), "/"); // 86400 = 1 day
}

function clearSessionAndCookie() {
    session_destroy();
    if (isset($_COOKIE['user'])) {
        unset($_COOKIE['user']);
        setcookie('user', '', time() - 3600, '/'); // empty value and old timestamp
    }
}

function generateSession($user) {
    session_start();
    session_destroy();
    session_start();
    $_SESSION['user'] = serialize($user);
}

function generateCookie($user) {
    setcookie("user", serialize($user), time() + (86400 * 30), "/"); // 86400 = 1 day
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
                echo "<img src='" . $_FILES['image']['tmp_name'] . "." . $file_ext . "'><br>";
                //var_dump(is_uploaded_file($_FILES["image"]["tmp_name"]));
                return "Cannot move " . $_FILES['image']['tmp_name'] . " to " . "/Users/Denis/" . $file_name;
            }


            //Check if file was before
            $oldFilename = "images/" . $user->getImage();
            if (file_exists($oldFilename)) {
                unlink($oldFilename);
            }

            $user->image = $file_name;
            generateSessionAndCookie($user);

            $uid = $user->getUid();

            $sql = "UPDATE members " .
                "SET image='$file_name' " .
                "WHERE id='$uid';";

            if (!mysqli_query($dbCon, $sql)) {
                return "SQL";
            }
        }
    }
    return "0";
}
?>