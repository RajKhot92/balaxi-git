<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input   */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
    $product_id = mysqli_real_escape_string($conn, $_REQUEST['product_id']);
    $lr_id = mysqli_real_escape_string($conn, $_REQUEST['lr_id']);

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

    /*  Getting nomenclature    */
    $get_legal_receipt_sql = "SELECT * FROM product_legal_receipt WHERE product_id=".$product_id." AND ent_by='".$login_id."'";

    if($lr_id != 0){
        $get_legal_receipt_sql .= " AND lr_id = ".$lr_id;
    }

    $result = mysqli_query($conn,$get_legal_receipt_sql);  
    $json_response = array();  
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {  
        $row_array['lr_id'] = $row['lr_id'];
        $row_array['product_id'] = $row['product_id'];
        $row_array['gmp'] = $row['gmp'];
        $row_array['fsc'] = $row['fsc'];
        $row_array['copp'] = $row['copp'];
        $row_array['pp'] = $row['pp'] == null ? "" : $row['pp'];
        $row_array['license_manufacture'] = $row['license_manufacture'] == null ? "" : $row['license_manufacture'];
        $row_array['cma'] = $row['cma'];
        $row_array['ent_by'] = $row['ent_by'];
        $row_array['ent_dt'] = $row['ent_dt'];        
        array_push($json_response,$row_array);
    }

    echo json_encode($json_response); 
    mysqli_free_result($result);

    $conn->close();

?>