<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input   */
    $country_id = mysqli_real_escape_string($conn, $_REQUEST['country_id']);
    $last_days = mysqli_real_escape_string($conn, $_REQUEST['last_days']);
    
    /*  Getting roles    */
    $get_progress_sql = "SELECT a.`product_id`,b.`product_name`,c.`category_name`,
                    d.`user_name` as product_owner,e.`user_name` as executive,f.`menu_name`,
                    IFNULL(a.`progress`,0.00) progress,a.`updt_dt`
                    from product_step_master a INNER JOIN product_master b ON a.`product_id`=b.`product_id` 
                    INNER JOIN product_category c ON b.`product_category`=c.`category_id`
                    INNER JOIN user_master d on b.`product_owner`=d.`user_id`
                    INNER JOIN user_master e ON a.`user_id`=e.`user_id`
                    INNER JOIN menu_master f on a.`menu_id`=f.`menu_id`";

    if($country_id != 0){
        $get_progress_sql .= "WHERE b.`country_id`=".$country_id." 
                            AND a.`updt_dt` between ADDDATE(NOW(),-".$last_days.") AND NOW()";
    }else{
        $get_progress_sql .= "WHERE a.`updt_dt` between ADDDATE(NOW(),-".$last_days.") AND NOW()";
    }

    $result = mysqli_query($conn,$get_progress_sql);  
    $json_response = array();  
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {  
        $row_array['product_id'] = $row['product_id'];
        $row_array['product_name'] = $row['product_name'];
        $row_array['category_name'] = $row['category_name'];
        $row_array['product_owner'] = $row['product_owner'];
        $row_array['executive'] = $row['executive'];
        $row_array['menu_name'] = $row['menu_name'];
        $row_array['progress'] = $row['progress'];
        $row_array['updt_dt'] = $row['updt_dt'];
        array_push($json_response,$row_array);  
    }

    echo json_encode($json_response); 
    mysqli_free_result($result);

    $conn->close();

?>