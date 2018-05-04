<?php
$page_title= 'Begin Password Reset';
include ('header.php');
include ('include/modals.php');
?>

<!-- *****************************************************************************************************************
 HEADERWRAP
 ***************************************************************************************************************** -->

<div id="headerwrap">
    <div class="container">
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">

                <div class="table">
                    <div class="header-text">
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <h4 class="light white"><strong style="color: deepskyblue;">Password Reset Portal</strong></h4>
                                <h4 class="white typed">Kindly enter your email to begin password reset process</h4><span class="typed-cursor">|</span>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="modal-popup">
                    <form action="" class="popup-form">
                        <input type="text" class="form-control form-white" placeholder="Email">
                        <div style="width: 100%;">
                            <button style="width: 100%;" type="submit" class="btn btn-submit">Send</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-3"></div>
        </div><!-- /row -->
    </div> <!-- /container -->
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
</div><!-- /headerwrap -->

<?php
include ('footer.php');
?>

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


    function closeModal(ele) {
        $('#' + ele).modal('toggle');
    }

</script>
</body>
</html>
