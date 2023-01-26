<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input   */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
    $product_id = mysqli_real_escape_string($conn, $_REQUEST['product_id']);
    $pp_id = mysqli_real_escape_string($conn, $_REQUEST['pp_id']);

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
    $get_pp_sql = "SELECT * FROM product_pp WHERE product_id=".$product_id." AND ent_by='".$login_id."' 
                                AND del_by IS NULL ";

    if($pp_id != 0){
        $get_pp_sql .= " AND pp_id = ".$pp_id;
    }

    $get_pp_sql .= " ORDER BY pp_id DESC ";

    $result = mysqli_query($conn,$get_pp_sql);  
    $json_response = array();  
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {  
        $row_array['pp_id'] = $row['pp_id'];
        $row_array['product_id'] = $row['product_id'];
        $row_array['strength'] = $row['strength'];
        $row_array['composition'] = $row['composition'];
        $row_array['pharmacopeia_type'] = $row['pharmacopeia_type'];
        $row_array['validity'] = $row['validity'] == null ? "" : $row['validity'];
        $row_array['received_date'] = $row['received_date'] == null ? "" : $row['received_date'];
        $row_array['file_type'] = $row['file_type'];
        $row_array['file_url'] = $row['file_url'] == null ? "" : $row['file_url'];
        $row_array['ent_by'] = $row['ent_by'];
        $row_array['ent_dt'] = $row['ent_dt'];        
        array_push($json_response,$row_array);
    }

    echo json_encode($json_response); 
    mysqli_free_result($result);

    $conn->close();

?>