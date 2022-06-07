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
    $total_progress_pre_requisite = 0;
    $total_entries_pre_requisite = 0;

    $get_dossier_pre_requisite_sql = "SELECT count(*) cnt FROM `product_dossier_pre_requisite` 
                            WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
    $result_dossier_pre_requisite = mysqli_query($conn,$get_dossier_pre_requisite_sql);
    while ($row_dossier_pre_requisite = mysqli_fetch_array($result_dossier_pre_requisite, MYSQLI_ASSOC)) {  
        $row_array['pre_requisite_check'] = $row_dossier_pre_requisite['cnt'] > 0 ? 100 : 0;
        $total_progress_pre_requisite += (int)$row_dossier_pre_requisite['cnt'] > 0 ? 1 : 0;
        $total_entries_pre_requisite++;
    }

    $get_dossier_check_sql = "SELECT count(*) cnt FROM `product_dossier_check` 
                            WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
    $result_dossier_check = mysqli_query($conn,$get_dossier_check_sql);
    while ($row_dossier_check = mysqli_fetch_array($result_dossier_check, MYSQLI_ASSOC)) {  
        $row_array['dossier_check'] = $row_dossier_check['cnt'] > 0 ? 100 : 0;
        $total_progress_pre_requisite += (int)$row_dossier_check['cnt'] > 0 ? 1 : 0;
        $total_entries_pre_requisite++;
    }

    $total_progress_population = 0;
    $total_entries_population = 0;

    $get_dossier_writing_sql = "SELECT count(*) cnt FROM `product_dossier_writing` 
                            WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
    $result_dossier_writing = mysqli_query($conn,$get_dossier_writing_sql);
    while ($row_dossier_writing = mysqli_fetch_array($result_dossier_writing, MYSQLI_ASSOC)) {  
        $row_array['dossier_writing'] = $row_dossier_writing['cnt'] > 0 ? 100 : 0;
        $total_progress_population += (int)$row_dossier_writing['cnt'] > 0 ? 1 : 0;
        $total_entries_population++;
    }

    $get_dossier_valid_marker_sql = "SELECT count(*) cnt FROM `product_dossier_valid_marker` 
                            WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
    $result_dossier_valid_marker = mysqli_query($conn,$get_dossier_valid_marker_sql);
    while ($row_dossier_valid_marker = mysqli_fetch_array($result_dossier_valid_marker, MYSQLI_ASSOC)) {  
        $row_array['dossier_marker'] = $row_dossier_valid_marker['cnt'] > 0 ? 100 : 0;
        $total_progress_population += (int)$row_dossier_valid_marker['cnt'] > 0 ? 1 : 0;
        $total_entries_population++;
    }

    // echo "progess=".$total_progress_pre_requisite;
    // echo "entries=".$total_entries_pre_requisite;
    $total_percent_pre_requisite = (int)$total_progress_pre_requisite / (int)$total_entries_pre_requisite * 100;
    $row_array['pre_requisite'] = $total_percent_pre_requisite;

    // echo "progess=".$total_progress_population;
    // echo "entries=".$total_entries_population;
    $total_percent_population = (int)$total_progress_population / (int)$total_entries_population * 100;
    $row_array['population'] = $total_percent_population;

    $total_progress_dossier = (int)$total_percent_pre_requisite + (int)$total_percent_population;
    $row_array['dossier'] = (int)$total_progress_dossier / 2;

    $json_response = array();
    array_push($json_response,$row_array);

    echo json_encode($json_response); 
    mysqli_free_result($result_dossier_pre_requisite);
    mysqli_free_result($result_dossier_check);
    mysqli_free_result($result_dossier_writing);
    mysqli_free_result($result_dossier_valid_marker);

    $conn->close();

?>