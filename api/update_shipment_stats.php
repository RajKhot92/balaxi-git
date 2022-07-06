<?php
	header('Access-Control-Allow-Origin: *');
	
    function processStats($user_id,$product_id,$conn){
        $SHIPMENT_MENU_ID = 14;
        $retval = "0";

        /*  Getting Progress    */
        $total_progress = 0;
        $total_entries = 0;

        $get_booking_sql = "SELECT count(*) cnt FROM `product_shipment_booking` 
                                WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
        $result_booking = mysqli_query($conn,$get_booking_sql);
        while ($row_booking = mysqli_fetch_array($result_booking, MYSQLI_ASSOC)) {  
            $total_progress += (int)$row_booking['cnt'] > 0 ? 1 : 0;
            $total_entries++;
        }

        $get_box_sql = "SELECT count(*) cnt FROM `product_shipment_box_prep` 
                                WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
        $result_box = mysqli_query($conn,$get_box_sql);
        while ($row_box = mysqli_fetch_array($result_box, MYSQLI_ASSOC)) {  
            $total_progress += (int)$row_box['cnt'] > 0 ? 1 : 0;
            $total_entries++;
        }

        $get_dipatch_sql = "SELECT count(*) cnt FROM `product_shipment_dispatch` 
                                WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
        $result_dispatch = mysqli_query($conn,$get_dipatch_sql);
        while ($row_dispatch = mysqli_fetch_array($result_dispatch, MYSQLI_ASSOC)) {  
            $total_progress += (int)$row_dispatch['cnt'] > 0 ? 1 : 0;
            $total_entries++;
        }

        // echo "progess=".$total_progress;
        // echo "entries=".$total_entries;
        $total_percent = (int)$total_progress / (int)$total_entries * 100;

        $progress = number_format($total_percent, 2);

        mysqli_free_result($result_booking);
        mysqli_free_result($result_box);
        mysqli_free_result($result_dispatch);

        /*  Updating Progress     */
        $update_progress_sql = "UPDATE product_step_master 
                                SET progress=".$progress.",
                                updt_by=".$user_id.",
                                updt_dt=NOW()  
                                WHERE product_id=".$product_id." 
                                AND user_id=".$user_id." 
                                AND menu_id=".$SHIPMENT_MENU_ID;

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