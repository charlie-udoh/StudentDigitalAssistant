<?php
require('include/util.php');
require ('include/common.php');
//include ('check_user_log.php');

$msg_begin = "<span style='color: red;'>";
$msg_end = "</span>";
$msg = '';
$password1 = '';
$password2 = '';
$msgToUser = '';

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);


$this_file = htmlspecialchars($_SERVER["PHP_SELF"]);
if(isset($_GET['_em_hsh']) ){
    $url_append = '?_em_hsh=' . mysqli_real_escape_string($conn, $_GET['_em_hsh']);
    $this_file = $this_file . $url_append;
}

if( isset($_POST['origin']) && ($_POST['origin'] == 'activation')) {

    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    $password1 = mysqli_real_escape_string($conn, $_POST['activation_password1']);
    $password2 =mysqli_real_escape_string($conn, $_POST['activation_password2']);
    $password_hash = md5($password1);
    $email = mysqli_real_escape_string($conn, $_GET['_em_hsh']);

    $sql = "SELECT email from student where email = '$email' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if(empty($_POST['activation_password1'])){
        $msg = "Please enter your password";
    }elseif(empty($_POST['activation_password2'])){
        $msg = "Please re-enter your password in the confirm password field";
    }elseif ($_POST['activation_password1'] != $_POST['activation_password2'] ){
        $msg = "Password field must match confirm password field";
    }elseif(!isset($_GET['_em_hsh'])){
        $msg = "Malicious activity noticed!";
    } else{
        $email_hash = mysqli_real_escape_string($conn, $_GET['_em_hsh']);
        $sql = "SELECT email from student where email_hash = '$email_hash' LIMIT 1";
        $result = mysqli_query($conn, $sql);
        if(mysqli_num_rows($result) > 0){
            $sql = "UPDATE student SET password = '$password_hash', activationstatus = 'ACTIVE' WHERE email_hash = '$email_hash'";
            if (mysqli_query($conn, $sql)) {
                if(mysqli_affected_rows($conn)){
                    $status = md5($active_value);
                    header("location: index.php?_act_=$status");
                    exit();
                }else{
                    $msg = "Unable to complete action. try again";
                }
            }else{
                $msg = "Error: " . $sql . "<br>" . mysqli_error($conn);
            }
        }else{
            $msg = "Malicious activity noticed!";
        }
    }
}

$msgToUser = $msg_begin . $msg . $msg_end

?>



<?php
$page_title= 'Account Activation';
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
                                <h4 class="light white"><strong style="color: deepskyblue;">Account Activation</strong></h4>
                                <h4 class="blue typed">Kindly enter password to complete your registration process<span class="typed-cursor">|</span></h4>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="modal-popup">
                    <form action="<?php echo $this_file; ?>" method="post" class="popup-form">
                        <input type="password" name="activation_password1" class="form-control form-white" placeholder="Password" value="<?php echo $password1; ?>">
                        <input type="password" name="activation_password2" class="form-control form-white" placeholder="Confirm Password" value="<?php echo $password2; ?>">
                        <div style="width: 100%;">
                            <div style="margin-top: 10px; font-size: 14px; font-weight: bold;">
                                <?php
                                    echo $msgToUser;
                                ?>
                            </div>
                            <button style="width: 100%;" type="submit" class="btn btn-submit">Proceed</button>
                            <input type="hidden" value="activation" name="origin" />
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
