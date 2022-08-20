<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input     */
    $user_id = mysqli_real_escape_string($conn, $_REQUEST['user_id']);
	$password = mysqli_real_escape_string($conn, $_REQUEST['password']);

    $conn -> autocommit(FALSE);

    if($user_id === "" || $password === ""){
        echo "Kindly provide valid input.";
        exit();
    }

    /*  Checking user by id already exists     */
    $emailId = "";
    $user_exist = "SELECT email_id FROM user_master 
                    WHERE user_id = ".$user_id."
                    AND del_by IS NULL";
    $user_response = mysqli_query($conn, $user_exist);
    if (mysqli_num_rows($user_response) == 0){
        echo "User details not found for user id ".$user_id." in the system.";
        exit();
    }else{
        while ($row = mysqli_fetch_array($user_response, MYSQLI_ASSOC)) {
            $emailId = $row['email_id'];
        }
    }

    $update_login_sql = "UPDATE user_login 
                        SET password=SHA1('".$emailId.$password."')
                        WHERE user_id = ".$user_id;

    if ($conn->query($update_login_sql) !== TRUE) {
        echo "Some error occurred while updating password. Please try again later.";
        exit();
    }
    
    if (!$conn -> commit()) {
        echo "Some error occurred while updating password. Please try again later.";
        exit();
    }else{
        echo "1";
    }

    $conn->close();
?>