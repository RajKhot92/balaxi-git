<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input     */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
    $user_id = mysqli_real_escape_string($conn, $_REQUEST['user_id']);
	
    $conn -> autocommit(FALSE);

    if($login_id === "" || $user_id === ""){
        echo "Kindly provide valid input.";
        exit();
    }

    /*  Checking role access    */
    $admin_exist = "SELECT user_id FROM user_master 
                    WHERE user_id = ".$login_id." AND user_role IN (1,2)
                    AND del_by IS NULL";
    $admin_response = mysqli_query($conn, $admin_exist);
    if (mysqli_num_rows($admin_response) == 0){
        echo "Invalid access. User don't have permission for this operation.";
        exit();
    }

    /*  Checking user already exists     */
    $user_exist = "SELECT user_id FROM user_master 
                    WHERE user_id = ".$user_id."
                    AND del_by IS NULL";
    $user_response = mysqli_query($conn, $user_exist);
    if (mysqli_num_rows($user_response) == 0){
        echo "User details not found for user id ".$user_id." in the system.";
        exit();
    }
    
    /*  Deleting user   */
    $delete_user_sql = "UPDATE user_master 
                        SET del_by = ".$login_id.",del_dt = CURDATE()
                        WHERE user_id = ".$user_id;

    if ($conn->query($delete_user_sql) !== TRUE) {
        echo "Some error occurred while deleting user. Please try again later.";
        exit();
    }
    
    if (!$conn -> commit()) {
        echo "Some error occurred while deleting user. Please try again later.";
        exit();
    }else{
        echo "1";
    }

    $conn->close();
?>