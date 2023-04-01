<?php
    header('Access-Control-Allow-Origin: *');
    include "db_connect.php";

    /*  Taking user input   */
    $country_id = mysqli_real_escape_string($conn, $_REQUEST['country_id']);
    $user_id = mysqli_real_escape_string($conn, $_REQUEST['user_id']);
    $user_role = mysqli_real_escape_string($conn, $_REQUEST['user_role']);

    /*  Getting categories    */
    $get_product_report_sql = "SELECT a.`product_id`,a.`product_name`,b.`country_name`,c.`user_name` as owner,d.`category_name`,
                        (SELECT `vendor_name` from product_vendor_finalization 
                        WHERE `vf_id` = (SELECT max(vf_id) FROM product_vendor_finalization WHERE `product_id` = a.`product_id` AND del_by IS NULL)) supplier_name,
                        (SELECT count(*) from product_gmp WHERE product_id = a.`product_id`) gmp,
                        (SELECT count(*) from product_fsc WHERE product_id = a.`product_id`) fsc,
                        (SELECT count(*) from product_cma WHERE product_id = a.`product_id`) cma,
                        (SELECT count(*) from product_dossier_writing WHERE product_id = a.`product_id`) dossier,
                        (SELECT count(*) from product_dossier_pre_requisite WHERE product_id = a.`product_id`) amv,
                        (SELECT ROUND(IFNULL(SUM(progress)/7,0),2) from product_step_master WHERE product_id = a.`product_id`) progress
                        FROM `product_master` a 
                        INNER JOIN `country_master` b ON a.`country_id`=b.`country_id`
                        INNER JOIN `user_master` c ON a.`product_owner`=c.`user_id`
                        INNER JOIN `product_category` d ON a.`product_category`=d.`category_id` AND d.`category_NAME` NOT IN ('HOLD')
                        WHERE a.`product_id` IN (
                        SELECT `product_id` FROM product_step_master) 
                        AND a.`product_id` NOT IN (
                        SELECT `product_id` FROM product_shipment_dispatch)
                        AND a.`product_id` NOT IN (
                        SELECT `product_id` FROM product_submission)
                        AND a.`product_id` NOT IN (
                        SELECT `product_id` FROM product_registration)
                        AND a.`del_by` IS NULL  ";

    if($country_id != 0){
        $get_product_report_sql .= "AND a.`country_id`=".$country_id;
    }

    $result = mysqli_query($conn,$get_product_report_sql);  
    $json_response = array();  
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {  
        $row_array['product_id'] = $row['product_id'];
        $row_array['product_name'] = $row['product_name'];
        $row_array['country_name'] = $row['country_name'];
        $row_array['category_name'] = $row['category_name'];
        $row_array['owner'] = $row['owner'];
        $row_array['supplier_name'] = $row['supplier_name'];
        $row_array['gmp'] = $row['gmp'];
        $row_array['fsc'] = $row['fsc'];
        $row_array['cma'] = $row['cma'];
        $row_array['dossier'] = $row['dossier'];
        $row_array['amv'] = $row['amv'];
        $row_array['progress'] = $row['progress'];
        array_push($json_response,$row_array);  
    }

    echo json_encode($json_response); 
    mysqli_free_result($result);

    $conn->close();

?>