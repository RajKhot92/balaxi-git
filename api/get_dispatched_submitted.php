<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input   */
    $country_id = mysqli_real_escape_string($conn, $_REQUEST['country_id']);
    
    /*  Getting roles    */
        $get_progress_sql = "SELECT DISTINCT a.`product_id`,a.`product_name`,b.`category_name`,
                            (SELECT dispatch_dt FROM product_shipment_dispatch WHERE psd_id=(SELECT max(psd_id) FROM `product_shipment_dispatch` WHERE product_id=a.`product_id`)) dispatch_dt,
                            IF((SELECT COUNT(*) FROM product_submission ps WHERE ps.`product_id`=a.`product_id`) > 0, 'Submitted', 'Not Submitted') submission_status
                            from product_master a INNER JOIN product_category b ON a.`product_category`=b.`category_id` ";

    if($country_id != 0){
        $get_progress_sql .= "WHERE a.`country_id`=".$country_id;
    }

    $result = mysqli_query($conn,$get_progress_sql);  
    $json_response = array();  
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {  
        $row_array['product_id'] = $row['product_id'];
        $row_array['product_name'] = $row['product_name'];
        $row_array['category_name'] = $row['category_name'];
        $row_array['dispatch_dt'] = $row['dispatch_dt'];
        $row_array['submission_status'] = $row['submission_status'];
        array_push($json_response,$row_array);  
    }

    echo json_encode($json_response); 
    mysqli_free_result($result);

    $conn->close();

?>