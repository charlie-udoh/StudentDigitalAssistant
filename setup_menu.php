<?php
require 'include/util.php';
include ('include/functions.php');
include ('include/common.php');
include ('check_user_log.php');


$page_title= 'Setup Menu';
include ('header_a.php'); 

?>

<div align="center" style="margin:20px"><a href="setup_academic_year.php" class="btn btn-lg btn-blue-fill" style="padding:40px 60px 40px 60px;">Setup Academic Periods</a></div>
<div align="center" style="margin:20px"><a href="setup_courses.php" class="btn btn-lg btn-blue-fill" style="padding:40px 100px 40px 100px;">Setup Courses</a></div>
<div align="center" style="margin:20px"><a href="upload_materials.php" class="btn btn-lg btn-blue-fill"  style="padding:40px 93px 40px 93px;">Upload Materials</a></div>
<div align="center" style="margin:20px"><a href="invite_members.php" class="btn btn-lg btn-blue-fill"  style="padding:40px 100px 40px 100px;">Invite Members</a></div>

<script>
	// Portfolio
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
</script>
</body>
</html>
