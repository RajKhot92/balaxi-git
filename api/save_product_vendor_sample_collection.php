<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input     */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
	$product_id = mysqli_real_escape_string($conn, $_REQUEST['product_id']);
    $work_standard_received_dt = mysqli_real_escape_string($conn, $_REQUEST['work_standard_received_dt']);
    $work_standard_finalize_dt = mysqli_real_escape_string($conn, $_REQUEST['work_standard_finalize_dt']);
    $work_standard_remark = mysqli_real_escape_string($conn, $_REQUEST['work_standard_remark']);
    $finish_product_received_dt = mysqli_real_escape_string($conn, $_REQUEST['finish_product_received_dt']);
    $finish_product_finalize_dt = mysqli_real_escape_string($conn, $_REQUEST['finish_product_finalize_dt']);
    $finish_product_remark = mysqli_real_escape_string($conn, $_REQUEST['finish_product_remark']);
    
    $conn -> autocommit(FALSE);

    if($login_id === "" || $product_id === "" || $work_standard_received_dt === "" || $work_standard_finalize_dt === "" || $work_standard_remark === "" || $finish_product_received_dt === "" || $finish_product_finalize_dt === "" || $finish_product_remark === ""){
        echo "Kindly provide valid input.";
        exit();
    }

    /*  Checking user exist    */
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

    /*  Adding license manufacture     */
    $add_vendor_sample_collection_sql = "INSERT INTO product_vendor_sample_collection (`product_id`, `work_standard_received_dt`, `work_standard_finalize_dt`, `work_standard_remark`, `finish_product_received_dt`, `finish_product_finalize_dt`, `finish_product_remark`, `ent_by`, `ent_dt`)
                            VALUES (".$product_id.",STR_TO_DATE('".$work_standard_received_dt."', '%m/%d/%Y'),STR_TO_DATE('".$work_standard_finalize_dt."', '%m/%d/%Y'),'".$work_standard_remark."',STR_TO_DATE('".$finish_product_received_dt."', '%m/%d/%Y'),STR_TO_DATE('".$finish_product_finalize_dt."', '%m/%d/%Y'),'".$finish_product_remark."',".$login_id.",NOW())";

    if ($conn->query($add_vendor_sample_collection_sql) !== TRUE) {
        echo "Some error occurred while adding vendor sample collection details. Please try again later.";
        exit();
    }
    
    if (!$conn -> commit()) {
        echo "Some error occurred while adding vendor sample collection details. Please try again later.";
        exit();
    }else{
        echo "1";
    }

    $conn->close();
?>  