<?php
session_start();
include_once("functionality.php");

$user = getUser();
if (!isset($user)) {
    header("Location: index.php");
}

$user->update(false);

//load all conversation with current user
$interlocutor = null;
if ( !isset($_GET['user']) ) {
    die("Go! What are you thinking you are doing?");
} else {
    $interlocutor = User::withUsername($_GET['user']);
}

$conversation = $user->getConversationWithUser($interlocutor);

$confirm = $conversation->confirm;

if ( $confirm == 2 && !$conversation->reverse) {
    header("Location: chats.php");
}

?>

<!DOCTYPE html >
<html>
<head>
    <title>Chat</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="/chat.css?v=<?php echo time();?>">
    <link rel="stylesheet" href="/base.css?v=<?php echo time();?>">

    <script src="jquery-3.1.1.min.js"></script>
    <script>
        var lastId = 0;

        function sendMessage(form) {
            var text = form.textt.value;
            var me = form.myUid.value;
            var part = form.partUid.value;
            form.textt.value = "";

            var http = new XMLHttpRequest();
            var url = "writeMessage.php";
            var params = "uid="+me+"&partUid="+part+"&text="+text+"&attachment='null'";
            http.open("POST", url, true);

            //Send the proper header information along with the request
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            http.onreadystatechange = function() {//Call a function when the state changes.
                if(http.readyState == 4 && http.status == 200) {
                    if ( http.responseText ) {
                        var httpLog = new XMLHttpRequest();
                        var url = "addLog.php";
                        var params = "page='chat.php'&error='"+http.responseText+"'";
                        httpLog.open("POST", url, true);

                        //Send the proper header information along with the request
                        httpLog.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                        /*
                        http.onreadystatechange = function() {//Call a function when the state changes.
                            if(http.readyState == 4 && http.status == 200) {

                            }
                        };
                        */

                        httpLog.send(params);
                    }
                }
            };

            http.send(params);

            fetch();
            return false;
        }

        function allowConversation(form) {
            var me = form.myUid.value;
            var part = form.partUid.value;

            var http = new XMLHttpRequest();
            var url = "allowConversation.php";
            var params = "uid="+me+"&partUid="+part;
            http.open("POST", url, true);

            //Send the proper header information along with the request
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            http.onreadystatechange = function() {//Call a function when the state changes.
                if(http.readyState == 4 && http.status == 200) {

                    var httpLog = new XMLHttpRequest();
                    var url = "addLog.php";
                    var params = "page='chat.php(not confirm)'&error='"+http.responseText+"'";
                    httpLog.open("POST", url, true);

                    //Send the proper header information along with the request
                    httpLog.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                    /*
                     http.onreadystatechange = function() {//Call a function when the state changes.
                     if(http.readyState == 4 && http.status == 200) {

                     }
                     };
                     */

                    httpLog.send(params);
                }
            };

            http.send(params);

            form.setAttribute("onsubmit", "return sendMessage(this);");
            form2.butt.setAttribute("type", "hidden");
            form.textt.setAttribute("type", "text");
            return false;
        }

        function fetch() {
            var me = form.myUid.value;
            var part = form.partUid.value;

            var http = new XMLHttpRequest();
            var url = "fetchMessages.php";
            var params = "uid="+me+"&partUid="+part+"&lastId="+lastId;
            http.open("POST", url, true);

            //Send the proper header information along with the request
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            http.onreadystatechange = function() {//Call a function when the state changes.
                if(http.readyState == 4 && http.status == 200) {

                    var str = http.responseText;

                    var res = String(str.split("◊", 1));
                    var responseText = str.substring(res.length+1);



                    var objDiv = document.getElementById("chat");

                    objDiv.innerHTML += responseText;
                    if ( responseText != "") {
                        objDiv.scrollTop = objDiv.scrollHeight;
                    }
                    lastId = parseInt(res);
                    http = null;
                }
            };

            http.send(params);
        }

        $(document).ready(function () { $.ajaxSetup({cache:false}); setInterval(function () { fetch(); }, 1000)});

    </script>
</head>

<body>

<!-- HERE GOES MENU -->

