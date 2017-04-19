/**
 * Created by deniz on 18/04/2017.
 */

function find() {
    searchPhrase = document.getElementById("search").value;
    messages = document.getElementsByClassName("user");
    for( i = 0; i < messages.length; ++i) {
        name = messages[i].children[1].children[2].textContent;
        if ( name.indexOf(searchPhrase) < 0) {
            messages[i].style = "display: none;";
        } else {
            messages[i].style = "";
        }
    }
}