<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input   */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);

    /*  Getting roles    */
    $get_steps_sql = "SELECT b.`product_id`,b.`product_name`,b.`country_id`,
					d.`country_name`,b.`product_owner`,c.`user_name`,
                    e.`menu_id`,e.`menu_name`,IFNULL(a.`progress`,'0.00') progress,
                    (SELECT `vendor_name` from product_vendor_finalization 
                        WHERE `vf_id` = (SELECT max(vf_id) FROM product_vendor_finalization WHERE `product_id` = a.`product_id`)) supplier_name
					FROM `product_step_master` a 
					INNER JOIN `product_master` b ON a.`product_id`=b.`product_id` 
					INNER JOIN `user_master` c ON b.`product_owner`=c.`user_id` 
					INNER JOIN `country_master` d ON b.`country_id`=d.`country_id` 
					INNER JOIN `menu_master` e ON a.`menu_id`=e.`menu_id` 
					WHERE a.`user_id`=".$login_id." 
                    AND b.`product_category` NOT IN (SELECT `category_id` FROM `product_category` WHERE UPPER(`category_name`)=UPPER('REGISTERED') OR UPPER(`category_name`)=UPPER('CLOSED'))";

    $result = mysqli_query($conn,$get_steps_sql);  
    $json_response = array();  
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {  
        $row_array['product_id'] = $row['product_id'];
        $row_array['product_name'] = $row['product_name'];
        $row_array['country_id'] = $row['country_id'];
        $row_array['country_name'] = $row['country_name'];
        $row_array['product_owner'] = $row['product_owner'];
        $row_array['user_name'] = $row['user_name'];
        $row_array['menu_id'] = $row['menu_id'];
        $row_array['menu_name'] = $row['menu_name'];
        $row_array['progress'] = $row['progress'];
        $row_array['supplier_name'] = $row['supplier_name'];
        array_push($json_response,$row_array);  
    }

    echo json_encode($json_response); 
    mysqli_free_result($result);

    $conn->close();

?>