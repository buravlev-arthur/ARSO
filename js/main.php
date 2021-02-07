<?php 
session_start();
header('Content-Type: charset=utf-8');
?>

//change elements by resize or start loading page
function resize_elements(){
    $('#head_menu').css('height',$(window).height()+'px');
    $('#content').css('height',($(window).height()-100)+'px');
    $('#head, #content').css('width',($(document).width()-$('#head_menu').width()-1)+'px');
    
    //for object_lessons
    $('.lessons_of_object_ul').hide(0);
    $('.object_edit_lessons_close').hide();
    $('.object_edit_lessons_open').show();
    //for object_lessons end
    
    if (parseInt($(window).width())<=1150){
        $('#head_menu').css('min-width','37px');                                   
        $('#head_menu').css('width','37px'); 
        $('#head, #content').css({
            'width':($(document).width()-37-1)+'px'
        });
    
    }
    else{                                   
        $('#head_menu').css('width',full_size_width_menu+'px');
        $('#head, #content').css({
            'width':($(document).width()-full_size_width_menu-1)+'px'
        });
    }
};

//slide menu
var full_size_width_menu; //start size of main menu
function slide_menu(){
    if (parseInt($('#head_menu').width())>37){
        $('#head_menu').css('min-width','37px');
        $('#head_menu').animate({
            'width':'37px'
        },200);
        $('#head, #content').animate({
            'width':($(document).width()-37-1)+'px'
        },200);
    }
    else{
        $('#head_menu').animate({
            'width':full_size_width_menu+'px'
        },200);
        $('#head, #content').animate({
            'width':($(document).width()-full_size_width_menu-1)+'px'
        },200);
    }
}

$(document).ready(function(){
    full_size_width_menu=$('#head_menu').width(); //start size of main menu
    
    resize_elements();
    
    $('#head_menu_button').click(function(){
        slide_menu();
    });
});

//by resize window
$(window).resize(function(){
    resize_elements();
});

$(function(){

$.getScript('./inputs.php');
    
//start loading of page
$('#content').load('objects_of_teacher.php');
    
    
//for main_head menu
var after_head_menu_link=1;
$('.head_menu_button').click(function(){
	if ($(this).index('.head_menu_button')!=after_head_menu_link){
		$('.head_menu_button').eq(after_head_menu_link).removeClass('head_menu_button_active');
		$(this).addClass('head_menu_button_active');
		after_head_menu_link=$(this).index('.head_menu_button');
	}
})
//user exit
$('#user_options_exit').click(function(){
	ajax_loading('body');
	$.ajax({
		url:'../server/user_exit.php',
		type:'GET',
		success: function(res){
			document.location.href='index.php'
		}
	});
});
});

//ajax-loading
function ajax_loading(element){
	$("<div id='white_shadow'></div>").append("<div id='ajax-loader'></div>").appendTo(element);
};