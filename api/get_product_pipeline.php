<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    $country_id = mysqli_real_escape_string($conn, $_REQUEST['country_id']);

    /*  Getting Progress    */
    $wip = 0;
    $dispatch = 0;
    $submitted = 0;
    $registered = 0;

    $get_wip_sql = "SELECT count(*) cnt FROM `product_master` 
                    WHERE `product_id` IN (
                    SELECT DISTINCT `product_id` FROM `product_step_master`) ";
    
    if($country_id != ""){
        $get_wip_sql .= "AND country_id = ".$country_id;
    }

    $result_wip = mysqli_query($conn,$get_wip_sql);
    while ($row_wip = mysqli_fetch_array($result_wip, MYSQLI_ASSOC)) {  
        $wip = $row_wip['cnt'];
    }

    $get_dispatch_sql = "SELECT DISTINCT a.product_id,COUNT(*) from product_shipment_booking a 
                        INNER JOIN product_shipment_box_prep b ON a.product_id=b.product_id
                        INNER JOIN product_shipment_dispatch c ON a.product_id=c.product_id ";
    
    if($country_id != ""){
        $get_dispatch_sql .= " AND a.product_id IN (SELECT DISTINCT product_id FROM product_master WHERE country_id = ".$country_id.") ";
    }

    $get_dispatch_sql .= " GROUP by a.product_id";

    $result_dispatch = mysqli_query($conn,$get_dispatch_sql);
    $dispatch = mysqli_num_rows($result_dispatch);

    $get_submitted_sql = "SELECT product_id, count(*) FROM `product_submission` ";

    if($country_id != ""){
        $get_submitted_sql .= " WHERE product_id IN (SELECT DISTINCT product_id FROM product_master WHERE country_id = ".$country_id.") ";
    }

    $get_submitted_sql .= " GROUP by product_id";

    $result_submitted = mysqli_query($conn,$get_submitted_sql);
    $submitted = mysqli_num_rows($result_submitted);

    $get_registered_sql = "SELECT product_id, count(*) FROM `product_registration`";

    if($country_id != ""){
        $get_registered_sql .= " WHERE product_id IN (SELECT DISTINCT product_id FROM product_master WHERE country_id = ".$country_id.") ";
    }

    $get_registered_sql .= " GROUP by product_id";

    $result_registered = mysqli_query($conn,$get_registered_sql);
    $registered = mysqli_num_rows($result_registered);
    
    // echo "wip=".$wip;
    // echo "dispatch=".$dispatch;
    // echo "submitted=".$submitted;
    // echo "registered=".$registered;
    
    $submitted_new = $submitted - $registered;
    $dispatch_new = $dispatch - $submitted_new;
    $wip_new = $wip - $dispatch_new;

    $row_array['wip'] = $wip_new;
    $row_array['dispatch'] = $dispatch_new;
    $row_array['submitted'] = $submitted_new;
    $row_array['registered'] = $registered;

    $json_response = array();
    array_push($json_response,$row_array);

    echo json_encode($json_response); 
    mysqli_free_result($result_wip);
    mysqli_free_result($result_dispatch);
    mysqli_free_result($result_submitted);
    mysqli_free_result($result_registered);

    $conn->close();

?>