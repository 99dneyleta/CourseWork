/**
 * Created by deniz on 19/04/2017.
 */

function sent(username) {
    params = "username="+username+"&req=sent";
    url = "Brain/peopleManagment.php";
    var http = new XMLHttpRequest();

    http.open("POST", url, true);

    http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    http.onreadystatechange = function() {//Call a function when the state changes.
        if(http.readyState == 4 && http.status == 200) {
            response =  http.responseText;
            if ( response != "true" ) {
                alert(response);
            } else {
                //we're good
                document.getElementById("manage").onclick = function() { none(username); }
                document.getElementById("manage").textContent = "Add to friend";

            }
        }
    };

    http.send(params);
}

function received(username) {
    params = "username="+username+"&req=accept";
    url = "Brain/peopleManagment.php";
    var http = new XMLHttpRequest();

    http.open("POST", url, true);

    http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    http.onreadystatechange = function() {//Call a function when the state changes.
        if(http.readyState == 4 && http.status == 200) {
            response =  http.responseText;
            if ( response != "true" ) {
                alert(response);
            } else {
                //we're good
                document.getElementById("manage").onclick = function() { friend(username); }
                document.getElementById("manage").textContent = "Remove friend";
                document.getElementById("write").style = "";
                document.getElementById("bl").textContent = "Add to Black List";
            }
        }
    };

    http.send(params);
}

function friend(username) {
    params = "username="+username+"&req=remove";
    url = "Brain/peopleManagment.php";
    var http = new XMLHttpRequest();

    http.open("POST", url, true);

    http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    http.onreadystatechange = function() {//Call a function when the state changes.
        if(http.readyState == 4 && http.status == 200) {
            response =  http.responseText;
            if ( response != "true" ) {
                alert(response);
            } else {
                //we're good
                document.getElementById("manage").onclick = function() { received(username); }
                document.getElementById("manage").textContent = "Accept request";
                document.getElementById("write").style = "display: none;";
            }
        }
    };

    http.send(params);
}

function none(username) {
    params = "username="+username+"&req=send";
    url = "Brain/peopleManagment.php";
    var http = new XMLHttpRequest();

    http.open("POST", url, true);

    http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    http.onreadystatechange = function() {//Call a function when the state changes.
        if(http.readyState == 4 && http.status == 200) {
            response =  http.responseText;
            if ( response != "true" ) {
                alert(response);
            } else {
                //we're good
                document.getElementById("manage").onclick = function() { sent(username); }
                document.getElementById("manage").textContent = "Remove request"
            }
        }
    };

    http.send(params);
}


function showAbout(username) {
    alert("about");
}