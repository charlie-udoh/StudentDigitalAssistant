<?php
$options = "<option value=''>Select academic programme</option>";
foreach ($academic_programs as $key=>$value){
    $options .= "<option value='$value'>$value</option>";
}
?>



<div class="modal fade" id="memberLoginModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-popup">
            <a href="#" data-dismiss="modal" class="close-link"><i class="icon_close_alt2"></i></a>
            <h3 class="white">Student Log In</h3>
            <form action="" class="popup-form" id="student_login_form" method="post">
                <input type="text" class="form-control form-white" name="student_email" placeholder="Email">
                <input type="password" class="form-control form-white" name="student_password" placeholder="Password">
                <div class="checkbox-holder text-left">
                    <div class="checkbox">
                        <input type="checkbox" value="None" id="student_login_check" name="student_login_check" />
                        <label for="student_login_check"><span>Remember Me</span></label>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img id="student_login_loading_gif" style="width: 25px; height: 25px;" src="loading.gif"/>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-submit">Log In</button>
                <input type="hidden" value="student_login"  name="origin" />
                
                <div id="student_login_feedback" style="font-size: 13px; font-weight: bold; margin-top: 8px;"></div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="adminLoginModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-popup">
            <a href="#" data-dismiss="modal" class="close-link"><i class="icon_close_alt2"></i></a>
            <h3 class="white">Admin Log In</h3>
            <form action="<?php echo $this_file; ?>" method="post" class="popup-form">
                <input type="text" class="form-control form-white" placeholder="Email">
                <input type="text" class="form-control form-white" placeholder="Password">
                <div class="checkbox-holder text-left">
                    <div class="checkbox">
                        <input type="checkbox" value="None" id="adminLogin" name="check" />
                        <label for="adminLogin"><span>Remember Me</span></label>
                    </div>
                </div>
                <button type="submit" class="btn btn-submit">Log In</button>
            </form>
        </div>
    </div>
</div>



<div class="modal fade" id="registerModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-popup">
            <a id="" href="#" data-dismiss="modal" class="close-link"><i class="icon_close_alt2"></i></a>
            <h3 class="white">Sign Up</h3>
            <div id="register_feedback" style="font-size: 13px; font-weight: bold; margin-top: 8px;"></div>
            <form id="register_form" class="popup-form" style="margin-top: 1px; margin-bottom: 1px;">
                <input type="text" name="register_name" class="form-control " placeholder="Name">
                <input type="text" name="register_email" class="form-control " placeholder="Email">
                <input type="text" name="register_group_name" class="form-control " placeholder="Group Name">
                <input type="text" name="register_institution" class="form-control " placeholder="Institution">
                <input type="text" name="register_faculty" class="form-control " placeholder="Faculty">
                <input type="text" name="register_department" class="form-control" placeholder="Department">
                <input type="text" name="register_course" class="form-control" placeholder="Course of study">
                <select name="register_program" class="form-control">
                    <?php echo $options; ?>
                </select>
                <div class="checkbox-holder text-left">
                    <div class="checkbox">
                        <input type="checkbox" value="None" id="register_terms" name="register_terms" />
                        <label for="register_terms"><span>I Agree to the <a href="#" style="color: darkblue;"> &nbsp;&nbsp; Terms &amp; Conditions</a></span></label>
                        &nbsp;&nbsp;&nbsp;<img id="register_loading_gif" style="width: 25px; height: 25px;" src="loading.gif"/>
                    </div>
                </div>
                <button type="submit" class="btn btn-submit" style="margin-top: 1px;">Register</button>
                <input type="hidden" value="register"  name="origin" />
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="forgotPasswordModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-popup">
            <a id="" href="#" data-dismiss="modal" class="close-link"><i class="icon_close_alt2"></i></a>
            <h3 class="white">Password Reset</h3>
            <form id="forgot_password_form" class="popup-form">
                <input type="text" name="forgot_password_email" class="form-control form-white" placeholder="Email">

                <div id="forgot_password_feedback" style="font-size: 13px; font-weight: bold; margin-top: 8px;"> </div>

                <button type="submit" class="btn btn-submit">Send</button>
                <input type="hidden" value="forgot_password" name="origin" />

                <div class="checkbox-holder text-left">
                    <div class="checkbox" style="padding-left: 174px;">
                        <img id="forgot_password_loading_gif" style="width: 25px; height: 25px;" src="loading.gif"/>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
