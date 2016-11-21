$(document).ready(function(){
    var fixed = false ;
    var moved = false;
    $(document).scroll(function(){
        if($(window).scrollTop() > 60 && !moved){

            $('#barre-recherche').animate({
                top: "90px",
                left: "100px"
                }
            ).addClass('recherche-decale');
            $('#logo-header').animate({
                    top: "90px"
                }
            );
            moved = true;
        }
        if($(window).scrollTop() < 60 && moved){
            $('#barre-recherche').animate({
                    top: "0px",
                    left: "0px"
                }
            ).removeClass('recherche-decale');
            $('#logo-header').animate({
                    top: "0px"
                }
            );
            moved = false;
        }
        if($(window).scrollTop() > 110 && !fixed){
            $('header').addClass("header-fixed");
            $('.body').addClass("body-margin");
            fixed = true;
        }
        if($(window).scrollTop() < 110 && fixed){
            $('header').removeClass("header-fixed");
            $('.body').removeClass("body-margin");
            fixed = false;
        }
    })
});


