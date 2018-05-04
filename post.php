<?php


require('include/util.php');
require('include/functions.php');
require ('include/common.php');
require ('PHPMailer/class.phpmailer.php');
require ('PHPMailer/class.smtp.php');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if( isset($_POST['origin']) && ($_POST['origin'] == 'forgot_password')) {
    $msg_begin = "<span style='color: red;'>";
    $msg_end = "</span>";
    $msg = '';

    $email = mysqli_real_escape_string($conn, $_POST['forgot_password_email']);
    $email_hash = md5($email);
    $forgot_password_link = "<a href='" . APP_URL .  "/password-reset.php?_em_hsh=$email_hash' style='color: white; font-size: larger; text-decoration: none; font-weight: bold;'> Begin Reset Process</a>";;

    $sql = "SELECT email from student where email = '$email' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if (empty($_POST['forgot_password_email'])){
        $msg = "Please enter your email.";
    }elseif (mysqli_num_rows($result) > 0){
        $subject = "Password Reset";
        $title = "Password Reset Process";
        $message = "Please click on the link below to begin password reset process.";
        if(sendRequestApproveMail($conn, $email, $forgot_password_link, $subject, $title, $message, $smtp_array) == 'success'){

            $sql = "UPDATE student SET password_reset_status = 'ACTIVE' WHERE email = '$email'";
            if (mysqli_query($conn, $sql)) {
                if(mysqli_affected_rows($conn)){
                    $msg = '<span style="color: green">A mail has been sent to you. kindly continue the password reset process from your mailbox.</span>';
                }else{
                    $msg = "Unable to complete action. Try again";
                }
            }
        }else{
            $msg = "Unable to send mail. Please try again.";
        }
    }else{
        $msg = "Email address not regconized on this website";
    }

    $msgToUser = $msg_begin . $msg . $msg_end;

    echo $msgToUser;
}

if( isset($_POST['origin']) && ($_POST['origin'] == 'register')) {
    $email = mysqli_real_escape_string($conn, $_POST['register_email']);
    $email_hash = md5($email);
    $password='';
    $password_reset_status='';
    $name = mysqli_real_escape_string($conn, $_POST['register_name']);
    $group_name = mysqli_real_escape_string($conn, $_POST['register_group_name']);
    $institution = mysqli_real_escape_string($conn, $_POST['register_institution']);
    $faculty = mysqli_real_escape_string($conn, $_POST['register_faculty']);
    $department = mysqli_real_escape_string($conn, $_POST['register_department']);
    $course = mysqli_real_escape_string($conn, $_POST['register_course']);
    $program = mysqli_real_escape_string($conn, $_POST['register_program']);

    @list($username, $domain) = explode("@", $email);

    if(isset($_POST['register_terms'])){
        $terms = mysqli_real_escape_string($conn, $_POST['register_terms']);
    }

    $status = 'INACTIVE';
    $msg_begin = "<span style='color: red;'>";
    $msg_end = "</span>";
    $msg = '';

    $sql = "SELECT email from student where email = '$email' LIMIT 1";
    $result = mysqli_query($conn, $sql);


    if(empty($_POST['register_name'])){
        $msg = "Name field is required";
    }elseif(empty($email)){
        $msg = "Email field is required";
    }elseif( filter_var($email, FILTER_VALIDATE_EMAIL) === false ){
        $msg = "Invalid email format";
    }elseif( !checkdnsrr($domain, 'MX')){
        $msg = "Invalid domain name";
    }elseif (empty($group_name)){
        $msg = "Please enter a group name";
    }elseif (empty($institution)){
        $msg = "Please enter your academic institution";
    }elseif (empty($faculty)){
        $msg = "Please enter your faculty";
    }elseif (empty($department)){
        $msg = "Please enter your department";
    }elseif (empty($course)){
        $msg = "Please enter your course";
    }elseif (empty($program)){
        $msg = "Please select an academic program";
    }elseif (empty($terms)){
        $msg = "You must agree to the terms & conditions";
    }elseif (mysqli_num_rows($result) > 0){
        $msg = "Please choose a different email address";
    }else{

        $confirm_link = "<a href='".APP_URL."/activation.php?_em_hsh=$email_hash' style='color: white; font-size: larger; text-decoration: none; font-weight: bold;'> CONFIRM </a>";

        if (!$conn) {
            $msg = mysqli_connect_error();
        }else{
            if(sendRequestApproveMail($conn,$email,$confirm_link,'','', '', $smtp_array) == 'success'){

                $date = date('Y-m-d');
                $sql = "INSERT INTO studyprogram (groupname, institution, faculty, department, courseofstudy, programme, datecreated, status) VALUES ('$group_name', '$institution', '$faculty', '$department', '$course', '$program', '$date', '$status')";

               if (mysqli_query($conn, $sql)) {
                    $group_id = mysqli_insert_id($conn); //Student id (admin)
                    $membertype = 'ADMIN';
                    $activationstatus = 'INACTIVE';

                   $sql = "INSERT INTO student (email, email_hash, password, password_reset_status, name, activationstatus, membertype, groupid) VALUES ('$email', '$email_hash','$password', '$password_reset_status', '$name', '$activationstatus', '$membertype', '$group_id')";
                   if (mysqli_query($conn, $sql)) {

                       if(!file_exists("$upload_folder/$group_id")){
                           mkdir("$upload_folder/$group_id");//, 0777,true);
                       }

                       $msg = '<span style="color: green">A mail has been sent to you. Please kindly confirm your registration from your mailbox.</span>';
                       
                    }else{
                       $msg = "Error: " . $sql . "<br>" . mysqli_error($conn);
                   }
                } else {
                    $msg = "Error: " . $sql . "<br>" . mysqli_error($conn);
                }

            }else{
                $msg = "Unable to send mail. Please try again";
            }
        }

    }

    $msgToUser = $msg_begin . $msg . $msg_end;

    echo $msgToUser;
}

