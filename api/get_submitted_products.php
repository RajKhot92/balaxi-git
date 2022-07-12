<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input   */
    $country_id = mysqli_real_escape_string($conn, $_REQUEST['country_id']);
    
    /*  Getting roles    */
    $get_progress_sql = "SELECT DISTINCT a.`product_id`,a.`product_name`,
                        (SELECT CONCAT(submitted_moh,'@#$',submission_slip_no) FROM product_submission 
                         WHERE ps_id=(SELECT max(ps_id) FROM `product_submission` WHERE product_id=a.`product_id`)) submitted_data,
                        IF((SELECT COUNT(*) FROM product_registration pr WHERE pr.`product_id`=a.`product_id`) > 0, 'Registered', 'Not Registered') registration_status
                        from product_master a INNER JOIN product_submission b ON a.`product_id`=b.`product_id` ";

    if($country_id != 0){
        $get_progress_sql .= "AND a.`country_id`=".$country_id;
    }

    $result = mysqli_query($conn,$get_progress_sql);  
    $json_response = array();  
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {  
        $row_array['product_id'] = $row['product_id'];
        $row_array['product_name'] = $row['product_name'];
        $submission_data = explode("@#$", $row['submitted_data']);
        $row_array['submitted_moh'] = $submission_data[0];
        $row_array['submission_slip_no'] = $submission_data[1];
        $row_array['registration_status'] = $row['registration_status'];
        array_push($json_response,$row_array);  
    }

    echo json_encode($json_response); 
    mysqli_free_result($result);

    $conn->close();

?>