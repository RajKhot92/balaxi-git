<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input   */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);

    /*  Checking user exist    */
    $user_exist = "SELECT user_role FROM user_master 
                    WHERE user_id = ".$login_id." 
                    AND del_by IS NULL";
    $user_response = mysqli_query($conn, $user_exist);
    if (mysqli_num_rows($user_response) == 0){
        echo "Invalid user. User details not found for user id ".$login_id.".";
        exit();
    }

    $role_id = 0;
    while($row_user = mysqli_fetch_array($user_response, MYSQLI_ASSOC)){
        $role_id = $row_user['user_role'];
    }

    /*  Getting menus    */
    $get_menu_sql = "SELECT * FROM `menu_master` WHERE `parent_menu` IS NULL AND menu_id in (
    				SELECT menu_id FROM `menu_roles` WHERE role_id=".$role_id.")
    				order by menu_id,seq";

    $result = mysqli_query($conn,$get_menu_sql);
    $json_response = array();

    $product_wise_menu_id_arr = array();
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $row_array = array();
        $json_response_sub = array();
        $json_response_sub_menu_product = array();
        
        $product_update_menu_id = $row['menu_id'];
        
        $row_array['menu_id'] = $row['menu_id'];
        $row_array['menu_name'] = $row['menu_name'];
        $row_array['parent_menu'] = $row['parent_menu'];
        $row_array['seq'] = $row['seq'];
        $row_array['menu_link'] = $row['menu_link'];
        $row_array['menu_icon'] = $row['menu_icon'];

        $get_sub_menu_sql = "SELECT * FROM `menu_master` 
        					WHERE `parent_menu`=".$row['menu_id']."
                            AND menu_id in (
                            SELECT menu_id FROM `menu_roles` WHERE role_id=".$role_id.")
							order by `menu_id`,`seq`";
		$result_sub = mysqli_query($conn,$get_sub_menu_sql);
		while ($row_sub = mysqli_fetch_array($result_sub, MYSQLI_ASSOC)) {
			$row_array_sub['menu_id'] = $row_sub['menu_id'];
	        $row_array_sub['menu_name'] = $row_sub['menu_name'];
    	    $row_array_sub['parent_menu'] = $row_sub['parent_menu'];
        	$row_array_sub['seq'] = $row_sub['seq'];
        	$row_array_sub['menu_link'] = $row_sub['menu_link'];
        	$row_array_sub['menu_icon'] = $row_sub['menu_icon'];
			array_push($json_response_sub,$row_array_sub);	
		}
        if(count($json_response_sub) > 0){
            $row_array['sub_menu'] = $json_response_sub;
        }

        //Fetch menu product assigned step wise
        if(($role_id == 3 || $role_id == 4) && $product_update_menu_id == 8){
            $get_product_wise_menu_sql = "SELECT DISTINCT `product_id`,`menu_id` 
                                        FROM `product_step_master` 
                                        WHERE `user_id`=".$login_id;
            $result_product_wise = mysqli_query($conn,$get_product_wise_menu_sql);
            while ($row_product_wise = mysqli_fetch_array($result_product_wise, MYSQLI_ASSOC)) {

                if(in_array($row_product_wise['menu_id'], $product_wise_menu_id_arr)){
                    //nothing
                }else{
                    $get_sub_menu_product_wise_sql = "SELECT * FROM `menu_master` 
                                                    WHERE `parent_menu`=".$product_update_menu_id."
                                                    AND menu_id = ".$row_product_wise['menu_id']." 
                                                    order by `menu_id`,`seq`";
                    $result_sub_menu_product = mysqli_query($conn,$get_sub_menu_product_wise_sql);
                    while ($row_sub_menu_product = mysqli_fetch_array($result_sub_menu_product, MYSQLI_ASSOC)) {
                        $row_array_sub_menu_product['menu_id'] = $row_sub_menu_product['menu_id'];
                        $row_array_sub_menu_product['menu_name'] = $row_sub_menu_product['menu_name'];
                        $row_array_sub_menu_product['parent_menu'] = $row_sub_menu_product['parent_menu'];
                        $row_array_sub_menu_product['seq'] = $row_sub_menu_product['seq'];
                        $row_array_sub_menu_product['menu_link'] = $row_sub_menu_product['menu_link'];
                        $row_array_sub_menu_product['menu_icon'] = $row_sub_menu_product['menu_icon'];
                        
                        array_push($json_response_sub_menu_product,$row_array_sub_menu_product);  
                    }

                    array_push($product_wise_menu_id_arr,$row_product_wise['menu_id']);
                }
            }
            if(count($json_response_sub_menu_product) > 0){
                $row_array['sub_menu'] = $json_response_sub_menu_product;
            }
        }

        if(count($json_response_sub_menu_product)==0 && count($json_response_sub)==0){
            $row_array['sub_menu'] = array();
        }

        array_push($json_response,$row_array);  
    }

    echo json_encode($json_response); 
    mysqli_free_result($result);

    $conn->close();

?>