<?php
	header('Access-Control-Allow-Origin: *');
	
    function deleteAllSteps($user_id,$product_id,$conn){
        $retval = "0";
        
        $steps_arr = array("product_artwork","product_artwork_finalize","product_artwork_question","product_certificate_analysis","product_cma","product_commercial_doc","product_copp","product_details","product_dossier_check","product_dossier_pre_requisite","product_dossier_valid_checker","product_dossier_valid_marker","product_dossier_valid_reviewer","product_dossier_writing","product_finish_product","product_fsc","product_gmp","product_lab_test","product_legal_receipt","product_legal_submission","product_license_manufacture","product_market_research","product_misc","product_nomenclature","product_physical_check","product_pp","product_procurement","product_qnq_formula","product_queries_received","product_queries_solution","product_queries_submitted","product_registration","product_shipment_booking","product_shipment_box_prep","product_shipment_dispatch","product_submission","product_submission_legal","product_submission_valid","product_translation","product_translation_received","product_vendor_doc_collection","product_vendor_finalization","product_vendor_sample_collection","product_work_standard");

        $counter = 0;
        foreach ($steps_arr as $step) {
            /*  Updating All Steps     */
            $update_step_sql = "UPDATE ".$step." 
                                SET del_by=".$user_id.", del_dt=NOW() 
                                WHERE product_id=".$product_id;

            if ($conn->query($update_step_sql) === TRUE) {
                $counter++;
            }
        }
        if(count($steps_arr) != $counter){
            $retval = "Some error occurred while deleting all steps. Please try again later.";
        }else{
            if (!$conn -> commit()) {
                $retval = "Some error occurred while deleting all steps. Please try again later.";
            }else{
                $retval = "1";
            }
        }

        return $retval;
    }

?>