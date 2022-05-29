<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input     */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
	$vendor_name = mysqli_real_escape_string($conn, $_REQUEST['vendor_name']);

    $conn -> autocommit(FALSE);

    if($login_id === "" || $vendor_name === ""){
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
                    WHERE UPPER(vendor_name) = UPPER('".$vendor_name."')
                    AND del_by IS NULL";
    $vendor_response = mysqli_query($conn, $vendor_exist);
    if (mysqli_num_rows($vendor_response) > 0){
        echo "Entered vendor name is already exist in the system. Please try with other vendor name.";
        exit();
    }
    
    /*  Adding new vendor     */
    $add_country_sql = "INSERT INTO vendor_master (vendor_name,ent_by,ent_dt,del_by,del_dt)
                        VALUES (UPPER('".$vendor_name."'),".$login_id.",CURDATE(),null,null)";

    if ($conn->query($add_country_sql) !== TRUE) {
        echo "Some error occurred while adding new vendor. Please try again later.";
        exit();
    }
    
    if (!$conn -> commit()) {
        echo "Some error occurred while adding new vendor. Please try again later.";
        exit();
    }else{
        echo "1";
    }

    $conn->close();
?>