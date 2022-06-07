<?php
    header('Access-Control-Allow-Origin: *');
    
    function processStats($user_id,$product_id,$conn){
        $DOCUMENT_SAMPLE_COLLECTION_MENU_ID = 11;
        $retval = "0";
    
        /*  Getting Progress    */
        $total_progress_doc_collection = 0;
        $total_entries_doc_collection = 0;

        $get_license_manufacture_sql = "SELECT count(*) cnt FROM `product_license_manufacture` 
                                WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
        $result_license_manufacture = mysqli_query($conn,$get_license_manufacture_sql);
        while ($row_license_manufacture = mysqli_fetch_array($result_license_manufacture, MYSQLI_ASSOC)) {  
            $total_progress_doc_collection += (int)$row_license_manufacture['cnt'] > 0 ? 1 : 0;
            $total_entries_doc_collection++;
        }

        $get_pp_sql = "SELECT count(*) cnt FROM `product_pp` 
                                WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
        $result_pp = mysqli_query($conn,$get_pp_sql);
        while ($row_pp = mysqli_fetch_array($result_pp, MYSQLI_ASSOC)) {  
            $total_progress_doc_collection += (int)$row_pp['cnt'] > 0 ? 1 : 0;
            $total_entries_doc_collection++;
        }

        $get_fsc_sql = "SELECT count(*) cnt FROM `product_fsc` 
                                WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
        $result_fsc = mysqli_query($conn,$get_fsc_sql);
        while ($row_fsc = mysqli_fetch_array($result_fsc, MYSQLI_ASSOC)) {  
            $total_progress_doc_collection += (int)$row_fsc['cnt'] > 0 ? 1 : 0;
            $total_entries_doc_collection++;
        }

        $get_gmp_sql = "SELECT count(*) cnt FROM `product_gmp` 
                                WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
        $result_gmp = mysqli_query($conn,$get_gmp_sql);
        while ($row_gmp = mysqli_fetch_array($result_gmp, MYSQLI_ASSOC)) {  
            $total_progress_doc_collection += (int)$row_gmp['cnt'] > 0 ? 1 : 0;
            $total_entries_doc_collection++;
        }

        $get_copp_sql = "SELECT count(*) cnt FROM `product_copp` 
                                WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
        $result_copp = mysqli_query($conn,$get_copp_sql);
        while ($row_copp = mysqli_fetch_array($result_copp, MYSQLI_ASSOC)) {  
            $total_progress_doc_collection += (int)$row_copp['cnt'] > 0 ? 1 : 0;
            $total_entries_doc_collection++;
        }

        $get_cma_sql = "SELECT count(*) cnt FROM `product_cma` 
                                WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
        $result_cma = mysqli_query($conn,$get_cma_sql);
        while ($row_cma = mysqli_fetch_array($result_cma, MYSQLI_ASSOC)) {  
            $total_progress_doc_collection += (int)$row_cma['cnt'] > 0 ? 1 : 0;
            $total_entries_doc_collection++;
        }

        // echo "progess=".$total_progress_doc_collection;
        // echo "entries=".$total_entries_doc_collection;
        $total_percent_doc_collection = (int)$total_progress_doc_collection / (int)$total_entries_doc_collection * 100;
        
        $total_progress_doc_validation = 0;
        $total_entries_doc_validation = 0;

        $get_certificate_analysis_sql = "SELECT count(*) cnt FROM `product_certificate_analysis` 
                                WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
        $result_certificate_analysis = mysqli_query($conn,$get_certificate_analysis_sql);
        while ($row_certificate_analysis = mysqli_fetch_array($result_certificate_analysis, MYSQLI_ASSOC)) {  
            $total_progress_doc_validation += (int)$row_certificate_analysis['cnt'] > 0 ? 1 : 0;
            $total_entries_doc_validation++;
        }

        $get_qnq_formula_sql = "SELECT count(*) cnt FROM `product_qnq_formula` 
                                WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
        $result_qnq_formula = mysqli_query($conn,$get_qnq_formula_sql);
        while ($row_qnq_formula = mysqli_fetch_array($result_qnq_formula, MYSQLI_ASSOC)) {  
            $total_progress_doc_validation += (int)$row_qnq_formula['cnt'] > 0 ? 1 : 0;
            $total_entries_doc_validation++;
        }

        $get_misc_sql = "SELECT count(*) cnt FROM `product_misc` 
                                WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
        $result_misc = mysqli_query($conn,$get_misc_sql);
        while ($row_misc = mysqli_fetch_array($result_misc, MYSQLI_ASSOC)) {  
            $total_progress_doc_validation += (int)$row_misc['cnt'] > 0 ? 1 : 0;
            $total_entries_doc_validation++;
        }

        // echo "progess=".$total_progress_doc_validation;
        // echo "entries=".$total_entries_doc_validation;
        $total_percent_doc_validation = (int)$total_progress_doc_validation / (int)$total_entries_doc_validation * 100;

        $total_progress_legalization = 0;
        $total_entries_legalization = 0;

        $get_submission_sql = "SELECT count(*) cnt FROM `product_legal_submission` 
                                WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
        $result_submission = mysqli_query($conn,$get_submission_sql);
        while ($row_submission = mysqli_fetch_array($result_submission, MYSQLI_ASSOC)) {  
            $total_progress_legalization += (int)$row_submission['cnt'] > 0 ? 1 : 0;
            $total_entries_legalization++;
        }

        $get_receipt_sql = "SELECT count(*) cnt FROM `product_legal_receipt` 
                                WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
        $result_receipt = mysqli_query($conn,$get_receipt_sql);
        while ($row_receipt = mysqli_fetch_array($result_receipt, MYSQLI_ASSOC)) {  
            $total_progress_legalization += (int)$row_receipt['cnt'] > 0 ? 1 : 0;
            $total_entries_legalization++;
        }

        // echo "progess=".$total_progress_legalization;
        // echo "entries=".$total_entries_legalization;
        $total_percent_legalization = (int)$total_progress_legalization / (int)$total_entries_legalization * 100;
        
        $total_progress_sample_collection = 0;
        $total_entries_sample_collection = 0;

        $get_work_standard_sql = "SELECT count(*) cnt FROM `product_work_standard` 
                                WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
        $result_work_standard = mysqli_query($conn,$get_work_standard_sql);
        while ($row_work_standard = mysqli_fetch_array($result_work_standard, MYSQLI_ASSOC)) {  
            $total_progress_sample_collection += (int)$row_work_standard['cnt'] > 0 ? 1 : 0;
            $total_entries_sample_collection++;
        }

        $get_finish_product_sql = "SELECT count(*) cnt FROM `product_finish_product` 
                                WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
        $result_finish_product = mysqli_query($conn,$get_finish_product_sql);
        while ($row_finish_product = mysqli_fetch_array($result_finish_product, MYSQLI_ASSOC)) {  
            $total_progress_sample_collection += (int)$row_finish_product['cnt'] > 0 ? 1 : 0;
            $total_entries_sample_collection++;
        }

        // echo "progess=".$total_progress_sample_collection;
        // echo "entries=".$total_entries_sample_collection;
        $total_percent_sample_collection = (int)$total_progress_sample_collection / (int)$total_entries_sample_collection * 100;
        
        $total_progress_sample_quality = 0;
        $total_entries_sample_quality = 0;

        $get_physical_check_sql = "SELECT count(*) cnt FROM `product_physical_check` 
                                WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
        $result_physical_check = mysqli_query($conn,$get_physical_check_sql);
        while ($row_physical_check = mysqli_fetch_array($result_physical_check, MYSQLI_ASSOC)) {  
            $total_progress_sample_quality += (int)$row_physical_check['cnt'] > 0 ? 1 : 0;
            $total_entries_sample_quality++;
        }

        $get_lab_test_sql = "SELECT count(*) cnt FROM `product_lab_test` 
                                WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
        $result_lab_test = mysqli_query($conn,$get_lab_test_sql);
        while ($row_lab_test = mysqli_fetch_array($result_lab_test, MYSQLI_ASSOC)) {  
            $total_progress_sample_quality += (int)$row_lab_test['cnt'] > 0 ? 1 : 0;
            $total_entries_sample_quality++;
        }

        // echo "progess=".$total_progress_sample_quality;
        // echo "entries=".$total_entries_legalization;
        $total_percent_sample_quality = (int)$total_progress_sample_quality / (int)$total_entries_legalization * 100;
        
        $total_progress_doc_sample_collection = (int)$total_percent_doc_collection + (int)$total_percent_doc_validation + (int)$total_percent_legalization + (int)$total_percent_sample_collection + (int)$total_percent_sample_quality;
        $total_percent_doc_sample_collection = (int)$total_progress_doc_sample_collection / 5;
        
        $progress = number_format($total_percent_doc_sample_collection, 2);    

        mysqli_free_result($result_license_manufacture);
        mysqli_free_result($result_pp);
        mysqli_free_result($result_fsc);
        mysqli_free_result($result_gmp);
        mysqli_free_result($result_copp);
        mysqli_free_result($result_cma);

        mysqli_free_result($result_certificate_analysis);
        mysqli_free_result($result_qnq_formula);
        mysqli_free_result($result_misc);

        mysqli_free_result($result_submission);
        mysqli_free_result($result_receipt);

        mysqli_free_result($result_work_standard);
        mysqli_free_result($result_finish_product);

        mysqli_free_result($result_physical_check);
        mysqli_free_result($result_lab_test);

        /*  Updating Progress     */
        $update_progress_sql = "UPDATE product_step_master 
                                SET progress=".$progress." 
                                WHERE product_id=".$product_id." 
                                AND user_id=".$user_id." 
                                AND menu_id=".$DOCUMENT_SAMPLE_COLLECTION_MENU_ID;

        if ($conn->query($update_progress_sql) !== TRUE) {
            $retval = "Some error occurred while updating progress details. Please try again later.";
        }
        if (!$conn -> commit()) {
            $retval = "Some error occurred while updating progress details. Please try again later.";
        }else{
            $retval = "1";
        }

        return $retval;
    }
?>