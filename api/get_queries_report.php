<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input   */
    $country_id = mysqli_real_escape_string($conn, $_REQUEST['country_id']);
    $user_id = mysqli_real_escape_string($conn, $_REQUEST['user_id']);
    $user_role = mysqli_real_escape_string($conn, $_REQUEST['user_role']);
    $category = mysqli_real_escape_string($conn, $_REQUEST['category']);

    /*  Getting market research    */
    $get_queries_sql = "SELECT a.`product_id`,a.`product_name`,b.`psq_id`,
                        b.`query_no`, b.`deadline_dt`,b.`received_date`,b.`ent_by`,
                        c.`category_name`,a.`product_owner` as user_id,d.`user_name` as product_owner,e.`category_name` as product_category,
                    (SELECT `vendor_name` from product_vendor_finalization 
                        WHERE `vf_id` = (SELECT max(vf_id) FROM product_vendor_finalization WHERE `product_id` = a.`product_id` AND del_by IS NULL)) supplier_name  
                        from product_master a 
                        INNER JOIN product_queries_received b ON a.`product_id`=b.`product_id`
                        INNER JOIN query_category c ON b.`query_category`=c.`category_id`
                        INNER JOIN user_master d on a.`product_owner`=d.`user_id` 
                        INNER JOIN product_category e ON a.`product_category`=e.`category_id`
                        AND a.`del_by` IS NULL ";


    if($country_id != 0){
        $get_queries_sql .= " AND a.`country_id`=".$country_id;
    }

    if($category == "pending"){
        $get_queries_sql .= " AND a.`product_id` not in (SELECT DISTINCT `product_id` FROM `product_queries_solution` where `del_by` IS NULL)";
    }else{
        $get_queries_sql .= " AND a.`product_id` in (SELECT DISTINCT `product_id` FROM `product_queries_solution` where `del_by` IS NULL)";
    }

    $get_queries_sql .= " ORDER BY a.`product_id`,b.`psq_id` DESC ";

    $result = mysqli_query($conn,$get_queries_sql);  
    $json_response = array();  
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {  
        $row_array['psq_id'] = $row['psq_id'];
        $row_array['product_id'] = $row['product_id'];
        $row_array['product_name'] = $row['product_name'];
        $row_array['user_id'] = $row['user_id'];
        $row_array['product_owner'] = $row['product_owner'];
        $row_array['query_no'] = $row['query_no'];
        $row_array['category_name'] = $row['category_name'];
        $row_array['deadline_dt'] = $row['deadline_dt'];
        $row_array['received_date'] = $row['received_date'];
        $row_array['product_category'] = $row['product_category'];
        $row_array['supplier_name'] = $row['supplier_name'] == null ? "-" : $row['supplier_name'];
        $row_array['ent_by'] = $row['ent_by'];
        array_push($json_response,$row_array);
    }

    echo json_encode($json_response); 
    mysqli_free_result($result);

    $conn->close();

?>