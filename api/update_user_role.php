<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input     */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
	$role_id = mysqli_real_escape_string($conn, $_REQUEST['role_id']);
    $role_name = mysqli_real_escape_string($conn, $_REQUEST['role_name']);

    $conn -> autocommit(FALSE);

    if($login_id === "" || $role_id === "" || $role_name === ""){
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

    /*  Checking role already exists     */
    $role_exist = "SELECT role_id FROM user_roles 
                    WHERE role_id = ".$role_id."
                    AND del_by IS NULL";
    $role_response = mysqli_query($conn, $role_exist);
    if (mysqli_num_rows($role_response) == 0){
        echo "Role details not found for role id ".$role_id." in the system.";
        exit();
    }

    /*  Checking role name already exists     */
    $role_name_exist = "SELECT role_id FROM user_roles 
                        WHERE role_id != ".$role_id." 
                        AND UPPER(role_name) = UPPER('".$role_name."')
                        AND del_by IS NULL";
    $role_name_response = mysqli_query($conn, $role_name_exist);
    if (mysqli_num_rows($role_name_response) > 0){
        echo "Entered role name is already exist in the system. Please try with other role name.";
        exit();
    }
    
    /*  Updating role name    */
    $update_role_sql = "UPDATE user_roles 
                        SET role_name = UPPER('".$role_name."')
                        WHERE role_id = ".$role_id;

    if ($conn->query($update_role_sql) !== TRUE) {
        echo "Some error occurred while updating role. Please try again later.";
        exit();
    }
    
    if (!$conn -> commit()) {
        echo "Some error occurred while updating role. Please try again later.";
        exit();
    }else{
        echo "1";
    }

    $conn->close();
?>