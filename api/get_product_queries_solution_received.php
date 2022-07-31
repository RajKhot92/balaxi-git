<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input   */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
    $product_id = mysqli_real_escape_string($conn, $_REQUEST['product_id']);
    $psq_id = mysqli_real_escape_string($conn, $_REQUEST['psq_id']);

    $login_exist = "SELECT user_id FROM user_master 
                    WHERE user_id = ".$login_id."
                    AND del_by IS NULL";
    $login_response = mysqli_query($conn, $login_exist);
    if (mysqli_num_rows($login_response) == 0){
        echo "User details not found for user id ".$login_id.".";
        exit();
    }

    $product_exist = "SELECT product_id FROM product_master 
                WHERE product_id = ".$product_id."
                AND del_by IS NULL";
    $product_response = mysqli_query($conn, $product_exist);
    if (mysqli_num_rows($product_response) == 0){
        echo "Product details not found for product id ".$product_id." in the system.";
        exit();
    }

    /*  Getting artwork question    */
    $get_solution_sql = "SELECT a.`psq_id`,a.`product_id`,c.`product_name`, 
                        a.`query_no`,a.`desc_box`,b.`pqs_id`,b.`solution_desc`, 
                        b.`file_url`,b.`file_type`,b.`ent_dt` 
                        FROM `product_queries_received` a 
                        INNER JOIN product_queries_solution b ON a.`psq_id`=b.`psq_id`
                        INNER JOIN product_master c ON a.`product_id`=c.`product_id` 
                        WHERE b.`pqs_id` IN (
                        SELECT MAX(pqs_id) FROM `product_queries_solution` GROUP by psq_id) 
                        AND a.`product_id`=".$product_id." AND a.`ent_by`=".$login_id;

    if($psq_id != 0){
        $get_solution_sql .= " AND a.`psq_id` = ".$psq_id;
    }

    $get_solution_sql .= " ORDER BY a.`psq_id` DESC ";
    
    $result = mysqli_query($conn,$get_solution_sql);  
    $json_response = array();  
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {  
        $row_array['psq_id'] = $row['psq_id'];
        $row_array['product_id'] = $row['product_id'];
        $row_array['product_name'] = $row['product_name'];
        $row_array['query_no'] = $row['query_no'];
        $row_array['desc_box'] = $row['desc_box'];
        $row_array['pqs_id'] = $row['pqs_id'];
        $row_array['solution_desc'] = $row['solution_desc'];
        $row_array['file_url'] = $row['file_url'];
        $row_array['file_type'] = $row['file_type'];
        $row_array['ent_dt'] = $row['ent_dt'];
        array_push($json_response,$row_array);
    }

    echo json_encode($json_response); 
    mysqli_free_result($result);

    $conn->close();

?>