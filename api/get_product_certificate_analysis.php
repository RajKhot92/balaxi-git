<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input   */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
    $product_id = mysqli_real_escape_string($conn, $_REQUEST['product_id']);
    $ca_id = mysqli_real_escape_string($conn, $_REQUEST['ca_id']);

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
    $get_certificate_analysis_sql = "SELECT * FROM product_certificate_analysis WHERE product_id=".$product_id." AND ent_by='".$login_id."'";

    if($ca_id != 0){
        $get_certificate_analysis_sql .= " AND ca_id = ".$ca_id;
    }

    $get_certificate_analysis_sql .= " ORDER BY ca_id DESC ";

    $result = mysqli_query($conn,$get_certificate_analysis_sql);  
    $json_response = array();  
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {  
        $row_array['ca_id'] = $row['ca_id'];
        $row_array['product_id'] = $row['product_id'];
        $row_array['batch_details'] = $row['batch_details'];
        $row_array['validity'] = $row['validity'];
        $row_array['certificate'] = base64_encode($row['certificate']);
        $row_array['ent_by'] = $row['ent_by'];
        $row_array['ent_dt'] = $row['ent_dt'];        
        array_push($json_response,$row_array);
    }

    echo json_encode($json_response); 
    mysqli_free_result($result);

    $conn->close();

?>