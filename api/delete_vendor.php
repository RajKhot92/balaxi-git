<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input     */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
	$vendor_id = mysqli_real_escape_string($conn, $_REQUEST['vendor_id']);

    $conn -> autocommit(FALSE);

    if($login_id === "" || $vendor_id === ""){
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

    /*  Checking vendor already exists     */
    $vendor_exist = "SELECT vendor_id FROM vendor_master 
                    WHERE vendor_id = ".$vendor_id."
                    AND del_by IS NULL";
    $vendor_response = mysqli_query($conn, $vendor_exist);
    if (mysqli_num_rows($vendor_response) == 0){
        echo "Country details not found for vendor id ".$vendor_id." in the system.";
        exit();
    }
    
    /*  Deleting vendor   */
    $delete_vendor_sql = "UPDATE vendor_master 
                        SET del_by = ".$login_id.",del_dt = CURDATE()
                        WHERE vendor_id = ".$vendor_id;

    if ($conn->query($delete_vendor_sql) !== TRUE) {
        echo "Some error occurred while deleting vendor. Please try again later.";
        exit();
    }
    
    if (!$conn -> commit()) {
        echo "Some error occurred while deleting vendor. Please try again later.";
        exit();
    }else{
        echo "1";
    }

    $conn->close();
?>