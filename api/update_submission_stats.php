<?php
	header('Access-Control-Allow-Origin: *');

    function processStats($user_id,$product_id,$conn){
        $SUBMISSION_MENU_ID = 15;
        $retval = "0";
    
        /*  Getting Progress    */
        $total_progress_translation = 0;
        $total_entries_translation = 0;

        $get_translation_sql = "SELECT count(*) cnt FROM `product_translation` 
                                WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
        $result_translation = mysqli_query($conn,$get_translation_sql);
        while ($row_translation = mysqli_fetch_array($result_translation, MYSQLI_ASSOC)) {  
            $total_progress_translation += (int)$row_translation['cnt'] > 0 ? 1 : 0;
            $total_entries_translation++;
        }

        // echo "progess=".$total_progress_translation;
        // echo "entries=".$total_entries_translation;
        $total_percent_translation = (int)$total_progress_translation / (int)$total_entries_translation * 100;

        
        $total_progress_submission = 0;
        $total_entries_submission = 0;

        $get_validation_sql = "SELECT count(*) cnt FROM `product_submission_valid` 
                                WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
        $result_validation = mysqli_query($conn,$get_validation_sql);
        while ($row_validation = mysqli_fetch_array($result_validation, MYSQLI_ASSOC)) {  
            $total_progress_submission += (int)$row_validation['cnt'] > 0 ? 1 : 0;
            $total_entries_submission++;
        }

        $get_legalization_sql = "SELECT count(*) cnt FROM `product_submission_legal` 
                                WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
        $result_legalization = mysqli_query($conn,$get_legalization_sql);
        while ($row_legalization = mysqli_fetch_array($result_legalization, MYSQLI_ASSOC)) {  
            $total_progress_submission += (int)$row_legalization['cnt'] > 0 ? 1 : 0;
            $total_entries_submission++;
        }

        $get_submission_sql = "SELECT count(*) cnt FROM `product_submission` 
                                WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
        $result_submission = mysqli_query($conn,$get_submission_sql);
        while ($row_submission = mysqli_fetch_array($result_submission, MYSQLI_ASSOC)) {  
            $total_progress_submission += (int)$row_submission['cnt'] > 0 ? 1 : 0;
            $total_entries_submission++;
        }

        $get_registration_sql = "SELECT count(*) cnt FROM `product_submission` 
                                WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
        $result_registration = mysqli_query($conn,$get_registration_sql);
        while ($row_registration = mysqli_fetch_array($result_registration, MYSQLI_ASSOC)) {  
            $total_progress_submission += (int)$row_registration['cnt'] > 0 ? 1 : 0;
            $total_entries_submission++;
        }

        // echo "progess=".$total_progress_submission;
        // echo "entries=".$total_entries_submission;
        $total_percent_submission = (int)$total_progress_submission / (int)$total_entries_submission * 100;
        
        $total_progress_submission_menu = (int)$total_percent_translation + (int)$total_percent_submission;

        $total_percent_submission_menu = $total_progress_submission_menu / 2;
        
        $progress = number_format($total_percent_submission_menu, 2);
        
        mysqli_free_result($result_translation);
        mysqli_free_result($result_validation);
        mysqli_free_result($result_legalization);
        mysqli_free_result($result_submission);
        mysqli_free_result($result_registration);
        
        /*  Updating Progress     */
        $update_progress_sql = "UPDATE product_step_master 
                                SET progress=".$progress.",
                                updt_by=".$user_id.",
                                updt_dt=NOW()  
                                WHERE product_id=".$product_id." 
                                AND user_id=".$user_id." 
                                AND menu_id=".$SUBMISSION_MENU_ID;

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