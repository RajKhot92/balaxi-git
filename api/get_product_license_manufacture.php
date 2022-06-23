<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input   */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
    $product_id = mysqli_real_escape_string($conn, $_REQUEST['product_id']);
    $lm_id = mysqli_real_escape_string($conn, $_REQUEST['lm_id']);

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
    $get_license_manufacture_sql = "SELECT * FROM product_license_manufacture WHERE product_id=".$product_id." AND ent_by='".$login_id."'";

    if($lm_id != 0){
        $get_license_manufacture_sql .= " AND lm_id = ".$lm_id;
    }

    $get_license_manufacture_sql .= " ORDER BY lm_id DESC ";

    $result = mysqli_query($conn,$get_license_manufacture_sql);  
    $json_response = array();  
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {  
        $row_array['lm_id'] = $row['lm_id'];
        $row_array['product_id'] = $row['product_id'];
        $row_array['manufacture_name'] = $row['manufacture_name'];
        $row_array['validity'] = $row['validity'] == null ? "" : $row['validity'];
        $row_array['received_date'] = $row['received_date'] == null ? "" : $row['received_date'];
        $row_array['license'] = $row['license'] == null ? "" : "license";
        $row_array['ent_by'] = $row['ent_by'];
        $row_array['ent_dt'] = $row['ent_dt'];        
        array_push($json_response,$row_array);
    }

    echo json_encode($json_response); 
    mysqli_free_result($result);

    $conn->close();

?>