<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input   */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
    $product_id = mysqli_real_escape_string($conn, $_REQUEST['product_id']);

    /*  Checking role access    */
    $admin_exist = "SELECT user_id FROM user_master 
                    WHERE user_id = ".$login_id." AND user_role IN (1,2,3)
                    AND del_by IS NULL";
    $admin_response = mysqli_query($conn, $admin_exist);
    if (mysqli_num_rows($admin_response) == 0){
        echo "Invalid access. User don't have permission for this operation.";
        exit();
    }

    /*  Getting user roles    */
    $get_product_step_sql = "SELECT * FROM product_step_master WHERE ent_by=".$login_id." AND product_id=".$product_id;

    $result = mysqli_query($conn,$get_product_step_sql);  
    $json_response = array();  
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {  
        $row_array['psm_id'] = $row['psm_id'];
        $row_array['product_id'] = $row['product_id'];
        $row_array['menu_id'] = $row['menu_id'];
        $row_array['user_id'] = $row['user_id'];
        $row_array['progress'] = $row['progress'];
        $row_array['ent_by'] = $row['ent_by'];
        $row_array['ent_dt'] = $row['ent_dt'];        
        array_push($json_response,$row_array);  
    }

    echo json_encode($json_response); 
    mysqli_free_result($result);

    $conn->close();

?>