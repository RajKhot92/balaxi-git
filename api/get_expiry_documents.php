<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input   */
    $country_id = mysqli_real_escape_string($conn, $_REQUEST['country_id']);
    $last_month = mysqli_real_escape_string($conn, $_REQUEST['last_month']);
    
    /*  Getting roles    */
    $get_progress_sql = "SELECT a.`product_id`,a.`product_name`,
    					b.`user_name` as product_owner,c.`category_name`,
                        (SELECT max(vendor_name) from product_vendor_finalization WHERE product_id = a.`product_id`) supplier_name,
						(SELECT pc.`validity` from product_cma pc WHERE pc.`cma_id` = (SELECT MAX(`cma_id`) FROM product_cma WHERE `product_id`=a.`product_id`) AND pc.`validity` between CURDATE() and DATE_ADD(CURDATE(), INTERVAL ".$last_month." MONTH) ) cma,
						(SELECT pco.`validity` from product_copp pco WHERE pco.`copp_id` = (SELECT MAX(`copp_id`) FROM product_copp WHERE `product_id`=a.`product_id`) AND pco.`validity` between CURDATE() and DATE_ADD(CURDATE(), INTERVAL ".$last_month." MONTH) ) copp,
						(SELECT pf.`validity` from product_fsc pf WHERE pf.`fsc_id` = (SELECT MAX(`fsc_id`) FROM product_fsc WHERE `product_id`=a.`product_id`) AND pf.`validity` between CURDATE() and DATE_ADD(CURDATE(), INTERVAL ".$last_month." MONTH) ) fsc,
						(SELECT pg.`validity` from product_gmp pg WHERE pg.`gmp_id` = (SELECT MAX(`gmp_id`) FROM product_gmp WHERE `product_id`=a.`product_id`) AND pg.`validity` between CURDATE() and DATE_ADD(CURDATE(), INTERVAL ".$last_month." MONTH) ) gmp,
						(SELECT pp.`validity` from product_pp pp WHERE pp.`pp_id` = (SELECT MAX(`pp_id`) FROM product_pp WHERE `product_id`=a.`product_id`)) pp,
						(SELECT pr.registration_expired from product_registration pr WHERE pr.`pr_id` = (SELECT MAX(`pr_id`) FROM product_registration WHERE `product_id`=a.`product_id`) AND pr.`registration_expired` between CURDATE() and DATE_ADD(CURDATE(), INTERVAL ".$last_month." MONTH) ) registration,
						(SELECT pfp.`validity` from product_finish_product pfp WHERE pfp.`fp_id` = (SELECT MAX(`fp_id`) FROM product_finish_product WHERE `product_id`=a.`product_id`) AND pfp.`validity` between CURDATE() and DATE_ADD(CURDATE(), INTERVAL ".$last_month." MONTH) ) finish_product
						from product_master a INNER JOIN user_master b on a.`product_owner`=b.`user_id` 
                        INNER JOIN product_category c on a.`product_category`=c.`category_id` WHERE a.del_by IS NULL ";

    if($country_id != 0){
        $get_progress_sql .= "AND a.`country_id`=".$country_id;
    }

    $result = mysqli_query($conn,$get_progress_sql);  
    $json_response = array();  
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {  
        $row_array['product_id'] = $row['product_id'];
        $row_array['product_name'] = $row['product_name'];
        $row_array['product_owner'] = $row['product_owner'];
        $row_array['category_name'] = $row['category_name'];
        $row_array['supplier_name'] = $row['supplier_name'] == null ? "-" : $row['supplier_name'];
        $row_array['cma'] = $row['cma'];
        $row_array['copp'] = $row['copp'];
        $row_array['fsc'] = $row['fsc'];
        $row_array['gmp'] = $row['gmp'];
        $row_array['registration'] = $row['registration'];
        $row_array['finish_product'] = $row['finish_product'];
        array_push($json_response,$row_array);  
    }

    echo json_encode($json_response); 
    mysqli_free_result($result);

    $conn->close();

?>