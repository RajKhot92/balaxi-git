<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input   */
    $country_id = mysqli_real_escape_string($conn, $_REQUEST['country_id']);
    $user_id = mysqli_real_escape_string($conn, $_REQUEST['user_id']);
    $user_role = mysqli_real_escape_string($conn, $_REQUEST['user_role']);
    
    /*  Getting roles    */
    $get_progress_sql = "SELECT a.`product_id`,a.`product_name`,b.`category_name`,
    					a.`deadline_dt`,c.`user_name` as product_owner,
                    (SELECT `vendor_name` from product_vendor_finalization 
                        WHERE `vf_id` = (SELECT max(vf_id) FROM product_vendor_finalization WHERE `product_id` = a.`product_id` AND del_by IS NULL)) supplier_name  
    					from product_master a INNER JOIN product_category b ON a.`product_category`=b.`category_id`
    					INNER JOIN user_master c on a.`product_owner`=c.`user_id`
                        WHERE a.`product_id` NOT IN (
                            SELECT DISTINCT `product_id` FROM `product_step_master`
                        ) AND a.`del_by` IS NULL  ";

    if($user_role == 4){
        $get_progress_sql .= "AND a.`product_id` in (SELECT DISTINCT `product_id` from `product_step_master` where `user_id`=".$user_id.") ";
    }

    if($country_id != 0){
        $get_progress_sql .= "AND a.`country_id`=".$country_id;
    }

    $result = mysqli_query($conn,$get_progress_sql);  
    $json_response = array();  
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {  
        $row_array['product_id'] = $row['product_id'];
        $row_array['product_name'] = $row['product_name'];
        $row_array['category_name'] = $row['category_name'];
        $row_array['product_owner'] = $row['product_owner'];
        $row_array['deadline_dt'] = $row['deadline_dt'];
        $row_array['supplier_name'] = $row['supplier_name'] == null ? "-" : $row['supplier_name'];
        array_push($json_response,$row_array);  
    }

    echo json_encode($json_response); 
    mysqli_free_result($result);

    $conn->close();

?>