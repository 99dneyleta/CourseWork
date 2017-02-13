<?php
//session start - standard syntax for user-interface web page
session_start();
include_once("functionality.php");

//getting user from cache or session
$user = getUser();
if (!isset($user)) {
    // if user do not exist, locate page to load index.php
    header("Location: index.php");
}
//update some basic info
$user->update(false);

//load all conversation with current user
//in case you haven't opened this page properly (with right parameter) I stops loading page and display only some text
$interlocutor = null;
if ( !isset($_GET['user']) ) {
    die("Go! What are you thinking you are doing?");
} else {
    //getting username from field by method GET
    $interlocutor = User::withUsername($_GET['user']);
}

//creating conversation
$conversation = $user->getConversationWithUser($interlocutor);

//getting confirmation sign
$confirm = $conversation->confirm;

//if not permitted to write go to hell
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

        /*
         * Some magic
         * Using JS to upload message to server via php with glue - POST method
         * copied from internet, don't ask, how it works
         */
        function sendMessage(form) {
            //interested in those fields from form
            var text = form.textt.value;
            var me = form.myUid.value;
            var part = form.partUid.value;
            form.textt.value = "";

            var http = new XMLHttpRequest();

            //setting server's program url
            var url = "writeMessage.php";
            var params = "uid="+me+"&partUid="+part+"&text="+text+"&attachment='null'";

            //opening connection
            http.open("POST", url, true);

            //Send the proper header information along with the request
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            http.onreadystatechange = function() {//Call a function when the state changes.
                if(http.readyState == 4 && http.status == 200) {

                    //in case respond arrived:
                    if ( http.responseText ) {
                        //sending all respond to log again via php+post
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

            //calling to fetch new mess
            fetch();

            //returning false, so page not refreshing because I'm using form
            return false;
        }

        //in case user received a request to chat and decided to allow
        //the same as in previous function
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

        //function to fetch new messages, same as last 2  functions
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

                    //difference is here:
                    //I use response text to pass 2 things: id of last fetched message (so I know next time what messages not to transfer)
                    // and new messages, already pre-styled
                    //so all I need is to separate this 2


                    var str = http.responseText;

                    //getting last id as string because of next step res.length
                    var res = String(str.split("◊", 1));

                    //getting everything else as substring
                    var responseText = str.substring(res.length+1);

                    //getting my chat div
                    var objDiv = document.getElementById("chat");

                    //adding my new mess
                    objDiv.innerHTML += responseText;

                    //scrolling down in case of new messages
                    if ( responseText != "") {
                        objDiv.scrollTop = objDiv.scrollHeight;
                    }

                    //getting lastId from string
                    lastId = parseInt(res);

                    //my way of deleting request from memory
                    http = null;
                }
            };

            http.send(params);
        }

        //here is a jQuery, included in header
        //if you know how to launch a loop after page loaded please do
        //I'm using function ready, that always do something
        //In this case I created some lame function without name in which I removing cache (as I think) ang fetching new mess
        //again, it's a loop, so without right timing I will kill page and server with query
        //so I'm using function set interval, that execute code inside after time T - second parameter
        //1000 = 1 sec

        $(document).ready( function () { $.ajaxSetup({cache:false}); setInterval(function () { fetch(); }, 1000)} );

    </script>

    <script src="jquery-1.11.3.min.js"></script>
    <script src="jquery.mobile-1.4.5.min.js"></script>
    <script>
        $(document).on("pagecreate","body",function(){
            $("body").on("swipe",function(){
                show_menu();
            });
        });
    </script>
</head>

<body>

<!-- HERE GOES MENU -->

<script>
    //pretty standard function, I'm using this always when needed a menu to display or hide
    function show_menu() {
        //getting all elements that needed

        var el = document.getElementById("menu");
        var title = document.getElementById("title");
        var dash = document.getElementById("dash");
        var dashes = document.getElementById("alone");

        //in case i want to show menu
        if ( el.style.display == 'none' ) {

            // showing menu!
            el.style.display = 'inline';
            //and, of course, here goes animation, included in css
            el.style.animation= 'moveforward 0.3s';
            el.style.webkitAnimationTimingFunction = 'ease-in';
            el.style.webkitAnimation = 'moveforward 0.3s';

            //and for dashes
            dash.style.animation= 'moveforward-dash 0.3s';
            dash.style.webkitAnimationTimingFunction = 'ease-in';
            dash.style.webkitAnimation = 'moveforward-dash 0.3s';

            //and title
            title.style.animation= 'moveup-title 0.3s';
            title.style.webkitAnimationTimingFunction = 'ease-in';
            title.style.webkitAnimation = 'moveup-title 0.3s';

            //here is class for dashes "x" figure
            dashes.className = "open";

            //a little bit to the right
            dash.style.left = '45vw';

            //hiding title in this case, in another - just moving around
            title.style.top = '-10vw';
        } else {
            //in case I want to hide menu:

            el.style.animation= 'moveback 0.3s';
            el.style.webkitAnimationTimingFunction = 'ease-out';
            el.style.webkitAnimation = 'moveback 0.3s';

            dash.style.animation= 'moveback-dash 0.3s';
            dash.style.webkitAnimationTimingFunction = 'ease-out';
            dash.style.webkitAnimation = 'moveback-dash 0.3s';

            title.style.animation= 'movedown-title 0.3s';
            title.style.webkitAnimationTimingFunction = 'ease-out';
            title.style.webkitAnimation = 'movedown-title 0.3s';

            //must have some time for animation before hiding menu
            setTimeout(function(){
                el.style.display = 'none';
            }, 290);

            //setting normal style for dashes
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

//this is changeable piece of html
//here I getting info about confirmation (0 - pending; 1 - allow; 2 - denied)
//field named reverse used for determining whatever conversation leaded in first place by myself or by interlocutor
//
// !ACHTUNG!
// DO NOT change anything in php code!
// please)
// It is connected with other code in this file or another, so be careful ;)
// Thanks

    if ( $confirm == 0 && $conversation->reverse ) {
        //so, if conversation is pending and its initiated by friend - give to user some buttons)
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
        //if it is confirmed conversation - give user field to write a mess
        echo "
            <form id=\"form\" action=\"javascript:void(0);\" onsubmit=\"return sendMessage(this);\">
                <input type=\"text\" name=\"textt\" class=\"inputMessage\" placeholder=\"your message\" >
                <input type=\"hidden\" name=\"myUid\" value=\"$user->uid\">
                <input type=\"hidden\" name=\"partUid\" value=\"$interlocutor->uid\">
            </form>
        ";
    }

?>

</body>
</html>
