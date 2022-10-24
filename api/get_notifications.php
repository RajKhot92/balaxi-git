<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input   */
    $user_id = mysqli_real_escape_string($conn, $_REQUEST['user_id']);

    /*  Getting roles    */
    $get_notify_sql = "SELECT a.`notify_id`,a.`product_id`,a.`notify_type`, 
                        b.`product_name`,c.`country_name`,a.`ent_by`,a.`ent_dt` 
                        FROM notification_master a INNER JOIN product_master b ON a.`product_id`=b.`product_id` 
                        INNER JOIN country_master c ON b.`country_id`=c.`country_id` 
                        WHERE a.`status`='No' 
                        AND a.`del_by` IS NULL AND b.`del_by` IS NULL 
                        AND a.`user_id`=".$user_id." 
                        ORDER BY a.`ent_dt` DESC ";

    $result = mysqli_query($conn,$get_notify_sql);  
    $json_response = array();
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {  
        $row_array['notify_id'] = $row['notify_id'];
        $row_array['product_id'] = $row['product_id'];
        $row_array['product_name'] = $row['product_name'];
        $row_array['notify_type'] = $row['notify_type'];
        $row_array['country_name'] = $row['country_name'];
        $row_array['ent_by'] = $row['ent_by'];
        $row_array['ent_dt'] = $row['ent_dt'];       
        array_push($json_response,$row_array);  
    }

    echo json_encode($json_response); 
    mysqli_free_result($result);

    $conn->close();

?>