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

    $get_nomenclature_sql = "SELECT count(*) cnt FROM `product_nomenclature` 
                            WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
    $result_nomenclature = mysqli_query($conn,$get_nomenclature_sql);
    while ($row_nomenclature = mysqli_fetch_array($result_nomenclature, MYSQLI_ASSOC)) {  
        $row_array['nomenclature'] = $row_nomenclature['cnt'] > 0 ? 100 : 0;
        $total_progress += (int)$row_nomenclature['cnt'] > 0 ? 1 : 0;
        $total_entries++;
    }

    $get_details_sql = "SELECT count(*) cnt FROM `product_details` 
                            WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
    $result_details = mysqli_query($conn,$get_details_sql);
    while ($row_details = mysqli_fetch_array($result_details, MYSQLI_ASSOC)) {  
        $row_array['details'] = $row_details['cnt'] > 0 ? 100 : 0;
        $total_progress += (int)$row_details['cnt'] > 0 ? 1 : 0;
        $total_entries++;
    }

    $get_market_research_sql = "SELECT count(*) cnt FROM `product_market_research` 
                            WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
    $result_market_research = mysqli_query($conn,$get_market_research_sql);
    while ($row_market_research = mysqli_fetch_array($result_market_research, MYSQLI_ASSOC)) {  
        $row_array['market_research'] = $row_market_research['cnt'] > 0 ? 100 : 0;
        $total_progress += (int)$row_market_research['cnt'] > 0 ? 1 : 0;
        $total_entries++;
    }

    $get_procurement_sql = "SELECT count(*) cnt FROM `product_procurement` 
                            WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
    $result_procurement = mysqli_query($conn,$get_procurement_sql);
    while ($row_procurement = mysqli_fetch_array($result_procurement, MYSQLI_ASSOC)) {  
        $row_array['procurement'] = $row_procurement['cnt'] > 0 ? 100 : 0;
        $total_progress += (int)$row_procurement['cnt'] > 0 ? 1 : 0;
        $total_entries++;
    }
    // echo "progess=".$total_progress;
    // echo "entries=".$total_entries;
    $total_percent = (int)$total_progress / (int)$total_entries * 100;
    $row_array['data_capture'] = $total_percent;

    $json_response = array();
    array_push($json_response,$row_array);

    echo json_encode($json_response); 
    mysqli_free_result($result_nomenclature);
    mysqli_free_result($result_details);
    mysqli_free_result($result_market_research);
    mysqli_free_result($result_procurement);

    $conn->close();

?>