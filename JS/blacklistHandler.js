/**
 * Created by deniz on 19/04/2017.
 */

function addToBlacklist(myId, userId) {
    params = "myId="+myId+"&userId="+userId+"&mode=Add";
    url = "Brain/blacklistHandler.php";
    var http = new XMLHttpRequest();

    http.open("POST", url, true);

    http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    http.onreadystatechange = function() {//Call a function when the state changes.
        if(http.readyState == 4 && http.status == 200) {
            response =  http.responseText;
            if ( response != "true" ) {
                alert(response);
            } else {
                document.getElementById("bl").onclick = function () { removeFromBlacklist(myId, userId); };
                document.getElementById("bl").textContent = "Remove from Black List";
                document.getElementById("write").style = "display:none;";
            }
        }
    };

    http.send(params);
}

function removeFromBlacklist(myId, userId) {
    params = "myId="+myId+"&userId="+userId+"&mode=Remove";
    url = "Brain/blacklistHandler.php";
    var http = new XMLHttpRequest();

    http.open("POST", url, true);

    http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    http.onreadystatechange = function() {//Call a function when the state changes.
        if(http.readyState == 4 && http.status == 200) {
            response =  http.responseText;
            if ( response != "true" ) {
                alert(response);
            } else {
                document.getElementById("bl").onclick = function () { addToBlacklist(myId, userId); };
                document.getElementById("bl").textContent = "Add to Black List";
            }
        }
    };

    http.send(params);
}