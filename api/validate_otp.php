<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input   */
    $email = mysqli_real_escape_string($conn, $_REQUEST['email']);
    $otp = mysqli_real_escape_string($conn, $_REQUEST['otp']);

    $response = new StdClass;

    if($email === "" || $otp === ""){
        $response->status_code = 500;
        $response->data = null;
        $response->error = "Kindly provide valid input.";

        echo json_encode($response);
        // echo "Kindly provide valid input.";
        exit();
    }

    /*  Checking user login    */
    $email_exist = "SELECT a.`user_id` 
                    FROM user_master a INNER JOIN user_login b 
                    ON a.`user_id`=b.`user_id` 
                    WHERE a.`email_id` = '".$email."' AND b.`otp`='".$otp."' 
                    AND del_by IS NULL";
    $email_response = mysqli_query($conn, $email_exist);
    if (mysqli_num_rows($email_response) == 0){
        // echo "Invalid login credentials.";
        $response->status_code = 500;
        $response->data = null;
        $response->error = "Invalid OTP.";
        echo json_encode($response);
        exit();
    }else{
        while ($row = mysqli_fetch_array($email_response, MYSQLI_ASSOC)) {
            $response->status_code = 200;
            $response->data = $row['user_id'];
            $response->error = null;
            echo json_encode($response);
        }
    }

    mysqli_free_result($email_response);

    $conn->close();

?>