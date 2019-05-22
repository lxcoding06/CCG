// Strict Mode
"use strict";

// Windows load event
$(window).on("load", function() {
    // Loader Fade Out
    $(".lx-loader", ".lx-wrapper").fadeOut();
    return false;
});

// Document Ready event
$(document).on("ready", function() {
	
	// Breaking News Effect
	var bn = 1;
	window.setInterval(function(){
		$(".lx-breaking-news ul li").slideUp();
		$(".lx-breaking-news ul li:eq("+bn+")").slideDown();
		bn++;
		if($(".lx-breaking-news ul li").length <= bn){
			bn = 0;
		}
	},5000);

	// Mini Slide Init
	for(var i=0;i<$(".lx-mini-slide").length;i++){
		$(".lx-mini-slide:eq("+i+") ul li").css({"width":$(".lx-mini-slide:eq("+i+")").outerWidth()+"px"});
		$(".lx-mini-slide:eq("+i+") ul").css({"-webkit-transition":"all 0s","transition":"all 0s","left":"-"+$(".lx-mini-slide:eq("+i+") ul li").outerWidth()+"px"});		
	}

	// Resize Video Iframe
	$(".lx-single-post-img iframe").css("height",($(".lx-single-post-img iframe").width()*0.5625)+"px");
	
	return false;
});

$(window).on("resize", function() {
	
	// Resize Video Iframe
	$(".lx-single-post-img iframe").css("height",($(".lx-single-post-img iframe").width()*0.5625)+"px");
	
	return false;
});

// Responsive Menu Effect
$(".lx-header > a").on("click", function() {
	if($(".lx-wrapper").attr("dir") === "rtl"){
		if($(".lx-header").css("right") === "-200px"){
			$(".lx-header").css("right","0px");
			$(".lx-header > a i").attr("class","fa fa-caret-right");
		}
		else{
			$(".lx-header").css("right","-200px");
			$(".lx-header > a i").attr("class","fa fa-caret-left");
		}		
		
	}
	else{
		if($(".lx-header").css("left") === "-200px"){
			$(".lx-header").css("left","0px");
			$(".lx-header > a i").attr("class","fa fa-caret-left");
		}
		else{
			$(".lx-header").css("left","-200px");
			$(".lx-header > a i").attr("class","fa fa-caret-right");
		}		
	}
});

// Responsive Menu Effect
$(".lx-header-horizontal .lx-header-main-menu > a").on("click", function() {
	if($(this).next("ul").css("left") === "-160px"){
		$(this).next("ul").css("left","0px");
	}
	else{
		$(this).next("ul").css("left","-160px");
	}		
});

// Responsive color setting event
$(".lx-settings > i").on("click", function() {
    if ($(".lx-settings").css("right") === "-111px") {
        $(".lx-settings").css("right", "0px");
    } else {
        $(".lx-settings").css("right", "-111px");
    }
    return false;
});

// Responsive color event
$(".lx-colors > a").on("click", function() {
    // Change style
    $("link[title='main']").attr("href", $(this).attr("data-css-link"));
    return false;
});

// Mini Slide Effect
var lx_passed = "yes";
$(".lx-mini-slide-nav > .fa-angle-right").on("click",function(){
	if(lx_passed == "yes"){
		lx_passed = "no";
		var ul = $(this).parent().parent().find("ul")
		ul.css({"-webkit-transition":"all 0.4s","transition":"all 0.4s","left":"-"+(ul.find("li").outerWidth()*2)+"px"});
		window.setTimeout(function(){
			ul.css({"-webkit-transition":"all 0s","transition":"all 0s","left":"-"+ul.find("li").outerWidth()+"px"});
			var item = "<li style='width:"+ul.find("li").outerWidth()+"px;'>"+ul.find("li:eq(0)").html()+"</li>";
			ul.append(item);
			ul.find("li:eq(0)").remove();
			lx_passed = "yes";
		},500);
	}
});
$(".lx-mini-slide-nav > .fa-angle-left").on("click",function(){
	if(lx_passed == "yes"){
		lx_passed = "no";
		var ul = $(this).parent().parent().find("ul")
		ul.css({"-webkit-transition":"all 0.4s","transition":"all 0.4s","left":"0px"});
		window.setTimeout(function(){
			ul.css({"-webkit-transition":"all 0s","transition":"all 0s","left":"-"+(ul.find("li").outerWidth())+"px"});
			var item = "<li style='width:"+ul.find("li").outerWidth()+"px;'>"+ul.find("li:last-child").prev(".lx-mini-slide ul li").html()+"</li>";
			ul.prepend(item);
			ul.find("li:last-child").prev(".lx-mini-slide ul li").remove();
			lx_passed = "yes";
		},500);
	}
});

// Tabs Effect
$(".lx-tab-nav ul li a").on("click",function(){
	var index = $(this).parent().index();
	var el = $(this).parent().parent().parent().parent();
	$(".lx-tab-nav ul li a").removeClass("active");
	$(this).addClass("active");
	el.find(".lx-tab").slideUp();
	el.find(".lx-tab:eq("+index+")").slideDown();
});

// Toggle Effect
$(".lx-toggle ul li > a").on("click",function(){
	$(".lx-toggle ul li > a i").attr("class","fa fa-plus");
	$(this).find("i").attr("class","fa fa-minus");
	$(this).parent().parent().find(".lx-toggle-item").slideUp();
	$(this).next(".lx-toggle-item").slideDown();
});

// Share Effect
$(document).on("scroll", function() {
	if($(window).width() > 768){
		if($(".lx-single-post-content").length){
			if(($(this).scrollTop() > $(".lx-single-post-content").offset().top) && ($(this).scrollTop() < $(".lx-single-post-content").offset().top+$(".lx-single-post-content").height()-312)) {
				$(".lx-share").css({"position":"fixed","left":$(".lx-share").offset().left+"px","top":"20px","bottom":"auto"});
			}
			else if($(this).scrollTop() > $(".lx-single-post-content").offset().top+$(".lx-single-post-content").height()-312){
			   $(".lx-share").css({"position":"absolute","left":"20px","bottom":"20px","top":"auto"});
			}
			else if($(this).scrollTop() < $(".lx-single-post-content").offset().top){
			   $(".lx-share").css({"position":"absolute","left":"20px","top":"20px","bottom":"auto"});
			}
		}
	}
    return false;
});