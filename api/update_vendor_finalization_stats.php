<?php
	header('Access-Control-Allow-Origin: *');
	
    function processStats($user_id,$product_id,$conn){
        $VENDOR_FINALIZATION_MENU_ID = 10;
        $retval = "0";
    
        /*  Getting Progress    */
        $total_progress = 0;
        $total_entries = 0;

        $get_finalization_sql = "SELECT count(*) cnt FROM `product_vendor_finalization` 
                                WHERE `ent_by`=".$user_id." AND del_by IS NULL AND product_id=".$product_id;
        $result_finalization = mysqli_query($conn,$get_finalization_sql);
        while ($row_finalization = mysqli_fetch_array($result_finalization, MYSQLI_ASSOC)) {  
            $total_progress += (int)$row_finalization['cnt'] > 0 ? 1 : 0;
            $total_entries++;
        }

        $get_doc_collection_sql = "SELECT count(*) cnt FROM `product_vendor_doc_collection` 
                                WHERE `ent_by`=".$user_id." AND del_by IS NULL  AND product_id=".$product_id;
        $result_doc_collection = mysqli_query($conn,$get_doc_collection_sql);
        while ($row_doc_collection = mysqli_fetch_array($result_doc_collection, MYSQLI_ASSOC)) {  
            $total_progress += (int)$row_doc_collection['cnt'] > 0 ? 1 : 0;
            $total_entries++;
        }

        $get_sample_collection_sql = "SELECT count(*) cnt FROM `product_vendor_sample_collection` 
                                WHERE `ent_by`=".$user_id." AND del_by IS NULL  AND product_id=".$product_id;
        $result_sample_collection = mysqli_query($conn,$get_sample_collection_sql);
        while ($row_sample_collection = mysqli_fetch_array($result_sample_collection, MYSQLI_ASSOC)) {  
            $total_progress += (int)$row_sample_collection['cnt'] > 0 ? 1 : 0;
            $total_entries++;
        }

        // echo "progess=".$total_progress;
        // echo "entries=".$total_entries;
        $total_percent = (int)$total_progress / (int)$total_entries * 100;

        $progress = number_format($total_percent, 2);

        mysqli_free_result($result_finalization);
        mysqli_free_result($result_sample_collection);
        mysqli_free_result($result_doc_collection);
    
        /*  Updating Progress     */
        $update_progress_sql = "UPDATE product_step_master 
                                SET progress=".$progress.",
                                updt_by=".$user_id.",
                                updt_dt=NOW()  
                                WHERE product_id=".$product_id." 
                                AND user_id=".$user_id." 
                                AND menu_id=".$VENDOR_FINALIZATION_MENU_ID;

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