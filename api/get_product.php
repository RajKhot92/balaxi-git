<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input   */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
    $product_id = mysqli_real_escape_string($conn, $_REQUEST['product_id']);
    $product_owner = mysqli_real_escape_string($conn, $_REQUEST['product_owner']);
    $product_assigned_to = mysqli_real_escape_string($conn, $_REQUEST['product_assigned_to']);
    $assigned_menu_id = mysqli_real_escape_string($conn, $_REQUEST['assigned_menu_id']);
    
    /*  Getting user    */
    $get_product_sql = "SELECT a.`product_id`,a.`product_name`,a.`product_category`,
                    a.`product_owner`,a.`country_id`,a.`ent_by`,a.`ent_dt`,
                    b.`category_name`,c.`user_name`,d.`country_name` 
                    FROM product_master a 
                    INNER JOIN product_category b ON a.`product_category`=b.`category_id` 
                    INNER JOIN user_master c ON a.`product_owner`=c.`user_id`
                    INNER JOIN country_master d ON a.`country_id`=d.`country_id`
                    WHERE a.`del_by` IS NULL";

    if($login_id != 0){
        /*  Checking product already exists     */
        $login_exist = "SELECT user_id FROM user_master 
                        WHERE user_id = ".$login_id."
                        AND del_by IS NULL";
        $login_response = mysqli_query($conn, $login_exist);
        if (mysqli_num_rows($login_response) == 0){
            echo "User details not found for login id ".$login_id." in the system.";
            exit();
        }

        $get_product_sql .= " AND a.`ent_by` = ".$login_id;
    }

    if($product_id != 0){
        /*  Checking product already exists     */
        $product_exist = "SELECT product_id FROM product_master 
                        WHERE product_id = ".$product_id."
                        AND del_by IS NULL";
        $product_response = mysqli_query($conn, $product_exist);
        if (mysqli_num_rows($product_response) == 0){
            echo "Product details not found for product id ".$product_id." in the system.";
            exit();
        }

        $get_product_sql .= " AND product_id = ".$product_id;
    }

    if($product_owner != 0){
        /*  Checking product already exists     */
        $owner_exist = "SELECT user_id FROM user_master 
                        WHERE user_id = ".$product_owner."
                        AND del_by IS NULL";
        $owner_response = mysqli_query($conn, $owner_exist);
        if (mysqli_num_rows($owner_response) == 0){
            echo "User details not found for product owner id ".$product_owner." in the system.";
            exit();
        }

        $get_product_sql .= " AND product_owner = ".$product_owner;
    }

    if($product_assigned_to != 0 && $assigned_menu_id != 0){
        /*  Checking product already exists     */
        $owner_exist = "SELECT user_id FROM user_master 
                        WHERE user_id = ".$product_assigned_to."
                        AND del_by IS NULL";
        $owner_response = mysqli_query($conn, $owner_exist);
        if (mysqli_num_rows($owner_response) == 0){
            echo "User details not found for user id ".$product_assigned_to." in the system.";
            exit();
        }

        $owner_exist = "SELECT menu_id FROM menu_master 
                        WHERE menu_id = ".$assigned_menu_id;
        $owner_response = mysqli_query($conn, $owner_exist);
        if (mysqli_num_rows($owner_response) == 0){
            echo "Menu details not found for menu id ".$assigned_menu_id." in the system.";
            exit();
        }

        $get_product_sql .= " AND a.`product_id` IN (SELECT DISTINCT psm.`product_id` FROM `product_step_master` psm WHERE psm.`menu_id`=".$assigned_menu_id." AND psm.`user_id`=".$product_assigned_to.")";
    }

    $get_product_sql .= " ORDER BY product_name ";

    $result = mysqli_query($conn,$get_product_sql);  
    $json_response = array();  
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {  
        $row_array['product_id'] = $row['product_id'];
        $row_array['product_name'] = $row['product_name'];
        $row_array['product_category'] = $row['product_category'];
        $row_array['product_owner'] = $row['product_owner'];
        $row_array['country_id'] = $row['country_id'];
        $row_array['category_name'] = $row['category_name'];
        $row_array['user_name'] = $row['user_name'];
        $row_array['country_name'] = $row['country_name'];
        $row_array['ent_by'] = $row['ent_by'];
        $row_array['ent_dt'] = $row['ent_dt'];  
        array_push($json_response,$row_array);  
    }

    echo json_encode($json_response); 
    mysqli_free_result($result);

    $conn->close();

?>