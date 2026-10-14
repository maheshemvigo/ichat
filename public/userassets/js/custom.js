


$(document).ready(function () {


    //img-wrp

    $(".img-wrapper").each(function () {
        var imageUrl = $(this).find('img').attr("src");
        $(this).find('img').css("visibility", "hidden");
        $(this).css('background-image', 'url(' + imageUrl + ')').css("background-repeat", "no-repeat").css("background-size", "cover").css("background-position", "50% 50%");
    });
    //img-wrp

});



jQuery(document).ready(function ($) {
    'use strict';
    $(window).load(function () {
        anims();
        $("body").click(function () {
            anims();
        });
        function anims() {
            function myanimations(doelement, doclass) {
                jQuery(doelement).each(function () {
                    if (jQuery(this).offset().top + 0 < (jQuery(window).height() + jQuery(window).scrollTop()) &&
                            (jQuery(this).offset().top + jQuery(doelement).outerHeight()) + 170 > jQuery(window).scrollTop()) {
                        jQuery(this).addClass('animated');
                        jQuery(this).addClass(doclass);
                    } else {
                        //jQuery(this).removeClass(doclass);
                    }
                });
            }
            myanimations('.footer-logo,.cmndetails h2,.banner h5,.welcome-wrp h2 span,.home-contact-wrp h2', 'fadeInUp , anim0');
            myanimations('footer h3,.welcome-wrp h2,.home-contact-wrp h4', 'fadeInUp , anim05');

            myanimations('footer ul,.cmndetails p,.contact-btn,.banner h1', 'fadeInUp , anim1');
            myanimations('.login-box,.footer-bot .right-wrp img,.footer-bot a,.banner h2,.welcome-wrp p', 'fadeInUp , anim2');
            myanimations('.btn_grp', 'fadeInUp , anim4');

            myanimations('.welcome-wrp .left-wrp', 'fadeInUp , anim0');

            myanimations('.investor-wrp figure:first-child,.sourcer-wrp figure:first-child', 'fadeIn , anim05');
            myanimations('.investor-wrp figure:last-child', 'fadeInRight , anim1');

            myanimations('.sourcer-wrp figure:last-child', 'fadeInDown , anim0');





        }


    });
});
jQuery(window).scroll(function () {
    "use strict";
    function myanimations(doelement, doclass) {
        jQuery(doelement).each(function () {
            if (jQuery(this).offset().top + 0 < (jQuery(window).height() + jQuery(window).scrollTop())) {
                jQuery(this).addClass('animated');
                jQuery(this).addClass(doclass);
            } else {
                //jQuery(this).removeClass(doclass);
            }
        });
    }

    myanimations('.footer-logo,.cmndetails h2,.banner h5,.welcome-wrp h2 span,.home-contact-wrp h2', 'fadeInUp , anim0');
    myanimations('footer h3,.welcome-wrp h2,.home-contact-wrp h4', 'fadeInUp , anim05');

    myanimations('footer ul,.cmndetails p,.contact-btn,.banner h1', 'fadeInUp , anim1');
    myanimations('.login-box,.footer-bot .right-wrp img,.footer-bot a,.banner h2,.welcome-wrp p', 'fadeInUp , anim2');
    myanimations('.btn_grp', 'fadeInUp , anim4');

    myanimations('.welcome-wrp .left-wrp', 'fadeInUp , anim0');

    myanimations('.investor-wrp figure:first-child,.sourcer-wrp figure:first-child', 'fadeIn , anim05');
    myanimations('.investor-wrp figure:last-child', 'fadeInRight , anim1');

    myanimations('.sourcer-wrp figure:last-child', 'fadeInDown , anim0');


});


$(document).on('ready', function() {
    setTimeout(function() {
       $(".alert").fadeOut();
    }, 3000); 
 });
