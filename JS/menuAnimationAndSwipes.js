//importScripts("./jgestures.js");

//    $(document).ready(function(){
//        $('#nav-icon2').click(function(){
//            $(this).toggleClass('open');
//
//        });
//    });

function openNav() {
    var k = document.getElementById("mySidenav").style.width;

    $('#nav-icon2').toggleClass('open');

    if(k == "50vw") {
        closeNav();
    }
    else {
        document.getElementById("nav-icon2").style.marginLeft = "50vw";
        document.getElementById("mySidenav").style.width = "50vw";

    }
}

function closeNav() {
    document.getElementById("mySidenav").style.width = "0";
    document.getElementById("nav-icon2").style.marginLeft = "4vw";
}


function openMaNav() {
    var k = document.getElementById("ma-mySidenav").style.height;
    console.log(k);
    if(k == "18vh") {
        closeMaNav();
    }
    else {
        document.getElementById("ma-mySidenav").style.height = "18vh";
        document.getElementById("ma-mySidenav").style.width = "50vw";

    }

}


function closeMaNav() {
    console.log("2");
    document.getElementById("ma-mySidenav").style.height = "0vh";
    document.getElementById("ma-mySidenav").style.width = "0vw";
}


function openFindNav() {
    document.getElementById("myFindSidenav").style.width = "100vw";
    document.getElementById("nav-icon2").style.marginLeft = "-100vw";
    document.getElementById("chats-find").style.marginRight = "120vw";
//    document.getElementById("header").style.marginLeft = "100vw";
}

function closeFindNav() {
    document.getElementById("myFindSidenav").style.width = "0";
    document.getElementById("nav-icon2").style.marginLeft = "4vw";
    document.getElementById("chats-find").style.marginRight = "4vw";
//    document.getElementById("header").style.marginLeft = "0";
    messages = document.getElementsByClassName("user");
    for( i = 0; i < messages.length; ++i) {
        messages[i].style = "";
    }
    document.getElementById("search").value = "";
}




$(function(){
    $('body').bind('swiperight', function(){
        openNav();
    });
});
$(function(){
    $('body').bind('swipeleft', function(){
        openNav();
    });
});
$(function(){
    $('div').bind('swiperight', function(){
        openNav();
    });
});
$(function(){
    $('div').bind('swipeleft', function(){
        openNav();
    });
});
$(function(){
    $('img').bind('swiperight', function(){
        openNav();
    });
});
$(function(){
    $('img').bind('swipeleft', function(){
        openNav();
    });
});

