/*var onResize = function() {
    var new_height= 10+$(".banner").height()+$(".navbar-sticky-top").height()
    console.log(new_height);
    // apply dynamic padding at the top of the body according to the fixed navbar height
    $("body").css("margin-top", new_height);


    var new_width=$(".banner").width()
    console.log(new_width);
    if (new_width < 760) {
        $("#sit_btn1").css("display", 'none'); 
        $("#link_pc1").css("display", 'none');
        $("#link_pc2").css("display", 'none');
        //$("#intestazione").css("display", 'none');
        //$(".banner").css("height", 0);
        $(".pull-left").css("height", 0);
        //$(".navbar-sticky-top").css("top", 0);
    } else {
        $("#sit_btn1").css("display", 'inline-block'); 
        $("#link_pc1").css("display", 'inline-block');
        $("#link_pc2").css("display", 'inline-block');
        $("#intestazione").css("display", 'inline-block');
        

    }
    
};*/


function onResize() {
    const $banner = $(".banner");
    const $navbar = $(".navbar-sticky-top");

    const newHeight = 10 + $banner.outerHeight() + $navbar.outerHeight();
    $("body").css("margin-top", newHeight);

    const isMobile = $banner.width() < 760;

    $("#sit_btn1, #link_pc1, #link_pc2").toggle(!isMobile);

    $(".pull-left").css("height", isMobile ? 0 : "");
}

$(function () {
    onResize();
    $(window).on("resize", onResize);
});



