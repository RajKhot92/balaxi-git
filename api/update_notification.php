<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input     */
    $notify_id = mysqli_real_escape_string($conn, $_REQUEST['notify_id']);

    $conn -> autocommit(FALSE);

    if($notify_id === ""){
        echo "Kindly provide valid input.";
        exit();
    }

    /*  Checking role access    */
    $admin_exist = "SELECT notify_id FROM notification_master 
                    WHERE notify_id = ".$notify_id." 
                    AND del_by IS NULL";
    $admin_response = mysqli_query($conn, $admin_exist);
    if (mysqli_num_rows($admin_response) == 0){
        echo "Invalid notification.";
        exit();
    }
    
    /*  Updating notification    */
    $update_notify_sql = "UPDATE notification_master 
                        SET status = 'Yes' WHERE notify_id = ".$notify_id;

    if ($conn->query($update_notify_sql) !== TRUE) {
        echo "Some error occurred while updating notification. Please try again later.";
        exit();
    }
    
    if (!$conn -> commit()) {
        echo "Some error occurred while updating notification. Please try again later.";
        exit();
    }else{
        echo "1";
    }

    $conn->close();
?>