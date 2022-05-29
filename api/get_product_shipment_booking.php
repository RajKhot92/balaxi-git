<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input   */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
    $product_id = mysqli_real_escape_string($conn, $_REQUEST['product_id']);
    $psb_id = mysqli_real_escape_string($conn, $_REQUEST['psb_id']);

    $login_exist = "SELECT user_id FROM user_master 
                    WHERE user_id = ".$login_id."
                    AND del_by IS NULL";
    $login_response = mysqli_query($conn, $login_exist);
    if (mysqli_num_rows($login_response) == 0){
        echo "User details not found for user id ".$login_id.".";
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

    /*  Getting shipment booking    */
    $get_shipment_booking_sql = "SELECT * FROM product_shipment_booking WHERE product_id=".$product_id." AND ent_by='".$login_id."'";

    if($psb_id != 0){
        $get_shipment_booking_sql .= " AND psb_id = ".$psb_id;
    }

    $result = mysqli_query($conn,$get_shipment_booking_sql);  
    $json_response = array();  
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {  
        $row_array['psb_id'] = $row['psb_id'];
        $row_array['product_id'] = $row['product_id'];
        $row_array['dhl_approval'] = $row['dhl_approval'];
        $row_array['slot_booking'] = $row['slot_booking'];
        $row_array['pick_up_date'] = $row['pick_up_date'];
        $row_array['book_invoice'] = base64_encode($row['book_invoice']);
        $row_array['ent_by'] = $row['ent_by'];
        $row_array['ent_dt'] = $row['ent_dt'];        
        array_push($json_response,$row_array);
    }

    echo json_encode($json_response); 
    mysqli_free_result($result);

    $conn->close();

?>