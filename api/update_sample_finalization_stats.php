<?php
	header('Access-Control-Allow-Origin: *');

    function processStats($user_id,$product_id,$conn){
        $SAMPLE_FINALIZATION_MENU_ID = 12;
        $retval = "0";

        /*  Getting Progress    */
        $total_progress_artwork = 0;
        $total_entries_artwork = 0;

        $get_artwork_question_sql = "SELECT count(*) cnt FROM `product_artwork_question` 
                                WHERE `ent_by`=".$user_id." AND del_by IS NULL  AND product_id=".$product_id;
        $result_artwork_question = mysqli_query($conn,$get_artwork_question_sql);
        while ($row_artwork_question = mysqli_fetch_array($result_artwork_question, MYSQLI_ASSOC)) {  
            $total_progress_artwork += (int)$row_artwork_question['cnt'] > 0 ? 1 : 0;
            $total_entries_artwork++;
        }

        $get_artwork_sql = "SELECT count(*) cnt FROM `product_artwork` 
                                WHERE `ent_by`=".$user_id." AND del_by IS NULL  AND product_id=".$product_id;
        $result_artwork = mysqli_query($conn,$get_artwork_sql);
        while ($row_artwork = mysqli_fetch_array($result_artwork, MYSQLI_ASSOC)) {  
            $total_progress_artwork += (int)$row_artwork['cnt'] > 0 ? 1 : 0;
            $total_entries_artwork++;
        }

        $get_commercial_doc_sql = "SELECT count(*) cnt FROM `product_commercial_doc` 
                                WHERE `ent_by`=".$user_id." AND del_by IS NULL  AND product_id=".$product_id;
        $result_commercial_doc = mysqli_query($conn,$get_commercial_doc_sql);
        while ($row_commercial_doc = mysqli_fetch_array($result_commercial_doc, MYSQLI_ASSOC)) {  
            $total_progress_artwork += (int)$row_commercial_doc['cnt'] > 0 ? 1 : 0;
            $total_entries_artwork++;
        }

        $total_progress_finalize = 0;
        $total_entries_finalize = 0;

        $get_artwork_finalize_sql = "SELECT count(*) cnt FROM `product_artwork_finalize` 
                                WHERE `ent_by`=".$user_id." AND del_by IS NULL  AND product_id=".$product_id;
        $result_artwork_finalize = mysqli_query($conn,$get_artwork_finalize_sql);
        while ($row_artwork_finalize = mysqli_fetch_array($result_artwork_finalize, MYSQLI_ASSOC)) {  
            $total_progress_finalize += (int)$row_artwork_finalize['cnt'] > 0 ? 1 : 0;
            $total_entries_finalize++;
        }

        // echo "progess=".$total_progress_artwork;
        // echo "entries=".$total_entries_artwork;
        $total_percent_artwork = (int)$total_progress_artwork / (int)$total_entries_artwork * 100;
        
        // echo "progess=".$total_progress_finalize;
        // echo "entries=".$total_entries_finalize;
        $total_percent_finalize = (int)$total_progress_finalize / (int)$total_entries_finalize * 100;
        
        $total_progress_sample_finalize = (int)$total_percent_artwork + (int)$total_percent_finalize;
        $total_percent_sample_finalize = (int)$total_progress_sample_finalize / 2;

        $progress = number_format($total_percent_sample_finalize, 2);

        mysqli_free_result($result_artwork_question);
        mysqli_free_result($result_artwork);
        mysqli_free_result($result_commercial_doc);
        mysqli_free_result($result_artwork_finalize);

        /*  Updating Progress     */
        $update_progress_sql = "UPDATE product_step_master 
                                SET progress=".$progress.",
                                updt_by=".$user_id.",
                                updt_dt=NOW()  
                                WHERE product_id=".$product_id." 
                                AND user_id=".$user_id." 
                                AND menu_id=".$SAMPLE_FINALIZATION_MENU_ID;

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