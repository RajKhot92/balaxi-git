<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input   */
    $country_id = mysqli_real_escape_string($conn, $_REQUEST['country_id']);
    $user_id = mysqli_real_escape_string($conn, $_REQUEST['user_id']);
    $user_role = mysqli_real_escape_string($conn, $_REQUEST['user_role']);
    
    /*  Getting roles    */
    $get_progress_sql = "SELECT a.`product_id`,a.`product_name`,b.`pr_id`,
                        b.`registration_complete`, b.`registration_expired`,c.`user_name` as product_owner,d.`category_name`  
                        from product_master a 
                        INNER JOIN product_registration b ON a.`product_id`=b.`product_id` 
                        INNER JOIN user_master c on a.`product_owner`=c.`user_id` 
                        INNER JOIN product_category d ON a.`product_category`=d.`category_id` 
                        WHERE b.`pr_id` IN (SELECT MAX(pr_id) from product_registration GROUP by product_id) AND a.`del_by` IS NULL  ";

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
        $row_array['registration_complete'] = $row['registration_complete'];
        $row_array['registration_expired'] = $row['registration_expired'];
        $row_array['product_owner'] = $row['product_owner'];
        array_push($json_response,$row_array);  
    }

    echo json_encode($json_response); 
    mysqli_free_result($result);

    $conn->close();

?>