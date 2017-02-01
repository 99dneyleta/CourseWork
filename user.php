<?php
session_start();

include_once("dbConnect.php");
include_once("functionality.php");
////////Global variables
$errors = null;
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
if ( isset($_FILES) && isset($_FILES['image'])) {
    $errors = proceedImageUpdate($user, 'image', $dbCon);
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo $user->getName() ?></title>
    <script>
        function validate_fileupload(fileName)
        {
            var allowed_extensions = new Array("jpg","png","gif");
            var file_extension = fileName.split('.').pop(); // split function will split the filename by dot(.), and pop function will pop the last element from the array which will give you the extension as well. If there will be no extension then it will return the filename.

            for(var i = 0; i <= allowed_extensions.length; i++)
            {
                if(allowed_extensions[i]==file_extension)
                {
                    return true; // valid file extension
                }
            }
            alert("Not valid file!");
            document.forms['Update']['image'].value = null;
            return false;
        }
    </script>
</head>

<body>
<div>
    <?php
        if ( $errors ) {
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
<a href="profileData.php">profileData</a>
<br><br>
<form name="Update" method="post" enctype="multipart/form-data" action="user.php" >

    <input type="file" id="image" name="image" onchange="validate_fileupload(this.value);">
    <br>
    <input type="submit" value="Update">
</form>
</body>
</html>