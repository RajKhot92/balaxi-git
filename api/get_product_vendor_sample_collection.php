<?php
    header('Access-Control-Allow-Origin: *');
    include "db_connect.php";

    /*  Taking user input   */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
    $product_id = mysqli_real_escape_string($conn, $_REQUEST['product_id']);
    $vsc_id = mysqli_real_escape_string($conn, $_REQUEST['vsc_id']);

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
    $get_vendor_doc_collection_sql = "SELECT * FROM product_vendor_sample_collection WHERE product_id=".$product_id." AND ent_by='".$login_id."'";

    if($vsc_id != 0){
        $get_vendor_doc_collection_sql .= " AND vsc_id = ".$vsc_id;
    }

    $get_vendor_doc_collection_sql .= " ORDER BY vsc_id DESC ";

    $result = mysqli_query($conn,$get_vendor_doc_collection_sql);  
    $json_response = array();  
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {  
        $row_array['vsc_id'] = $row['vsc_id'];
        $row_array['product_id'] = $row['product_id'];
        $row_array['work_standard_received_dt'] = $row['work_standard_received_dt'];
        $row_array['work_standard_finalize_dt'] = $row['work_standard_finalize_dt'];
        $row_array['work_standard_remark'] = $row['work_standard_remark'];
        $row_array['finish_product_received_dt'] = $row['finish_product_received_dt'];
        $row_array['finish_product_finalize_dt'] = $row['finish_product_finalize_dt'];
        $row_array['finish_product_remark'] = $row['finish_product_remark'];
        $row_array['ent_by'] = $row['ent_by'];
        $row_array['ent_dt'] = $row['ent_dt'];        
        array_push($json_response,$row_array);
    }

    echo json_encode($json_response); 
    mysqli_free_result($result);

    $conn->close();

?>