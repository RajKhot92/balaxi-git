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

    /*  Getting market research    */
    $get_queries_sql = "SELECT a.`psq_id`,a.`product_id`,a.`received_date`,a.`query_no`,
                        a.`desc_box`,a.`deadline_dt`,a.`ent_by`,a.`ent_dt`,
                        b.`category_name`,c.`product_name`,d.`country_name`
                        FROM product_queries_received a 
                        INNER JOIN query_category b ON a.`query_category`=b.`category_id`
                        INNER JOIN product_master c ON a.`product_id`=c.`product_id` 
                        INNER JOIN country_master d ON c.`country_id`=d.`country_id`
                        WHERE a.`product_id`=".$product_id." AND a.`ent_by`=".$login_id."  
                                AND a.del_by IS NULL ";

    if($psq_id != 0){
        $get_queries_sql .= " AND a.`psq_id` = ".$psq_id;
    }

    $get_queries_sql .= " ORDER BY a.`psq_id` DESC ";

    $result = mysqli_query($conn,$get_queries_sql);  
    $json_response = array();  
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {  
        $row_array['psq_id'] = $row['psq_id'];
        $row_array['product_id'] = $row['product_id'];
        $row_array['product_name'] = $row['product_name'];
        $row_array['received_date'] = $row['received_date'];
        $row_array['query_no'] = $row['query_no'];
        $row_array['desc_box'] = $row['desc_box'];
        $row_array['category_name'] = $row['category_name'];
        $row_array['deadline_dt'] = $row['deadline_dt'];
        $row_array['country_name'] = $row['country_name'];
        $row_array['ent_by'] = $row['ent_by'];
        $row_array['ent_dt'] = $row['ent_dt'];
        array_push($json_response,$row_array);
    }

    echo json_encode($json_response); 
    mysqli_free_result($result);

    $conn->close();

?>