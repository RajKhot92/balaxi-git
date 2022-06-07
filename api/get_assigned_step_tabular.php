<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input   */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);

    /*  Checking role access    */
    $login_exist = "SELECT user_role FROM user_master 
                    WHERE user_id = ".$login_id." 
                    AND user_role IN (1,2,3) AND del_by IS NULL";
    $login_response = mysqli_query($conn, $login_exist);
    if (mysqli_num_rows($login_response) == 0){
        echo "Invalid login id. User not found for given login id.";
        exit();
    }

    $role_id = 0;
    while ($row_login = mysqli_fetch_array($login_response, MYSQLI_ASSOC)) {
        $role_id = $row_login['user_role'];
    }

    $json_response = array();
    $product_step_arr = array();
    $get_products_sql = "SELECT a.`product_id`,a.`product_name`,a.`country_id`,
                        c.`country_name`,a.`product_owner`,b.`user_name`
                        FROM `product_master` a 
                        INNER JOIN `user_master` b ON a.`product_owner`=b.`user_id`
                        INNER JOIN `country_master` c ON a.`country_id`=c.`country_id` ";
    if($role_id == 3){
        $get_products_sql .= "WHERE a.`product_owner`=".$login_id." ";
    }
    $get_products_sql .= "ORDER BY a.`product_id`";
    $result_product = mysqli_query($conn,$get_products_sql);  
    while ($row_product = mysqli_fetch_array($result_product, MYSQLI_ASSOC)) {
        $row_array['product_id'] = $row_product['product_id'];
        $row_array['product_name'] = $row_product['product_name'];
        $row_array['country_id'] = $row_product['country_id'];
        $row_array['country_name'] = $row_product['country_name'];
        $row_array['product_owner'] = $row_product['product_owner'];
        $row_array['user_name'] = $row_product['user_name'];

        $product_step_arr = array();
        /*  Getting steps    */
        $get_steps_sql = "SELECT a.`menu_id`,e.`menu_name`,f.`user_name` executive,
                        IFNULL(a.`progress`,'0.00') progress
                        FROM `product_step_master` a 
                        INNER JOIN `menu_master` e ON a.`menu_id`=e.`menu_id`
                        INNER JOIN `user_master` f ON a.`user_id`=f.`user_id`
                        WHERE a.`product_id`=".$row_product['product_id']." ";
        if($role_id == 3){
            $get_steps_sql .= "AND a.`ent_by`=".$login_id." ";
        }
        $get_steps_sql .= "ORDER BY a.`menu_id`";
        // echo $get_steps_sql; 
        $result_steps = mysqli_query($conn,$get_steps_sql);  
        while ($row_steps = mysqli_fetch_array($result_steps, MYSQLI_ASSOC)) {
            $step_array['menu_id'] = $row_steps['menu_id'];
            $step_array['menu_name'] = $row_steps['menu_name'];
            $step_array['executive'] = $row_steps['executive'];
            $step_array['progress'] = $row_steps['progress'];

            array_push($product_step_arr, $step_array);
        }

        if(count($product_step_arr) > 0){
            $row_array['steps'] = $product_step_arr;
        }else{
            $row_array['steps'] = array();
        }
    
        array_push($json_response,$row_array);

        mysqli_free_result($result_steps);
    }

    echo json_encode($json_response); 
    mysqli_free_result($result_product);

    $conn->close();

?>