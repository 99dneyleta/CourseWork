
var lastId = 0;
var fetching = false;

/*
 * Some magic
 * Using JS to upload message to server via php with glue - POST method
 * copied from internet, don't ask, how it works
 */
function sendMessage(form) {
    //interested in those fields from form
    var text = form.text.value;
    var me = form.myUid.value;
    var part = form.partUid.value;
    form.text.value = "";

    var http = new XMLHttpRequest();

    //setting server's program url
    var url = "Brain/writeMessage.php";
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
                var url = "Brain/addLog.php";
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

            fetch();
        }
    };

    http.send(params);

    //calling to fetch new mess


    //returning false, so page not refreshing because I'm using form
    return false;
}

//function to fetch new messages, same as last 2  functions
function fetch() {
    if (fetching) {
        return ;
    }
    fetching = true;
    var me = form.myUid.value;
    var part = form.partUid.value;

    var http = new XMLHttpRequest();
    var url = "Brain/fetchMessages.php";
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
            //scrolling down in case of new messages
            if ( responseText != "") {
                objDiv.innerHTML += responseText;
                objDiv.scrollTop = objDiv.scrollHeight;
            }

            //getting lastId from string
            lastId = parseInt(res);

            //my way of deleting request from memory
            http = null;

            fetching = false;

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

$(document).ready( function () { $.ajaxSetup({cache:false}); fetch(); setInterval(function () { fetch();  }, 1000)} );
