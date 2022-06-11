<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input   */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
    $product_id = mysqli_real_escape_string($conn, $_REQUEST['product_id']);
    $vdc_id = mysqli_real_escape_string($conn, $_REQUEST['vdc_id']);

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
    $get_vendor_doc_collection_sql = "SELECT * FROM product_vendor_doc_collection WHERE product_id=".$product_id." AND ent_by='".$login_id."'";

    if($vdc_id != 0){
        $get_vendor_doc_collection_sql .= " AND vdc_id = ".$vdc_id;
    }

    $result = mysqli_query($conn,$get_vendor_doc_collection_sql);  
    $json_response = array();  
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {  
        $row_array['vdc_id'] = $row['vdc_id'];
        $row_array['product_id'] = $row['product_id'];
        $row_array['gmp_received_dt'] = $row['gmp_received_dt'];
        $row_array['gmp_finalize_dt'] = $row['gmp_finalize_dt'];
        $row_array['gmp_remark'] = $row['gmp_remark'];
        $row_array['manufacture_received_dt'] = $row['manufacture_received_dt'] == null ? "" : $row['manufacture_received_dt'];
        $row_array['manufacture_finalize_dt'] = $row['manufacture_finalize_dt'] == null ? "" : $row['manufacture_finalize_dt'];
        $row_array['manufacture_remark'] = $row['manufacture_remark'];
        $row_array['fsc_copp_received_dt'] = $row['fsc_copp_received_dt'];
        $row_array['fsc_copp_finalize_dt'] = $row['fsc_copp_finalize_dt'];
        $row_array['fsc_copp_remark'] = $row['fsc_copp_remark'];
        $row_array['pp_received_dt'] = $row['pp_received_dt'] == null ? "" : $row['pp_received_dt'];
        $row_array['pp_finalize_dt'] = $row['pp_finalize_dt'] == null ? "" : $row['pp_finalize_dt'];
        $row_array['pp_remark'] = $row['pp_remark'];
        $row_array['cma_received_dt'] = $row['cma_received_dt'];
        $row_array['cma_finalize_dt'] = $row['cma_finalize_dt'];
        $row_array['cma_remark'] = $row['cma_remark'];
        $row_array['ent_by'] = $row['ent_by'];
        $row_array['ent_dt'] = $row['ent_dt'];        
        array_push($json_response,$row_array);
    }

    echo json_encode($json_response); 
    mysqli_free_result($result);

    $conn->close();

?>