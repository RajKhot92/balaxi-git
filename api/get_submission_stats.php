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
    $total_progress_translation = 0;
    $total_entries_translation = 0;

    $get_translation_sql = "SELECT count(*) cnt FROM `product_translation` 
                            WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
    $result_translation = mysqli_query($conn,$get_translation_sql);
    while ($row_translation = mysqli_fetch_array($result_translation, MYSQLI_ASSOC)) {  
        $row_array['translation'] = $row_translation['cnt'] > 0 ? 100 : 0;
        $total_progress_translation += (int)$row_translation['cnt'] > 0 ? 1 : 0;
        $total_entries_translation++;
    }

    $get_translation_received_sql = "SELECT count(*) cnt FROM `product_translation_received` 
                            WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
    $result_translation_received = mysqli_query($conn,$get_translation_received_sql);
    while ($row_translation_received = mysqli_fetch_array($result_translation_received, MYSQLI_ASSOC)) {  
        $row_array['translation_received'] = $row_translation_received['cnt'] > 0 ? 100 : 0;
        $total_progress_translation += (int)$row_translation_received['cnt'] > 0 ? 1 : 0;
        $total_entries_translation++;
    }

    // echo "progess=".$total_progress_translation;
    // echo "entries=".$total_entries_translation;
    $total_percent_translation = (int)$total_progress_translation / (int)$total_entries_translation * 100;
    $row_array['translation_main'] = $total_percent_translation;

    
    $total_progress_submission = 0;
    $total_entries_submission = 0;

    $get_validation_sql = "SELECT count(*) cnt FROM `product_submission_valid` 
                            WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
    $result_validation = mysqli_query($conn,$get_validation_sql);
    while ($row_validation = mysqli_fetch_array($result_validation, MYSQLI_ASSOC)) {  
        $row_array['validation'] = $row_validation['cnt'] > 0 ? 100 : 0;
        $total_progress_submission += (int)$row_validation['cnt'] > 0 ? 1 : 0;
        $total_entries_submission++;
    }

    $get_legalization_sql = "SELECT count(*) cnt FROM `product_submission_legal` 
                            WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
    $result_legalization = mysqli_query($conn,$get_legalization_sql);
    while ($row_legalization = mysqli_fetch_array($result_legalization, MYSQLI_ASSOC)) {  
        $row_array['legalization'] = $row_legalization['cnt'] > 0 ? 100 : 0;
        $total_progress_submission += (int)$row_legalization['cnt'] > 0 ? 1 : 0;
        $total_entries_submission++;
    }

    $get_submission_sql = "SELECT count(*) cnt FROM `product_submission` 
                            WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
    $result_submission = mysqli_query($conn,$get_submission_sql);
    while ($row_submission = mysqli_fetch_array($result_submission, MYSQLI_ASSOC)) {  
        $row_array['submission'] = $row_submission['cnt'] > 0 ? 100 : 0;
        $total_progress_submission += (int)$row_submission['cnt'] > 0 ? 1 : 0;
        $total_entries_submission++;
    }

    $get_registration_sql = "SELECT count(*) cnt FROM `product_registration` 
                            WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
    $result_registration = mysqli_query($conn,$get_registration_sql);
    while ($row_registration = mysqli_fetch_array($result_registration, MYSQLI_ASSOC)) {  
        $row_array['registration'] = $row_registration['cnt'] > 0 ? 100 : 0;
        $total_progress_submission += (int)$row_registration['cnt'] > 0 ? 1 : 0;
        $total_entries_submission++;
    }

    // echo "progess=".$total_progress_submission;
    // echo "entries=".$total_entries_submission;
    $total_percent_submission = (int)$total_progress_submission / (int)$total_entries_submission * 100;
    $row_array['submission_main'] = $total_percent_submission;

    $total_progress_submission_menu = (int)$total_percent_translation + (int)$total_percent_submission;
    $row_array['submission_menu'] = (int)$total_progress_submission_menu / 2;

    $json_response = array();
    array_push($json_response,$row_array);

    echo json_encode($json_response); 
    mysqli_free_result($result_translation);
    mysqli_free_result($result_translation_received);
    mysqli_free_result($result_validation);
    mysqli_free_result($result_legalization);
    mysqli_free_result($result_submission);
    mysqli_free_result($result_registration);

    $conn->close();

?>