if( isset($_POST['origin']) && ($_POST['origin'] == 'student_login')) {
    $email = mysqli_real_escape_string($conn, $_POST['student_email']);
    $password = mysqli_real_escape_string($conn, $_POST['student_password']);
    $password_hash = md5($password);
    $status = '';
    $group_id = 0;
    $feedback = '';


    $result = mysqli_query($conn, "SELECT * FROM student WHERE email = '$email' AND password = '$password_hash' AND activationstatus = 'ACTIVE' LIMIT 1");

    if(empty($_POST['student_email'])){
        $feedback = 'email_field_empty';
    }elseif (empty($_POST['student_password'])){
        $feedback = 'password_field_empty';
    }elseif (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_array($result)) {
            $group_id = $row['groupid'];
            $status = $row['activationstatus'];
            $member_type = $row['membertype'];
            $email = $row['email'];
        }


        if (strtolower($status) == 'active') {
            $id = $group_id;
            $_SESSION['groupid'] = $group_id;
            $_SESSION['email'] = $email;
            $_SESSION['membertype'] = $member_type;
            $_SESSION['idx'] = base64_encode("g4p3h9xfn8sq03hs2234$id");

            if (isset($_POST['student_login_check'])) {
                setcookie("emailCookie", $email, $cookie_time , "/");
                setcookie("passwordCookie", $password_hash, $cookie_time, "/");
                setcookie("memberTypeCookie", $member_type, $cookie_time, "/");
            }

            $feedback = 'success';
        }else{
            $feedback = "not_activated";
        }

    } else {
        $feedback = "invalid_details";
    }

    echo $feedback;
}

mysqli_close($conn);


//function sendRequestApproveMail($conn, $student_email, $confirmation_link, $subject='', $title='', $message='', $smtp_settings){
//    /*$smtp_host = "secure290.sgcpanel.com";
//    $smtp_password = "automailer*123#";
//    $smtp_username = "automailer@oandsonsnetwork.com";*/
//
//    if(empty(trim($subject))){
//       $subject = "Confirmation Request";
//    }
//    if(empty(trim($title))){
//        $subject = "Confirmation Request";
//    }
//    if(empty(trim($message))){
//        $message = "Please click on the link below to confirm your request";
//    }
//
////    $sql = "SELECT * FROM settings LIMIT 1";
////    $result = mysqli_query($conn, $sql);
////    if(mysqli_num_rows($result) > 0) {
////        while ($row = mysqli_fetch_array($result)) {
////            $smtp_host = $row['smtp_host'];
////            $smtp_password = $row['smtp_password'];
////            $smtp_username = $row['smtp_username'];
////        }
////    }
//
//    require('PHPMailer/class.phpmailer.php');
//    require('PHPMailer/class.smtp.php');
//    $mail = new PHPMailer;
//    $mail->isSMTP();
//    $email = $student_email;//'enenim2000@yahoo.com';
//    //$mail->SMTPDebug = 2; //comment out this code for ajax request to work
//    $mail->Host = $smtp_settings['smtp_host'];//'secure290.sgcpanel.com';
//    $mail->Port = 465;
//    $mail->SMTPAuth = true;
//    $mail->Username = $smtp_settings['smtp_username'];//'automailer@oandsonsnetwork.com';
//    $mail->Password =$smtp_settings['smtp_password'];//'automailer*123#';
//    $mail->SMTPSecure = 'ssl';
//    $mail->From = $smtp_settings['smtp_username'];//'automailer@oandsonsnetwork.com';
//    $mail->FromName = "Student Digital Assignment";//ucfirst($store_name1) . ' espread application';
//    $recipient_name = trim($email);
//    $mail->addAddress($email); //Name is optional
//    //$mail->addReplyTo("bassey@dqdemos.com","Espread System");
//    $mail->isHTML(true); //Set email format to HTML
//    date_default_timezone_set ("Africa/Lagos");
//    $mail->Subject = "$subject";
//    $mail->Body = '<div style="width: 610px;">
//<div style="width: 610px;">
//        <div style="width: 600px; background-color: ghostwhite; color: red; padding: 10px; text-align: center; font-weight: bold;">
//            <span style="">'. $title . '</span><hr/>
//        </div>
//        <div style="width: 610px; background-color: white; padding: 5px; text-align: center; color: black; font-weight: bold;">
//            <span>' . $message . '</span>
//        </div>
//        <div style="width: 610px; background-color: ghostwhite; padding: 5px; text-align: center; color: black; font-weight: bold;">
//            <span class="" style="font-size: xx-large; font-weight: bold; color: orange;">&#8659;</span>
//        </div>
//        <div style="width: 610px; border-radius: 0; text-align: center; color: white; background-color: #3c8dbc; font-size:18px; font-weight:bold; padding: 5px;">'.  $confirmation_link . '</div>
//    </div>
//</div>';
//    $mail->AltBody = 'Student Digital Assignment ' . $subject;
//
//    if($mail->send()) {
//        return "success";
//    }else{
//        return "failure";
//    }
//}
