(function($) {
	"use strict";
	var $container = $('.portfolio'),
	    $items = $container.find('.portfolio-item'),
	    portfolioLayout = 'fitRows';

	if( $container.hasClass('portfolio-centered') ) {
		portfolioLayout = 'masonry';
	}

	$container.isotope({
		filter: '*',
		animationEngine: 'best-available',
		layoutMode: portfolioLayout,
		animationOptions: {
			duration: 750,
			easing: 'linear',
			queue: false
		},
		masonry: {
		}
	}, refreshWaypoints());

	function refreshWaypoints() {
		setTimeout(function() {
		}, 1000);
	}

	$('nav.portfolio-filter ul a').on('click', function() {
		var selector = $(this).attr('data-filter');
		$container.isotope({ filter: selector }, refreshWaypoints());
		$('nav.portfolio-filter ul a').removeClass('active');
		$(this).addClass('active');
		return false;
	});

	function getColumnNumber() {
		var winWidth = $(window).width(),
		    columnNumber = 1;

		if (winWidth > 1200) {
			columnNumber = 5;
		} else if (winWidth > 950) {
			columnNumber = 4;
		} else if (winWidth > 600) {
			columnNumber = 3;
		} else if (winWidth > 400) {
			columnNumber = 2;
		} else if (winWidth > 250) {
			columnNumber = 1;
		}
		return columnNumber;
	}

	function setColumns() {
		var winWidth = $(window).width(),
		    columnNumber = getColumnNumber(),
		    itemWidth = Math.floor(winWidth / columnNumber);

		$container.find('.portfolio-item').each(function() {
			$(this).css( {
				width : itemWidth + 'px'
			});
		});
	}

	function setPortfolio() {
		setColumns();
		$container.isotope('reLayout');
	}

	$container.imagesLoaded(function () {
		setPortfolio();
	});

	$(window).on('resize', function () {
		setPortfolio();
	});
})(jQuery);



$(function() {
	$('#register_loading_gif').hide();
	$('#student_login_loading_gif').hide();
	$('#forgot_password_loading_gif').hide();

	$("#register_form").on("submit", function(event) {
		//document.getElementById('register_loading_gif').style.display = 'block';
		$('#register_loading_gif').show();
		event.preventDefault();

		$.ajax({
			url: "post.php",
			type: "post",
			data: $(this).serialize(),
			success: function(response) {
				$('#register_loading_gif').hide();
				$('#register_feedback').empty().html(response);
			}
		});
	});

	$("#forgot_password_form").on("submit", function(event) {
		$('#forgot_password_loading_gif').show();
		event.preventDefault();

		$.ajax({
			url: "post.php",
			type: "post",
			data: $(this).serialize(),
			success: function(response) {
				$('#forgot_password_loading_gif').hide();
				$('#forgot_password_feedback').html(response);
			}
		});
	});

	$("#student_login_form").on("submit", function(event) {
		//document.getElementById('register_loading_gif').style.display = 'block';
		var begin = "<span style='font-size: 14px; font-weight: bold; color: red;'>";
		var end = "</span>";
		var msg = '';
		$('#student_login_loading_gif').show();
		event.preventDefault();

		$.ajax({
			url: "post.php",
			type: "post",
			data: $(this).serialize(),
			success: function(response) {

				//alert(response);
				//document.getElementById('register_loading_gif').style.display = 'none';
				$('#student_login_loading_gif').hide();

				if(response == 'email_field_empty'){
					msg = 'Enter your email';
					$('#student_login_feedback').html(begin + msg + end);
				}else if(response == 'password_field_empty'){
					msg = 'Enter your password';
					$('#student_login_feedback').html(begin + msg + end);
				}else if(response == 'success'){
					msg = '<span style="color: green">Login Successful</span>';
					$('#student_login_feedback').html(begin + msg + end);
					setTimeout(function(){ location.assign("<?php echo $first_screen; ?>"); }, 2000);
				}else if(response == 'not_activated'){
					msg = 'Your account has not been activated.';
					$('#student_login_feedback').html(begin + msg + end);
				}else if(response == 'invalid_details'){
					msg = 'Email or password not recognized';
					$('#student_login_feedback').html(begin + msg + end);
				}else {
					$('#student_login_feedback').html(begin + msg + end);
				}
			}
		});
	});

});
function closeModal(ele) {
	$('#' + ele).modal('toggle');
}