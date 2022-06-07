<?php
	header('Access-Control-Allow-Origin: *');
	
    function processStats($user_id,$product_id,$conn){
        $DATA_CAPTURE_MENU_ID = 9;
        $retval = "0";
        
        /*  Getting Progress    */
        $total_progress = 0;
        $total_entries = 0;

        $get_nomenclature_sql = "SELECT count(*) cnt FROM `product_nomenclature` 
                                WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
        $result_nomenclature = mysqli_query($conn,$get_nomenclature_sql);
        while ($row_nomenclature = mysqli_fetch_array($result_nomenclature, MYSQLI_ASSOC)) {  
            $total_progress += (int)$row_nomenclature['cnt'] > 0 ? 1 : 0;
            $total_entries++;
        }

        $get_details_sql = "SELECT count(*) cnt FROM `product_details` 
                            WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
        $result_details = mysqli_query($conn,$get_details_sql);
        while ($row_details = mysqli_fetch_array($result_details, MYSQLI_ASSOC)) {  
            $total_progress += (int)$row_details['cnt'] > 0 ? 1 : 0;
            $total_entries++;
        }

        $get_market_research_sql = "SELECT count(*) cnt FROM `product_market_research` 
                                WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
        $result_market_research = mysqli_query($conn,$get_market_research_sql);
        while ($row_market_research = mysqli_fetch_array($result_market_research, MYSQLI_ASSOC)) {  
            $total_progress += (int)$row_market_research['cnt'] > 0 ? 1 : 0;
            $total_entries++;
        }

        $get_procurement_sql = "SELECT count(*) cnt FROM `product_procurement` 
                                WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
        $result_procurement = mysqli_query($conn,$get_procurement_sql);
        while ($row_procurement = mysqli_fetch_array($result_procurement, MYSQLI_ASSOC)) {  
            $total_progress += (int)$row_procurement['cnt'] > 0 ? 1 : 0;
            $total_entries++;
        }
        // echo "progess=".$total_progress;
        // echo "entries=".$total_entries;
        $total_percent = (int)$total_progress / (int)$total_entries * 100;
        
        $progress = number_format($total_percent, 2);

        mysqli_free_result($result_nomenclature);
        mysqli_free_result($result_details);
        mysqli_free_result($result_market_research);
        mysqli_free_result($result_procurement);

        /*  Updating Progress     */
        $update_progress_sql = "UPDATE product_step_master 
                                SET progress=".$progress." 
                                WHERE product_id=".$product_id." 
                                AND user_id=".$user_id." 
                                AND menu_id=".$DATA_CAPTURE_MENU_ID;

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