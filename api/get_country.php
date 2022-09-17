<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input   */
    $country_id = mysqli_real_escape_string($conn, $_REQUEST['country_id']);
    $user_id = mysqli_real_escape_string($conn, $_REQUEST['user_id']);
    $user_role = mysqli_real_escape_string($conn, $_REQUEST['user_role']);

    /*  Getting roles    */
    $get_country_sql = "SELECT * FROM country_master WHERE del_by IS NULL ";

    if($user_role == 4){
        $get_country_sql .= " AND country_id IN ( 
                            SELECT DISTINCT a.`country_id` 
                            from `product_master` a INNER join `product_step_master` b 
                            ON a.`product_id`=b.`product_id` 
                            WHERE b.`user_id`=".$user_id.") ";
    }

    if($country_id != 0){
        /*  Checking role already exists     */
        $role_exist = "SELECT country_id FROM country_master 
                        WHERE country_id = ".$country_id."
                        AND del_by IS NULL";
        $role_response = mysqli_query($conn, $role_exist);
        if (mysqli_num_rows($role_response) == 0){
            echo "Country details not found for country id ".$country_id." in the system.";
            exit();
        }

        $get_country_sql .= " AND country_id = ".$country_id;
    }

    $get_country_sql .= " ORDER BY country_id DESC ";

    $result = mysqli_query($conn,$get_country_sql);  
    $json_response = array();  
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {  
        $row_array['country_id'] = $row['country_id'];
        $row_array['country_name'] = $row['country_name'];
        $row_array['ent_by'] = $row['ent_by'];
        $row_array['ent_dt'] = $row['ent_dt'];        
        array_push($json_response,$row_array);  
    }

    echo json_encode($json_response); 
    mysqli_free_result($result);

    $conn->close();

?>