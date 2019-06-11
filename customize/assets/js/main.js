(function($) {
	jQuery(document).ready(function($) {

	    // Menu Mobile
	    $('.btn_menu').on('click', function (e){
	    	e.preventDefault();
	    	$(this).parents().find('.menu_mobile').toggleClass('open_menu');
	    	$(this).parents().find('.overlay_menu').toggleClass('show_overlay');
	    });

	     $('.close_menu').on('click', function (e){
	    	e.preventDefault();
	    	$(this).parents().find('.menu_mobile').toggleClass('open_menu');
	    	$(this).parents().find('.overlay_menu').toggleClass('show_overlay');
	    });

	    $(".overlay_menu").on("click", function (){
        	$(this).parents().find('.menu_mobile').toggleClass('open_menu');
	    	$(this).parents().find('.overlay_menu').toggleClass('show_overlay');                 
    	});

    	$(".toggle_menu").on("click", function (e){
    		e.preventDefault();
        	$(this).parent().find('.sub_menu').slideToggle();
    	});

    	// Hero slider
    	$('.owl-carousel').owlCarousel({
		    loop:true,
		    margin:10,
		    nav:false,
		    animateOut: 'fadeOut',
		    responsive:{
		        0:{
		            items:1
		        },
		        600:{
		            items:1
		        },
		        1000:{
		            items:1
		        }
		    }
		});
		
		$('.lumise-filter').find('input, select').on('change', function (){
			//console.log($(this).closest('form'));
			$(this).closest('form').submit();
		});
	    
	});
})(jQuery);
