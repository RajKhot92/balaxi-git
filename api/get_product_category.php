<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input   */
    $category_id = mysqli_real_escape_string($conn, $_REQUEST['category_id']);

    /*  Getting categories    */
    $get_category_sql = "SELECT * FROM product_category WHERE del_by IS NULL";

    if($category_id != 0){
        /*  Checking role already exists     */
        $role_exist = "SELECT category_id FROM product_category 
                        WHERE category_id = ".$category_id."
                        AND del_by IS NULL";
        $role_response = mysqli_query($conn, $role_exist);
        if (mysqli_num_rows($role_response) == 0){
            echo "Product category details not found for category id ".$category_id." in the system.";
            exit();
        }

        $get_category_sql .= " AND category_id = ".$category_id;
    }

    $result = mysqli_query($conn,$get_category_sql);  
    $json_response = array();  
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {  
        $row_array['category_id'] = $row['category_id'];
        $row_array['category_name'] = $row['category_name'];
        $row_array['ent_by'] = $row['ent_by'];
        $row_array['ent_dt'] = $row['ent_dt'];        
        array_push($json_response,$row_array);  
    }

    echo json_encode($json_response); 
    mysqli_free_result($result);

    $conn->close();

?>