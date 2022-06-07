<?php
	header('Access-Control-Allow-Origin: *');

    function processStats($user_id,$product_id,$conn){
        $DOSSIER_DEVELOPMENT_MENU_ID = 13;
        $retval = "0";
    
        /*  Getting Progress    */
        $total_progress_pre_requisite = 0;
        $total_entries_pre_requisite = 0;

        $get_dossier_pre_requisite_sql = "SELECT count(*) cnt FROM `product_dossier_pre_requisite` 
                                WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
        $result_dossier_pre_requisite = mysqli_query($conn,$get_dossier_pre_requisite_sql);
        while ($row_dossier_pre_requisite = mysqli_fetch_array($result_dossier_pre_requisite, MYSQLI_ASSOC)) {  
            $total_progress_pre_requisite += (int)$row_dossier_pre_requisite['cnt'] > 0 ? 1 : 0;
            $total_entries_pre_requisite++;
        }

        $get_dossier_check_sql = "SELECT count(*) cnt FROM `product_dossier_check` 
                                WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
        $result_dossier_check = mysqli_query($conn,$get_dossier_check_sql);
        while ($row_dossier_check = mysqli_fetch_array($result_dossier_check, MYSQLI_ASSOC)) {  
            $total_progress_pre_requisite += (int)$row_dossier_check['cnt'] > 0 ? 1 : 0;
            $total_entries_pre_requisite++;
        }

        $total_progress_population = 0;
        $total_entries_population = 0;

        $get_dossier_writing_sql = "SELECT count(*) cnt FROM `product_dossier_writing` 
                                WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
        $result_dossier_writing = mysqli_query($conn,$get_dossier_writing_sql);
        while ($row_dossier_writing = mysqli_fetch_array($result_dossier_writing, MYSQLI_ASSOC)) {  
            $total_progress_population += (int)$row_dossier_writing['cnt'] > 0 ? 1 : 0;
            $total_entries_population++;
        }

        $get_dossier_valid_marker_sql = "SELECT count(*) cnt FROM `product_dossier_valid_marker` 
                                WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
        $result_dossier_valid_marker = mysqli_query($conn,$get_dossier_valid_marker_sql);
        while ($row_dossier_valid_marker = mysqli_fetch_array($result_dossier_valid_marker, MYSQLI_ASSOC)) {  
            $total_progress_population += (int)$row_dossier_valid_marker['cnt'] > 0 ? 1 : 0;
            $total_entries_population++;
        }

        // echo "progess=".$total_progress_pre_requisite;
        // echo "entries=".$total_entries_pre_requisite;
        $total_percent_pre_requisite = (int)$total_progress_pre_requisite / (int)$total_entries_pre_requisite * 100;
        
        // echo "progess=".$total_progress_population;
        // echo "entries=".$total_entries_population;
        $total_percent_population = (int)$total_progress_population / (int)$total_entries_population * 100;
        
        $total_progress_dossier = (int)$total_percent_pre_requisite + (int)$total_percent_population;
        $total_percent_dossier = (int)$total_progress_dossier / 2;

        $progress = number_format($total_percent_dossier, 2);

        mysqli_free_result($result_dossier_pre_requisite);
        mysqli_free_result($result_dossier_check);
        mysqli_free_result($result_dossier_writing);
        mysqli_free_result($result_dossier_valid_marker);

        /*  Updating Progress     */
        $update_progress_sql = "UPDATE product_step_master 
                                SET progress=".$progress." 
                                WHERE product_id=".$product_id." 
                                AND user_id=".$user_id." 
                                AND menu_id=".$DOSSIER_DEVELOPMENT_MENU_ID;

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