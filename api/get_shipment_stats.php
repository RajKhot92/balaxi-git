<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input   */
    $user_id = mysqli_real_escape_string($conn, $_REQUEST['user_id']);
    $menu_id = mysqli_real_escape_string($conn, $_REQUEST['menu_id']);
    $product_id = mysqli_real_escape_string($conn, $_REQUEST['product_id']);

    if($user_id === "" || $menu_id === "" || $product_id === ""){
        echo "Kindly provide valid input.";
        exit();
    }

    $user_exist = "SELECT user_id FROM user_master 
                WHERE user_id = ".$user_id."
                AND del_by IS NULL";
    $user_response = mysqli_query($conn, $user_exist);
    if (mysqli_num_rows($user_response) == 0){
        echo "User details not found for user id ".$user_id." in the system.";
        exit();
    }

    $menu_exist = "SELECT menu_id FROM menu_master 
                WHERE menu_id = ".$menu_id;
    $menu_response = mysqli_query($conn, $menu_exist);
    if (mysqli_num_rows($menu_response) == 0){
        echo "Menu details not found for menu id ".$menu_id." in the system.";
        exit();
    }

    $product_exist = "SELECT product_id FROM product_master 
                WHERE product_id = ".$product_id."
                AND del_by IS NULL";
    $product_response = mysqli_query($conn, $product_exist);
    if (mysqli_num_rows($product_response) == 0){
        echo "Product details not found for product id ".$product_id." in the system.";
        exit();
    }

    /*  Getting Progress    */
    $total_progress = 0;
    $total_entries = 0;

    $get_booking_sql = "SELECT count(*) cnt FROM `product_shipment_booking` 
                            WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
    $result_booking = mysqli_query($conn,$get_booking_sql);
    while ($row_booking = mysqli_fetch_array($result_booking, MYSQLI_ASSOC)) {  
        $row_array['boxing'] = $row_booking['cnt'] > 0 ? 100 : 0;
        $total_progress += (int)$row_booking['cnt'] > 0 ? 1 : 0;
        $total_entries++;
    }

    $get_box_sql = "SELECT count(*) cnt FROM `product_shipment_box_prep` 
                            WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
    $result_box = mysqli_query($conn,$get_box_sql);
    while ($row_box = mysqli_fetch_array($result_box, MYSQLI_ASSOC)) {  
        $row_array['box_prep'] = $row_box['cnt'] > 0 ? 100 : 0;
        $total_progress += (int)$row_box['cnt'] > 0 ? 1 : 0;
        $total_entries++;
    }

    $get_dipatch_sql = "SELECT count(*) cnt FROM `product_shipment_dispatch` 
                            WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
    $result_dispatch = mysqli_query($conn,$get_dipatch_sql);
    while ($row_dispatch = mysqli_fetch_array($result_dispatch, MYSQLI_ASSOC)) {  
        $row_array['dispatch'] = $row_dispatch['cnt'] > 0 ? 100 : 0;
        $total_progress += (int)$row_dispatch['cnt'] > 0 ? 1 : 0;
        $total_entries++;
    }

    // echo "progess=".$total_progress;
    // echo "entries=".$total_entries;
    $total_percent = (int)$total_progress / (int)$total_entries * 100;
    $row_array['shipment'] = $total_percent;

    $json_response = array();
    array_push($json_response,$row_array);

    echo json_encode($json_response); 
    mysqli_free_result($result_booking);
    mysqli_free_result($result_box);
    mysqli_free_result($result_dispatch);

    $conn->close();

?>