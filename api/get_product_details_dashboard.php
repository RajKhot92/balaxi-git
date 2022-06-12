<?php
    header('Access-Control-Allow-Origin: *');
    include "db_connect.php";

    /*  Taking user input   */
    // $category_id = mysqli_real_escape_string($conn, $_REQUEST['category_id']);

    /*  Getting categories    */
    $get_product_report_sql = "SELECT a.`product_name`,b.`country_name`,c.`user_name` as owner,
                        (SELECT max(vendor_name) from product_vendor_finalization WHERE product_id = a.`product_id`) supplier_name,
                        (SELECT count(*) from product_gmp WHERE product_id = a.`product_id`) gmp,
                        (SELECT count(*) from product_fsc WHERE product_id = a.`product_id`) fsc,
                        (SELECT count(*) from product_cma WHERE product_id = a.`product_id`) cma,
                        (SELECT count(*) from product_dossier_writing WHERE product_id = a.`product_id`) dossier,
                        (SELECT count(*) from product_dossier_pre_requisite WHERE product_id = a.`product_id`) amv
                        FROM `product_master` a 
                        INNER JOIN `country_master` b ON a.`country_id`=b.`country_id`
                        INNER JOIN `user_master` c ON a.`product_owner`=c.`user_id`";

    // if($category_id != 0){
    //     /*  Checking role already exists     */
    //     $role_exist = "SELECT category_id FROM product_category 
    //                     WHERE category_id = ".$category_id."
    //                     AND del_by IS NULL";
    //     $role_response = mysqli_query($conn, $role_exist);
    //     if (mysqli_num_rows($role_response) == 0){
    //         echo "Product category details not found for category id ".$category_id." in the system.";
    //         exit();
    //     }

    //     $get_product_report_sql .= " AND category_id = ".$category_id;
    // }

    // $get_product_report_sql .= " ORDER BY category_id DESC ";

    $result = mysqli_query($conn,$get_product_report_sql);  
    $json_response = array();  
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {  
        $row_array['product_name'] = $row['product_name'];
        $row_array['country_name'] = $row['country_name'];
        $row_array['owner'] = $row['owner'];
        $row_array['supplier_name'] = $row['supplier_name'];
        $row_array['gmp'] = $row['gmp'];
        $row_array['fsc'] = $row['fsc'];
        $row_array['cma'] = $row['cma'];
        $row_array['dossier'] = $row['dossier'];
        $row_array['amv'] = $row['amv'];
        array_push($json_response,$row_array);  
    }

    echo json_encode($json_response); 
    mysqli_free_result($result);

    $conn->close();

?>