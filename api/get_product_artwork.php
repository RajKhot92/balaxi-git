<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input   */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
    $product_id = mysqli_real_escape_string($conn, $_REQUEST['product_id']);
    $art_id = mysqli_real_escape_string($conn, $_REQUEST['art_id']);

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

    /*  Getting artwork question    */
    $get_artwork_sql = "SELECT a.`art_id`,a.`product_id`,a.`product_code`,a.`vendor_id`,
                        b.`vendor_name`,a.`package_details`,a.`revision_no`,a.`revision_dt`,
                        a.`artwork_file`,a.`commercial_file`,a.`ent_by`,a.`ent_dt` 
                        FROM product_artwork a INNER JOIN vendor_master b 
                        ON a.`vendor_id`=b.`vendor_id` 
                        WHERE a.`product_id`=".$product_id." AND a.`ent_by`='".$login_id."' 
                                AND a.del_by IS NULL ";

    if($art_id != 0){
        $get_artwork_sql .= " AND art_id = ".$art_id;
    }

    $get_artwork_sql .= " ORDER BY art_id DESC ";

    $result = mysqli_query($conn,$get_artwork_sql);  
    $json_response = array();  
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {  
        $row_array['art_id'] = $row['art_id'];
        $row_array['product_id'] = $row['product_id'];
        $row_array['product_code'] = $row['product_code'];
        $row_array['vendor_id'] = $row['vendor_id'];
        $row_array['vendor_name'] = $row['vendor_name'];
        $row_array['package_details'] = $row['package_details'];
        $row_array['revision_no'] = $row['revision_no'];
        $row_array['revision_dt'] = $row['revision_dt'];
        $row_array['ent_by'] = $row['ent_by'];
        $row_array['ent_dt'] = $row['ent_dt'];        
        array_push($json_response,$row_array);
    }

    echo json_encode($json_response); 
    mysqli_free_result($result);

    $conn->close();

?>