<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input   */
    $vendor_id = mysqli_real_escape_string($conn, $_REQUEST['vendor_id']);

    /*  Getting roles    */
    $get_vendor_sql = "SELECT * FROM vendor_master WHERE del_by IS NULL";

    if($vendor_id != 0){
        /*  Checking role already exists     */
        $role_exist = "SELECT vendor_id FROM vendor_master 
                        WHERE vendor_id = ".$vendor_id."
                        AND del_by IS NULL";
        $role_response = mysqli_query($conn, $role_exist);
        if (mysqli_num_rows($role_response) == 0){
            echo "Country details not found for vendor id ".$vendor_id." in the system.";
            exit();
        }

        $get_vendor_sql .= " AND vendor_id = ".$vendor_id;
    }

    $result = mysqli_query($conn,$get_vendor_sql);  
    $json_response = array();  
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {  
        $row_array['vendor_id'] = $row['vendor_id'];
        $row_array['vendor_name'] = $row['vendor_name'];
        $row_array['ent_by'] = $row['ent_by'];
        $row_array['ent_dt'] = $row['ent_dt'];        
        array_push($json_response,$row_array);  
    }

    echo json_encode($json_response); 
    mysqli_free_result($result);

    $conn->close();

?>