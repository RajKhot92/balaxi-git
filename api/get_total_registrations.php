<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input   */
    $country_id = mysqli_real_escape_string($conn, $_REQUEST['country_id']);
    
    /*  Getting roles    */
    $get_progress_sql = "SELECT a.`product_id`,a.`product_name`,b.`registration_complete`,
                        b.`registration_expired`,c.`user_name` as product_owner
                        from product_master a INNER JOIN product_registration b ON a.`product_id`=b.`product_id`
                        INNER JOIN user_master c on a.`product_owner`=c.`user_id` ";

    if($country_id != 0){
        $get_progress_sql .= "WHERE a.`country_id`=".$country_id;
    }

    $result = mysqli_query($conn,$get_progress_sql);  
    $json_response = array();  
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {  
        $row_array['product_id'] = $row['product_id'];
        $row_array['product_name'] = $row['product_name'];
        $row_array['registration_complete'] = $row['registration_complete'];
        $row_array['registration_expired'] = $row['registration_expired'];
        $row_array['product_owner'] = $row['product_owner'];
        array_push($json_response,$row_array);  
    }

    echo json_encode($json_response); 
    mysqli_free_result($result);

    $conn->close();

?>