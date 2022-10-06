<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input   */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
    $product_id = mysqli_real_escape_string($conn, $_REQUEST['product_id']);
    $psv_id = mysqli_real_escape_string($conn, $_REQUEST['psv_id']);

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
    $get_validation_sql = "SELECT * FROM product_submission_valid WHERE product_id=".$product_id." AND ent_by='".$login_id."' 
                                AND del_by IS NULL ";

    if($psv_id != 0){
        $get_validation_sql .= " AND psv_id = ".$psv_id;
    }

    $get_validation_sql .= " ORDER BY psv_id DESC ";

    $result = mysqli_query($conn,$get_validation_sql);  
    $json_response = array();  
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {  
        $row_array['psv_id'] = $row['psv_id'];
        $row_array['product_id'] = $row['product_id'];
        $row_array['gmp'] = $row['gmp'];
        $row_array['fsc'] = $row['fsc'];
        $row_array['copp'] = $row['copp'];
        $row_array['pp'] = $row['pp'];
        $row_array['license_manufacture'] = $row['license_manufacture'];
        $row_array['cma'] = $row['cma'];
        $row_array['box'] = $row['box'];
        $row_array['ws_finish_product'] = $row['ws_finish_product'];
        $row_array['qnq'] = $row['qnq'];
        $row_array['signing_authority'] = $row['signing_authority'];
        $row_array['ent_by'] = $row['ent_by'];
        $row_array['ent_dt'] = $row['ent_dt'];        
        array_push($json_response,$row_array);
    }

    echo json_encode($json_response); 
    mysqli_free_result($result);

    $conn->close();

?>