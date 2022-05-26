<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input   */
    $user_role = mysqli_real_escape_string($conn, $_REQUEST['user_role']);
    $role_id = mysqli_real_escape_string($conn, $_REQUEST['role_id']);

    /*  Getting user roles    */
    $get_role_sql = "SELECT * FROM user_roles WHERE del_by IS NULL AND role_id > ".$user_role;

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

        $get_role_sql .= " AND role_id = ".$role_id;
    }

    $get_role_sql .= " order by role_id";

    $result = mysqli_query($conn,$get_role_sql);  
    $json_response = array();  
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {  
        $row_array['role_id'] = $row['role_id'];
        $row_array['role_name'] = $row['role_name'];
        $row_array['ent_by'] = $row['ent_by'];
        $row_array['ent_dt'] = $row['ent_dt'];        
        array_push($json_response,$row_array);  
    }

    echo json_encode($json_response); 
    mysqli_free_result($result);

    $conn->close();

?>