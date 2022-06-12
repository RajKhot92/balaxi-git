<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input   */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
    $user_id = mysqli_real_escape_string($conn, $_REQUEST['user_id']);
    $role_id = mysqli_real_escape_string($conn, $_REQUEST['role_id']);

    $get_logged_user_sql = "SELECT user_role FROM user_master WHERE user_id=".$login_id." AND del_by IS NULL";
    $logged_user_response = mysqli_query($conn, $get_logged_user_sql);
    $logged_user_role = 0;
    while ($row_logged_user = mysqli_fetch_array($logged_user_response, MYSQLI_ASSOC)) {  
    // while($row_logged_user = mysqli_fetch_array($logged_user_response, MYSQLI_ASSOC){
        $logged_user_role = $row_logged_user['user_role'];
    }

    /*  Getting user    */
    $get_user_sql = "SELECT a.`user_id`,a.`user_name`,a.`contact_no`,a.`email_id`,
                    a.`user_role`,a.`ent_by`,a.`ent_dt`,b.`role_name` 
                    FROM user_master a INNER JOIN user_roles b 
                    ON a.`user_role`=b.`role_id` 
                    WHERE a.`user_role` > ".$logged_user_role." AND a.`del_by` IS NULL";

    if($user_id != 0){
        /*  Checking user already exists     */
        $user_exist = "SELECT user_id FROM user_master 
                        WHERE user_id = ".$user_id."
                        AND user_role NOT IN (1,2) AND del_by IS NULL";
        $user_response = mysqli_query($conn, $user_exist);
        if (mysqli_num_rows($user_response) == 0){
            echo "User details not found for user id ".$user_id." in the system.";
            exit();
        }

        $get_user_sql .= " AND user_id = ".$user_id;
    }

    if($role_id != 0){
        /*  Checking role already exists     */
        $role_exist = "SELECT role_id FROM user_roles 
                        WHERE role_id = ".$role_id."
                        AND del_by IS NULL";
        $role_response = mysqli_query($conn, $role_exist);
        if (mysqli_num_rows($role_response) == 0){
            echo "Role details not found for role id ".$role_id." in the system.";
            exit();
        }

        $get_user_sql .= " AND role_id = ".$role_id;
    }

    $get_user_sql .= " ORDER BY user_id DESC ";

    $result = mysqli_query($conn,$get_user_sql);  
    $json_response = array();  
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {  
        $row_array['user_id'] = $row['user_id'];
        $row_array['user_name'] = $row['user_name'];
        $row_array['contact_no'] = $row['contact_no'];
        $row_array['email_id'] = $row['email_id'];
        $row_array['user_role'] = $row['user_role'];
        $row_array['role_name'] = $row['role_name'];
        $row_array['ent_by'] = $row['ent_by'];
        $row_array['ent_dt'] = $row['ent_dt'];  
        array_push($json_response,$row_array);  
    }

    echo json_encode($json_response); 
    mysqli_free_result($result);

    $conn->close();

?>