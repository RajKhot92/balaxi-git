<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input   */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
    $product_id = mysqli_real_escape_string($conn, $_REQUEST['product_id']);
    $psq_id = mysqli_real_escape_string($conn, $_REQUEST['psq_id']);
    $pqs_id = mysqli_real_escape_string($conn, $_REQUEST['pqs_id']);

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

    $query_exist = "SELECT psq_id FROM product_queries_received 
                WHERE psq_id = ".$psq_id;
    $query_response = mysqli_query($conn, $query_exist);
    if (mysqli_num_rows($query_response) == 0){
        echo "Query details not found for query id ".$psq_id." in the system.";
        exit();
    }

    /*  Getting artwork question    */
    $get_solution_sql = "SELECT * FROM product_queries_solution 
                        WHERE `product_id`=".$product_id." AND `ent_by`='".$login_id."' 
                        AND psq_id=".$psq_id." ";

    if($pqs_id != 0){
        $get_solution_sql .= " AND pqs_id = ".$pqs_id;
    }

    $get_solution_sql .= " ORDER BY pqs_id DESC ";

    $result = mysqli_query($conn,$get_solution_sql);  
    $json_response = array();  
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {  
        $row_array['pqs_id'] = $row['pqs_id'];
        $row_array['product_id'] = $row['product_id'];
        $row_array['psq_id'] = $row['psq_id'];
        $row_array['file_type'] = $row['file_type'];
        $row_array['file_url'] = $row['file_url'];
        $row_array['solution_desc'] = $row['solution_desc'];
        $row_array['ent_by'] = $row['ent_by'];
        $row_array['ent_dt'] = $row['ent_dt'];        
        array_push($json_response,$row_array);
    }

    echo json_encode($json_response); 
    mysqli_free_result($result);

    $conn->close();

?>