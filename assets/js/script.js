$(document).ready(function(){
    $(".toggle-menu i").click(function(){
        $(".slidebar").toggleClass("active");
        $(".slidebar").toggleClass("hidden");
        $("main").toggleClass("hidden");
        $(".overlay-sm").toggleClass("activated");
    });
});



