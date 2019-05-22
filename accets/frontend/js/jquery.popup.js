// Variables
"use strict";

var nbpics;
var currentpic;
var popup = $(".lx-popup");
var popupImg = $(".lx-popup-image img");
var popupTitle = $(".lx-popup-details h2");
var popupDescr = $(".lx-popup-details p");
var pupupCpic = $(".lx-popup-image span");

// espand popup click event
$(".lx-picture-news").on("click",function() {
    // set nbpics to 0
    nbpics = 0;
    // get the number of pictures
    for (var i = 0; i < $(".lx-picture-news").length; i++) {
        if ($(".lx-picture-news:eq(" + i + ")").parent().width() !== 0) {
            // increment the number of pictures
            nbpics += 1;
            // pot the number of picture in the attribute data
            $(".lx-picture-news:eq(" + i + ")").attr("data", nbpics);
        }
    }
    // get current picture number
    currentpic = $(this).attr("data");
    // transmit information to the popup
    popupImg.attr("src", $(this).find("img").attr("src"));
    popupTitle.text($(this).find("img").attr("data-title"));
    popupDescr.text($(this).find("img").attr("data-descr"));
    pupupCpic.text(currentpic + " of " + nbpics);
    popup.css({
        "display": "block"
    });
    return false;
});

// popup left arrow click event
$(".lx-popup-inside a .fa-caret-left").on("click",function() {
    // test if the curent picture is equal to 1 or not
    if (currentpic === 1) {
        currentpic = nbpics;
    } else {
        currentpic = parseInt(currentpic) - 1;
    }
    // transmit information to the popup
    popupImg.attr("src", $(".lx-picture-news[data='" + currentpic + "'] img").attr("src"));
    popupTitle.text($(".lx-picture-news[data='" + currentpic + "'] img").attr("data-title"));
    popupDescr.text($(".lx-picture-news[data='" + currentpic + "'] img").attr("data-descr"));
    pupupCpic.text(currentpic + " of " + nbpics);
    return false;
});

// popup right arrow click event
$(".lx-popup-inside a .fa-caret-right", ".lx-popup").on("click",function() {
    // test if the current picture is equal to the number pictures or not
    if (currentpic === nbpics) {
        currentpic = 1;
    } else {
        currentpic = parseInt(currentpic) + 1;
    }
    // transmit information to the popup
    popupImg.attr("src", $(".lx-picture-news[data='" + currentpic + "'] img").attr("src"));
    popupTitle.text($(".lx-picture-news[data='" + currentpic + "'] img").attr("data-title"));
    popupDescr.text($(".lx-picture-news[data='" + currentpic + "'] img").attr("data-descr"));
    pupupCpic.text(currentpic + " of " + nbpics);
    return false;
});

// popup remove click event
$(".lx-popup a .fa-remove").on("click",function() {
    // hide popup
    popup.css("display", "none");
    return false;
});

// Hide the popup when esc key is clicked
$(document).on("keyup", function(e) {
    if (e.keyCode === 27 || e.keyCode === 13) {
        // hide popup
        popup.css("display", "none");
    }
    if (e.keyCode === 37) {
        $(".lx-popup-inside a .fa-caret-left").trigger("click");
    }	
	if (e.keyCode === 39) {
        $(".lx-popup-inside a .fa-caret-right").trigger("click");
    }	
    return false;
});

$("body").on("mouseup",function (e){
	var bloc = $(".lx-popup-inside *");
	if (!bloc.is(e.target)){
        popup.css("display", "none");
	}
});

// arrows click event
$(".lx-popup-content,.lx-popup-inside a .fa-caret-left,.lx-popup-inside a .fa-caret-right", ".lx-popup").on("click",function(event) {
    // stop hide popup event
    event.stopPropagation();
    return false;
});