<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input   */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
    $product_id = mysqli_real_escape_string($conn, $_REQUEST['product_id']);
    $lt_id = mysqli_real_escape_string($conn, $_REQUEST['lt_id']);

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

    /*  Getting nomenclature    */
    $get_lab_test_sql = "SELECT * FROM product_lab_test WHERE product_id=".$product_id." AND ent_by='".$login_id."' 
                                AND del_by IS NULL ";

    if($lt_id != 0){
        $get_lab_test_sql .= " AND lt_id = ".$lt_id;
    }

    $get_lab_test_sql .= " ORDER BY lt_id DESC ";

    $result = mysqli_query($conn,$get_lab_test_sql);  
    $json_response = array();  
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {  
        $row_array['lt_id'] = $row['lt_id'];
        $row_array['product_id'] = $row['product_id'];
        $row_array['batch_no'] = $row['batch_no'];
        $row_array['lab_name'] = $row['lab_name'];
        $row_array['test_performed'] = $row['test_performed'];
        $row_array['submitted_dt'] = $row['submitted_dt'];
        $row_array['received_dt'] = $row['received_dt'];
        $row_array['result'] = $row['result'];
        $row_array['remarks'] = $row['remarks'];
        $row_array['ent_by'] = $row['ent_by'];
        $row_array['ent_dt'] = $row['ent_dt'];        
        array_push($json_response,$row_array);
    }

    echo json_encode($json_response); 
    mysqli_free_result($result);

    $conn->close();

?>