<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    include "send_email.php";
    /*  Taking user input   */
    $email = mysqli_real_escape_string($conn, $_REQUEST['email']);

    $response = new StdClass;

    if($email === ""){
        $response->status_code = 500;
        $response->data = null;
        $response->error = "Kindly provide valid input.";

        echo json_encode($response);
        // echo "Kindly provide valid input.";
        exit();
    }

    /*  Checking user login    */
    $userId = 0;
    $email_exist = "SELECT a.`user_id`
                    FROM user_master a INNER JOIN user_login b 
                    ON a.`user_id`=b.`user_id`
                    WHERE a.`email_id` = '".$email."' AND del_by IS NULL";
    $email_response = mysqli_query($conn, $email_exist);
    if (mysqli_num_rows($email_response) == 0){
        // echo "Invalid login credentials.";
        $response->status_code = 500;
        $response->data = null;
        $response->error = "Invalid email id.";
        echo json_encode($response);
        exit();
    }else{
        while ($row = mysqli_fetch_array($email_response, MYSQLI_ASSOC)) {
            $userId = $row['user_id'];
        }
    }

    $otp = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6);
    //echo $otp;

    /*  Updating OTP */
    $update_otp_sql = "UPDATE user_login 
                        SET otp = UPPER('".$otp."')
                        WHERE user_id = ".$userId;

    if ($conn->query($update_otp_sql) !== TRUE) {
        $response->status_code = 500;
        $response->data = null;
        $response->error = "Some error occurred while validating email. Please try again later.";
        echo json_encode($response);
        exit();
    }
    
    $retval = "0";
    if (!$conn -> commit()) {
        $response->status_code = 500;
        $response->data = null;
        $response->error = "Some error occurred while validating email. Please try again later.";
        echo json_encode($response);
        exit();
    }else{
        // $mail_to = 'khot.raj92@gmail.com';
        // $subject = 'Reset Password';
        // $content = 'Hello User,<br/><br/>
        //             Your OTP for reset password is <b>'.$otp.'</b>. OTP is secret and can be used only once. Therefore, do not disclose this to anyone.<br/><br/>
        //             Please do not reply to this mail.<br/><br/>
        //             Thanks,<br/>
        //             Team Balaxi';
        // $retval = sendEmail($mail_to,null,$subject,$content);
        $retval = "1";
        if($retval == "0"){
            $response->status_code = 500;
            $response->data = null;
            $response->error = "Some error occurred while sending OTP. Please try again later.";
            echo json_encode($response);
            exit();
        }else{
            $response->status_code = 200;
            $response->data = "1";
            $response->error = null;
            echo json_encode($response);
        }
    }

    mysqli_free_result($email_response);

    $conn->close();

?>