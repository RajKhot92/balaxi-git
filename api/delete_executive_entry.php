<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input     */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
	$tbl_nm = mysqli_real_escape_string($conn, $_REQUEST['tbl_nm']);
    $col_id = mysqli_real_escape_string($conn, $_REQUEST['col_id']);
    $col_val = mysqli_real_escape_string($conn, $_REQUEST['col_val']);

    $conn -> autocommit(FALSE);

    if($login_id === "" || $tbl_nm === ""){
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

    /*  Checking country already exists     */
    $col_exist = "SELECT ".$col_id." FROM ".$tbl_nm." 
                    WHERE ".$col_id." = ".$col_val." 
                    AND del_by IS NULL";
    $col_response = mysqli_query($conn, $col_exist);
    if (mysqli_num_rows($col_response) == 0){
        echo "Details not found for ".$col_id." ".$col_val." in the system.";
        exit();
    }
    
    /*  Deleting entry   */
    $delete_entry_sql = "UPDATE ".$tbl_nm." 
                        SET del_by = ".$login_id.",del_dt = NOW() 
                        WHERE ".$col_id." = ".$col_val;

    if ($conn->query($delete_entry_sql) !== TRUE) {
        echo "Some error occurred while deleting entry. Please try again later.";
        exit();
    }
    
    if (!$conn -> commit()) {
        echo "Some error occurred while deleting entry. Please try again later.";
        exit();
    }else{
        echo "1";
    }

    $conn->close();
?>