<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input     */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
	$role_id = mysqli_real_escape_string($conn, $_REQUEST['role_id']);

    $conn -> autocommit(FALSE);

    if($login_id === "" || $role_id === ""){
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

    /*  Checking any user having role exists     */
    $user_exist = "SELECT count(user_id) cntUser FROM user_master 
                    WHERE user_role = ".$role_id."
                    AND del_by IS NULL";
    $user_response = mysqli_query($conn, $user_exist);
    $cntUser = 0;
    while ($row_user = mysqli_fetch_array($user_response, MYSQLI_ASSOC)) {
        $cntUser = $row_user["cntUser"];
    }
    if ($cntUser > 0){
        echo "Some users exist in the system having a role which you are deleting. You can not delete this role.";
        exit();
    }
    
    /*  Deleting role   */
    $delete_role_sql = "UPDATE user_roles 
                        SET del_by = ".$login_id.",del_dt = CURDATE()
                        WHERE role_id = ".$role_id;

    if ($conn->query($delete_role_sql) !== TRUE) {
        echo "Some error occurred while deleting role. Please try again later.";
        exit();
    }
    
    if (!$conn -> commit()) {
        echo "Some error occurred while deleting role. Please try again later.";
        exit();
    }else{
        echo "1";
    }

    $conn->close();
?>