<script>
    function show_menu() {
        var el = document.getElementById("menu");

        var title = document.getElementById("title");
        var dash = document.getElementById("dash");
        var dashes = document.getElementById("alone");
        if ( el.style.display == 'none' ) {

            el.style.display = 'inline';
            el.style.animation= 'moveforward 0.3s';
            el.style.webkitAnimationTimingFunction = 'ease-in';
            el.style.webkitAnimation = 'moveforward 0.3s';

            dash.style.animation= 'moveforward-dash 0.3s';
            dash.style.webkitAnimationTimingFunction = 'ease-in';
            dash.style.webkitAnimation = 'moveforward-dash 0.3s';

            title.style.animation= 'moveup-title 0.3s';
            title.style.webkitAnimationTimingFunction = 'ease-in';
            title.style.webkitAnimation = 'moveup-title 0.3s';

            dashes.className = "open";

            dash.style.left = '45vw';

            title.style.top = '-10vw';
        } else {

            el.style.animation= 'moveback 0.3s';
            el.style.webkitAnimationTimingFunction = 'ease-out';
            el.style.webkitAnimation = 'moveback 0.3s';

            dash.style.animation= 'moveback-dash 0.3s';
            dash.style.webkitAnimationTimingFunction = 'ease-out';
            dash.style.webkitAnimation = 'moveback-dash 0.3s';

            title.style.animation= 'movedown-title 0.3s';
            title.style.webkitAnimationTimingFunction = 'ease-out';
            title.style.webkitAnimation = 'movedown-title 0.3s';

            setTimeout(function(){
                el.style.display = 'none';
            }, 290);

            dashes.className = "";

            dash.style.left = '0';

            title.style.top = '0';
        }
    }
</script>
<div class="menu" id="menu" style="display: none;">
    <a href="profile.php">
        <img class="profile-image-tiny" src=" <?php if ( isset($user->image) ) {echo "images/".$user->image;} else {echo "img/space-for-avatar.png";} ?> " >
        <div class="menu_header"><?php if ( isset($user->username)) {echo $user->username;} else { echo "dev/null/";}?></div>
        <div class="menu_status">Online</div>
    </a>
    <a href="chats.php"><div class="menu_button"><img src="img/chats.svg" class="menu_image">Chats<?php if ( $user->hasPendingMessages() || $user->hasNewMessages()) { echo "<div id=notify></div>"; } ?></div></a>
    <a href="friends.php"><div class="menu_button"><img src="img/friends.svg" class="menu_image">Friends<?php if ( $user->hasPendingFriends()) { echo "<div id=notify></div>"; } ?></div></a>
    <a href="profileData.php"><div class="menu_button"><img src="img/settings.svg" class="menu_image">Settings</div></a>
    <a href="logout.php"><div class="menu_button"><img src="img/logout.svg" class="menu_image">Log out</div></a>
</div>

<!-- HERE ENDS MENU -->
<!-- HERE GOES HEADER-->

<header style="margin-bottom: 50px;">
    <div class="dash" id="dash" onclick="show_menu()">
        <div id="alone">
            <span></span>
            <span <?php if ( $user->hasNews()) { echo "style='background: #ff1001'"; } ?> ></span>
            <span></span>
        </div>

    </div>
    <div class="header-title" id="title" style="display: block; top: 0;"><?php echo $interlocutor->getName();?></div>
    <div class = "search"><a href="search.php"><img class="search" src="img/search.svg"></a></div>
</header>
<div class="header-empty"></div>

<!-- HERE ENDS HEADER -->

<div id="chat" ></div>

<?php
//echo ($conversation->reverse)? "rev, ".$confirm: "no, ". $confirm;
    if ( $confirm == 0 && $conversation->reverse ) {
        echo "
            <form id=\"form\" action=\"javascript:void(0);\" onsubmit=\"return allowConversation(this);\">
                <input type=\"submit\" id='confirmButt' name=\"butt\" class=\"allow\" value='Allow' >
                <input type=\"hidden\" id='confirm' name=\"textt\" class=\"inputMessage\" placeholder=\"your message\" >
                <input type=\"hidden\" name=\"myUid\" value=\"$user->uid\">
                <input type=\"hidden\" name=\"partUid\" value=\"$interlocutor->uid\">
            </form>
            <form id=\"form2\" action=\"denyConversation.php\" method='post'>
                <input type=\"submit\" id='confirmButt' name=\"butt\" class=\"deny\" value='Deny' >
                <input type=\"hidden\" id='confirm' name=\"textt\" class=\"inputMessage\" placeholder=\"your message\" >
                <input type=\"hidden\" name=\"uid\" value=\"$user->uid\">
                <input type=\"hidden\" name=\"partUid\" value=\"$interlocutor->uid\">
            </form>
        ";
    } else  {
        echo "
            <form id=\"form\" action=\"javascript:void(0);\" onsubmit=\"return sendMessage(this);\">
                <input type=\"text\" name=\"textt\" class=\"inputMessage\" placeholder=\"your message\" >
                <input type=\"hidden\" name=\"myUid\" value=\"$user->uid\">
                <input type=\"hidden\" name=\"partUid\" value=\"$interlocutor->uid\">
            </form>
        ";
    }

?>


<!--<a href="#" onclick="sendMessage()" style="width: 10vw; height: 20vw; font-size: 5vw;">send</a>-->
</body>
</html>
