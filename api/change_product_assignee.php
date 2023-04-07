<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input     */
    $user_id = mysqli_real_escape_string($conn, $_REQUEST['user_id']);
    $current_user_id = mysqli_real_escape_string($conn, $_REQUEST['current_user_id']);
    $delegatee_id = mysqli_real_escape_string($conn, $_REQUEST['delegatee_id']);
	
    $conn -> autocommit(FALSE);

    if($user_id === "" || $current_user_id === "" || $delegatee_id === ""){
        echo "Kindly provide valid input.";
        exit();
    }

    /*  Checking role access    */
    $admin_exist = "SELECT user_id FROM user_master 
                    WHERE user_id = ".$user_id." AND user_role IN (1,2)
                    AND del_by IS NULL";
    $admin_response = mysqli_query($conn, $admin_exist);
    if (mysqli_num_rows($admin_response) == 0){
        echo "Invalid access. User don't have permission for this operation.";
        exit();
    }

    /*  Checking current user already exists     */
    $current_user_exist = "SELECT user_id FROM user_master 
                    WHERE user_id = ".$current_user_id."
                    AND del_by IS NULL";
    $current_user_response = mysqli_query($conn, $current_user_exist);
    if (mysqli_num_rows($current_user_response) == 0){
        echo "User details not found for current user id ".$current_user_id." in the system.";
        exit();
    }

    /*  Checking delegatee already exists     */
    $delegatee_exist = "SELECT user_id FROM user_master 
                    WHERE user_id = ".$delegatee_id."
                    AND del_by IS NULL";
    $delegatee_response = mysqli_query($conn, $delegatee_exist);
    if (mysqli_num_rows($delegatee_response) == 0){
        echo "User details not found for delegatee id ".$delegatee_id." in the system.";
        exit();
    }
    
    $failed_data = 0;
    /*  Delegating logic   */
    $user_data_tables = array('product_artwork','product_artwork_finalize','product_artwork_question','product_certificate_analysis','product_cma','product_artwork','product_artwork_finalize','product_commercial_doc','product_dossier_check','product_dossier_pre_requisite','product_dossier_valid_marker','product_dossier_writing','product_fsc','product_gmp','product_license_manufacture','product_misc','product_nomenclature','product_qnq_formula','product_shipment_booking','product_shipment_box_prep','product_shipment_dhl','product_shipment_dispatch','notification_master','product_artwork_monogram','product_copp','product_dossier_valid_checker','product_dossier_valid_reviewer','product_finish_product','product_lab_test','product_legal_receipt','product_legal_submission','product_market_research','product_physical_check','product_pp','product_procurement','product_queries_received','product_queries_solution','product_queries_submitted','product_registration','product_shipment_dhl_feedback','product_shipment_msds','product_submission','product_submission_legal','product_submission_valid','product_test_report','product_translation','product_translation_received','product_vendor_doc_collection','product_vendor_finalization','product_vendor_sample_collection','product_work_standard');

    for ($i = 0; $i < count($user_data_tables); $i++) {
        $check_user_data_exist = "SELECT COUNT(*) FROM ".$user_data_tables[$i]." WHERE ENT_BY=".$current_user_id;
        $check_user_data_response = mysqli_query($conn, $check_user_data_exist);
        if (mysqli_num_rows($check_user_data_response) > 0){
            // echo "User data found for current user id ".$current_user_id." in the system.";
            // exit();
            $update_user_with_delegate = "UPDATE ".$user_data_tables[$i]." 
                                        SET ENT_BY=".$delegatee_id."
                                        WHERE ENT_BY=".$current_user_id;
            if ($conn->query($update_user_with_delegate) !== TRUE) {
                $failed_data++;
            }
        }
    }
    
    if($failed_data > 0){
        echo "Some error occurred while delegating user data. Please try again later.";
        exit();
    }

    $check_user_step_exist = "SELECT COUNT(*) FROM product_step_master WHERE USER_ID=".$current_user_id;
    $check_user_step_response = mysqli_query($conn, $check_user_step_exist);
    if (mysqli_num_rows($check_user_step_response) > 0){
        // echo "User data found for current user id ".$current_user_id." in the system.";
        // exit();
        $update_user_step_with_delegate = "UPDATE product_step_master 
                                    SET USER_ID=".$delegatee_id."
                                    WHERE USER_ID=".$current_user_id;
        if ($conn->query($update_user_step_with_delegate) !== TRUE) {
            echo "Some error occurred while delegating user step details. Please try again later.";
            exit();
        }
    }

    if (!$conn -> commit()) {
        echo "Some error occurred while performing user delegation process. Please try again later.";
        exit();
    }else{
        echo "1";
    }

    $conn->close();
?>