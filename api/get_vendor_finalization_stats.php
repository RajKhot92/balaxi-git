<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input   */
    $user_id = mysqli_real_escape_string($conn, $_REQUEST['user_id']);
    $menu_id = mysqli_real_escape_string($conn, $_REQUEST['menu_id']);
    $product_id = mysqli_real_escape_string($conn, $_REQUEST['product_id']);

    if($user_id === "" || $menu_id === "" || $product_id === ""){
        echo "Kindly provide valid input.";
        exit();
    }

    $user_exist = "SELECT user_id FROM user_master 
                WHERE user_id = ".$user_id."
                AND del_by IS NULL";
    $user_response = mysqli_query($conn, $user_exist);
    if (mysqli_num_rows($user_response) == 0){
        echo "User details not found for user id ".$user_id." in the system.";
        exit();
    }

    $menu_exist = "SELECT menu_id FROM menu_master 
                WHERE menu_id = ".$menu_id;
    $menu_response = mysqli_query($conn, $menu_exist);
    if (mysqli_num_rows($menu_response) == 0){
        echo "Menu details not found for menu id ".$menu_id." in the system.";
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

    /*  Getting Progress    */
    $total_progress = 0;
    $total_entries = 0;

    $get_finalization_sql = "SELECT count(*) cnt FROM `product_vendor_finalization` 
                            WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
    $result_finalization = mysqli_query($conn,$get_finalization_sql);
    while ($row_finalization = mysqli_fetch_array($result_finalization, MYSQLI_ASSOC)) {  
        $row_array['finalization'] = $row_finalization['cnt'] > 0 ? 100 : 0;
        $total_progress += (int)$row_finalization['cnt'] > 0 ? 1 : 0;
        $total_entries++;
    }

    $get_doc_collection_sql = "SELECT count(*) cnt FROM `product_vendor_doc_collection` 
                            WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
    $result_doc_collection = mysqli_query($conn,$get_doc_collection_sql);
    while ($row_doc_collection = mysqli_fetch_array($result_doc_collection, MYSQLI_ASSOC)) {  
        $row_array['doc_collection'] = $row_doc_collection['cnt'] > 0 ? 100 : 0;
        $total_progress += (int)$row_doc_collection['cnt'] > 0 ? 1 : 0;
        $total_entries++;
    }

    $get_sample_collection_sql = "SELECT count(*) cnt FROM `product_vendor_sample_collection` 
                            WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
    $result_sample_collection = mysqli_query($conn,$get_sample_collection_sql);
    while ($row_sample_collection = mysqli_fetch_array($result_sample_collection, MYSQLI_ASSOC)) {  
        $row_array['sample_collection'] = $row_sample_collection['cnt'] > 0 ? 100 : 0;
        $total_progress += (int)$row_sample_collection['cnt'] > 0 ? 1 : 0;
        $total_entries++;
    }

    // echo "progess=".$total_progress;
    // echo "entries=".$total_entries;
    $total_percent = (int)$total_progress / (int)$total_entries * 100;
    $row_array['vendor_management'] = $total_percent;

    $row_array['vendor_finalize'] = $total_percent;

    $json_response = array();
    array_push($json_response,$row_array);

    echo json_encode($json_response); 
    mysqli_free_result($result_finalization);
    mysqli_free_result($result_sample_collection);
    mysqli_free_result($result_doc_collection);

    $conn->close();

?>