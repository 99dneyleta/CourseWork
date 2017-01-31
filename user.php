<?php
session_start();

include_once("dbConnect.php");
include_once("functionality.php");
////////Global variables
$errors = 0;
$user = null;
$result = null;


if (isset($_SESSION['user'])) {
    // Put stored session variables into local PHP variable
    $user = unserialize($_SESSION['user']);
    $user->update($dbCon, true);
    $result = "Test variables: <br /> User: ".$user->ToString();
} else if (isset($_COOKIE['user'])) {
    // Put stored session variables into local PHP variable
    $user = unserialize($_COOKIE['user']);
    generateSession($user);
    header("Location: user.php");
} else  {
    header("Location: index.php");
}

proceedImageUpdate($user, 'image', $dbCon);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo $user->getUsername() ;?> - Test Site</title>
</head>

<body>
<div>
    <?php
        if ( $errors != "0") {
            echo "<h1> '$errors'</h1>";
        }
        $img = $user->image;
        if ( isset($img) ){
            echo "<img src='./images/".$img."'>";
        }
    ?>
</div>
<?php
echo $result;
?>
<br>
<a href="logout.php">logout</a>
<br><br>
<form method="post" enctype="multipart/form-data" action="user.php" >

    <input type="file" name="image">
    <br>
    <input type="submit" value="Update">
</form>
</body>
</html>