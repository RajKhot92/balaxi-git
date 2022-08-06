<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input   */
    $country_id = mysqli_real_escape_string($conn, $_REQUEST['country_id']);
    
    /*  Getting roles    */
    $get_progress_sql = "SELECT a.`product_id`,a.`product_name`,b.`category_name`,
    					a.`deadline_dt`,c.`user_name` as product_owner,
    					IFNULL((SELECT ROUND(SUM(psm.progress)/7,2) FROM product_step_master psm WHERE psm.menu_id IN (9,10,11,12,13,14,15) and psm.product_id=a.product_id),0) progress
    					from product_master a INNER JOIN product_category b ON a.`product_category`=b.`category_id`
    					INNER JOIN user_master c on a.`product_owner`=c.`user_id`
                        WHERE a.`del_by` IS NULL  ";

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
        $row_array['progress'] = $row['progress'];
        array_push($json_response,$row_array);  
    }

    echo json_encode($json_response); 
    mysqli_free_result($result);

    $conn->close();

?>