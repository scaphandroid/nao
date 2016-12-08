$(document).ready(function(){

    var fixed = false ;
    var moved = false;
    var top1 = "90px";
    var top2 ;
    var left2 = "100px";
    var left3 = "0px";
    var limite = 60;
    var limite2 = 110;

    function ajustement(){

        if( $(window).width() < 768 ){
            left3 = "-120px";
            top2 = "45px";
            limite = 30;
            limite2 = 40;
        }else{
            top2 = top1;
            left3 = "0px";
            limite = 60;
            limite2 = 110;
        }

        if($(window).scrollTop() > limite && !moved){
            $('#barre-recherche').animate({
                    top: top1,
                    left: left2
                }
            ).addClass('recherche-decale');
            if( $(window).width() < 768 ){
                $('#logo-header').animate({
                        top: top2,
                        left: left3
                    }
                );
            }else{
                $('#logo-header').animate({
                        top: top2
                    }
                );
            }
            moved = true;
        }

        if($(window).scrollTop() < limite && moved){
            $('#barre-recherche').animate({
                    top: "0px",
                    left: "0px"
                }
            ).removeClass('recherche-decale');
            $('#logo-header').animate({
                    top: "0px",
                    left: "0px"
                }
            );
            moved = false;
        }

        if($(window).scrollTop() > limite2 && !fixed){
            $('header').addClass("header-fixed");
            $('.body').addClass("body-margin");
            $('nav').addClass("nav-decale");
            fixed = true;
        }

        if($(window).scrollTop() < limite2 && fixed){
            $('header').removeClass("header-fixed");
            $('.body').removeClass("body-margin");
            $('nav').removeClass("nav-decale");
            fixed = false;
        }
    }

    ajustement();

    $(document).scroll(function(){
        ajustement();
    });

});


