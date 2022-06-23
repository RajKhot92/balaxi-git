<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    include "update_vendor_finalization_stats.php";

    /*  Taking user input     */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
	$product_id = mysqli_real_escape_string($conn, $_REQUEST['product_id']);
    $gmp_received_dt = mysqli_real_escape_string($conn, $_REQUEST['gmp_received_dt']);
    $gmp_finalize_dt = mysqli_real_escape_string($conn, $_REQUEST['gmp_finalize_dt']);
    $gmp_remark = mysqli_real_escape_string($conn, $_REQUEST['gmp_remark']);
    $manufacture_received_dt = mysqli_real_escape_string($conn, $_REQUEST['manufacture_received_dt']);
    $manufacture_finalize_dt = mysqli_real_escape_string($conn, $_REQUEST['manufacture_finalize_dt']);
    $manufacture_remark = mysqli_real_escape_string($conn, $_REQUEST['manufacture_remark']);
    $fsc_copp_received_dt = mysqli_real_escape_string($conn, $_REQUEST['fsc_copp_received_dt']);
    $fsc_copp_finalize_dt = mysqli_real_escape_string($conn, $_REQUEST['fsc_copp_finalize_dt']);
    $fsc_copp_remark = mysqli_real_escape_string($conn, $_REQUEST['fsc_copp_remark']);
    $pp_received_dt = mysqli_real_escape_string($conn, $_REQUEST['pp_received_dt']);
    $pp_finalize_dt = mysqli_real_escape_string($conn, $_REQUEST['pp_finalize_dt']);
    $pp_remark = mysqli_real_escape_string($conn, $_REQUEST['pp_remark']);
    $cma_received_dt = mysqli_real_escape_string($conn, $_REQUEST['cma_received_dt']);
    $cma_finalize_dt = mysqli_real_escape_string($conn, $_REQUEST['cma_finalize_dt']);
    $cma_remark = mysqli_real_escape_string($conn, $_REQUEST['cma_remark']);
    
    $conn -> autocommit(FALSE);

    if($login_id === "" || $product_id === "" || $gmp_received_dt === "" || $gmp_finalize_dt === "" || $fsc_copp_received_dt === "" || $fsc_copp_finalize_dt === "" || $cma_received_dt === "" || $cma_finalize_dt === ""){
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
    $mnfctr_rec_dt = $manufacture_received_dt === "" ? "null" : "STR_TO_DATE('".$manufacture_received_dt."', '%m/%d/%Y')";
    $mnfctr_fnl_dt = $manufacture_finalize_dt === "" ? "null" : "STR_TO_DATE('".$manufacture_finalize_dt."', '%m/%d/%Y')";
    $pp_rec_dt = $pp_received_dt === "" ? "null" : "STR_TO_DATE('".$pp_received_dt."', '%m/%d/%Y')";
    $pp_fnl_dt = $pp_finalize_dt === "" ? "null" : "STR_TO_DATE('".$pp_finalize_dt."', '%m/%d/%Y')";

    $add_vendor_doc_collection_sql = "INSERT INTO product_vendor_doc_collection (`product_id`, `gmp_received_dt`, `gmp_finalize_dt`, `gmp_remark`, `manufacture_received_dt`, `manufacture_finalize_dt`, `manufacture_remark`, `fsc_copp_received_dt`, `fsc_copp_finalize_dt`, `fsc_copp_remark`, `pp_received_dt`, `pp_finalize_dt`, `pp_remark`, `cma_received_dt`, `cma_finalize_dt`, `cma_remark`, `ent_by`, `ent_dt`)
                            VALUES (".$product_id.",STR_TO_DATE('".$gmp_received_dt."', '%m/%d/%Y'),STR_TO_DATE('".$gmp_finalize_dt."', '%m/%d/%Y'),'".$gmp_remark."',".$mnfctr_rec_dt.",".$mnfctr_fnl_dt.",'".$manufacture_remark."',STR_TO_DATE('".$fsc_copp_received_dt."', '%m/%d/%Y'),STR_TO_DATE('".$fsc_copp_finalize_dt."', '%m/%d/%Y'),'".$fsc_copp_remark."',".$pp_rec_dt.",".$pp_fnl_dt.",'".$pp_remark."',STR_TO_DATE('".$cma_received_dt."', '%m/%d/%Y'),STR_TO_DATE('".$cma_finalize_dt."', '%m/%d/%Y'),'".$cma_remark."',".$login_id.",NOW())";

    if ($conn->query($add_vendor_doc_collection_sql) !== TRUE) {
        echo "Some error occurred while adding vendor document collection details. Please try again later.";
        exit();
    }
    
    if (!$conn -> commit()) {
        echo "Some error occurred while adding vendor document collection details. Please try again later.";
        exit();
    }else{
        $retval = processStats($login_id,$product_id,$conn);
        if($retval == "0"){
            echo "Some error occurred while updating progress details. Please try again later.";
            exit();
        }else if($retval != "1"){
            echo $retval;
            exit();
        }else{
            echo $retval;
        }
    }

    $conn->close();
?>  