<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input   */
    $therapeutic_id = mysqli_real_escape_string($conn, $_REQUEST['therapeutic_id']);

    /*  Getting categories    */
    $get_category_sql = "SELECT * FROM therapeutic_class_master WHERE del_by IS NULL";

    if($therapeutic_id != 0){
        /*  Checking role already exists     */
        $role_exist = "SELECT therapeutic_id FROM therapeutic_class_master 
                        WHERE therapeutic_id = ".$therapeutic_id."
                        AND del_by IS NULL";
        $role_response = mysqli_query($conn, $role_exist);
        if (mysqli_num_rows($role_response) == 0){
            echo "Theapeutic class details not found for therapeutic id ".$therapeutic_id." in the system.";
            exit();
        }

        $get_category_sql .= " AND therapeutic_id = ".$therapeutic_id;
    }

    $get_category_sql .= " ORDER BY therapeutic_id DESC ";

    $result = mysqli_query($conn,$get_category_sql);  
    $json_response = array();  
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {  
        $row_array['therapeutic_id'] = $row['therapeutic_id'];
        $row_array['therapeutic_class'] = $row['therapeutic_class'];
        $row_array['ent_by'] = $row['ent_by'];
        $row_array['ent_dt'] = $row['ent_dt'];        
        array_push($json_response,$row_array);  
    }

    echo json_encode($json_response); 
    mysqli_free_result($result);

    $conn->close();

?>