<?php
session_start();
include_once("functionality.php");
include_once("dbConnect.php");

$user = getUser();
if (!isset($user)) {
    header("Location: index.php");
}
$usr = null;
if ( !isset($_GET['username'])) {
    die("Go away");
} else {
    $username = trim(mysqli_real_escape_string($dbCon, $_GET['username']));
    $sql = "SELECT first_name, last_name, image, gender, city, online FROM members WHERE username = '$username' AND activated = '1' LIMIT 1";
    $query = mysqli_query($dbCon, $sql);
    $row = mysqli_fetch_row($query);
    if ( count($row) == 0) {
        die("Go away");
    }
    if ( mysqli_error($dbCon) ) {
        die("Go away");
    }
    $usr = User::withUsername($username);
    $usr->firstname = $row[0];
    $usr->lastname = $row[1];
    $usr->image = $row[2];
    $usr->gender = $row[3];
    $usr->city = $row[4];

    $lastLogged = $row[5];

    if ( round(abs(time() - $lastLogged) / 60,2) > 10) {
        $usr->status = "offline";
    }
}

if ( isset($_POST['request'])) {
    $req = $_POST['request'];

}

$user->update($dbCon, false);
?>


<!Doctype html>
<html>
<head>
    <title>Info</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="/base.css?v=<?=time();?>">
    <link rel="stylesheet" href="/profile.css?v=<?=time();?>">
    <style>
        ::-webkit-input-placeholder { /* WebKit browsers input color*/
            color:    black;
        }
        .text {
            max-width: 96%;
        }
    </style>

</head>

<body>

<!-- HERE GOES HEADER-->

<header style="margin-bottom: 50px;">
    <div class="dash"><a href="search.php?search=<?php echo $_GET['query']; ?>"><img src="img/back.svg"></a></div>
    <div class="header-title" id="title" style="display: block; left: 44vw;">Info</div>
    <div class = "search"><a href="search.php"><img class="search" src="img/search.svg"></a></div>
</header>
<div class="header-empty"></div>

<!-- HERE ENDS HEADER -->

<div id = "container-for-photo">
    <div class="profile-image">
        <img class="profile-image" src=" <?php if ( isset($usr->image) ) {echo "images/".$usr->image;} else {echo "/img/space-for-avatar.png";} ?> " >
    </div>
    <div class="status" >
        <?php echo $usr->status; ?>
    </div>

    <div class="all-info">
        <div class="text" >
            <?php if ( isset($usr->firstname)) {echo "First name: '".$usr->firstname."'";} ?>
        </div>
        <br>
        <div class="text" >
            <?php if ( isset($usr->lastname)) {echo "Last name: '".$usr->lastname."'";} ?>
        </div>
        <br>
        <div class="text" >
            <?php if ( isset($usr->username)) {echo "username: '".$usr->username."'";} ?>
        </div>
        <br>
        <div class="text" >
            <span><?php if ( isset($usr->city)) {echo "City: '".$usr->city."'";} ?></span>
        </div>
        <br>
        <div class="text" >
            <?php if ( isset($usr->gender) ) {echo $usr->gender;} ?>
        </div>
        <br>

    </div>



</div>

</body>
</html>