<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input   */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
    $product_id = mysqli_real_escape_string($conn, $_REQUEST['product_id']);
    $nom_id = mysqli_real_escape_string($conn, $_REQUEST['nom_id']);

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
    $get_nomenclature_sql = "SELECT * FROM product_nomenclature 
                            WHERE product_id=".$product_id." AND ent_by='".$login_id."' 
                            AND del_by IS NULL ";

    if($nom_id != 0){
        $get_nomenclature_sql .= " AND nom_id = ".$nom_id;
    }

    $get_nomenclature_sql .= " ORDER BY nom_id DESC ";

    $result = mysqli_query($conn,$get_nomenclature_sql);  
    $json_response = array();  
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {  
        $row_array['nom_id'] = $row['nom_id'];
        $row_array['product_id'] = $row['product_id'];
        $row_array['nom_name'] = $row['nom_name'];
        $row_array['pf_value'] = $row['pf_value'];
        $row_array['pharmacopeia_type'] = $row['pharmacopeia_type'];
        $row_array['ent_by'] = $row['ent_by'];
        $row_array['ent_dt'] = $row['ent_dt'];        
        array_push($json_response,$row_array);  
    }

    echo json_encode($json_response); 
    mysqli_free_result($result);

    $conn->close();

?>