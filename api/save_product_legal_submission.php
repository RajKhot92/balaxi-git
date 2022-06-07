<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    include "update_document_sample_collection_stats.php";

    /*  Taking user input     */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
	$product_id = mysqli_real_escape_string($conn, $_REQUEST['product_id']);
    $gmp = mysqli_real_escape_string($conn, $_REQUEST['gmp']);
    $fsc = mysqli_real_escape_string($conn, $_REQUEST['fsc']);
    $copp = mysqli_real_escape_string($conn, $_REQUEST['copp']);
    $pp = mysqli_real_escape_string($conn, $_REQUEST['pp']);
    $license_manufacture = mysqli_real_escape_string($conn, $_REQUEST['license_manufacture']);
    $cma = mysqli_real_escape_string($conn, $_REQUEST['cma']);
    
    $conn -> autocommit(FALSE);

    if($login_id === "" || $product_id === "" || $gmp === "" || $fsc === "" || $copp === "" || $cma === ""){
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

    $ppdt = $pp === "" ? "null" : "STR_TO_DATE('".$pp."', '%m/%d/%Y')";
    $lcnsedt = $license_manufacture === "" ? "null" : "STR_TO_DATE('".$license_manufacture."', '%m/%d/%Y')";

    /*  Adding license manufacture     */
    $add_legal_submission_sql = "INSERT INTO product_legal_submission (`product_id`, `gmp`, `fsc`, `copp`, `pp`, `license_manufacture`, `cma`, `ent_by`, `ent_dt`)
                            VALUES (".$product_id.",STR_TO_DATE('".$gmp."', '%m/%d/%Y'), STR_TO_DATE('".$fsc."', '%m/%d/%Y'), STR_TO_DATE('".$copp."', '%m/%d/%Y'), ".$ppdt.", ".$lcnsedt.", STR_TO_DATE('".$cma."', '%m/%d/%Y'),".$login_id.",NOW())";

    if ($conn->query($add_legal_submission_sql) !== TRUE) {
        echo "Some error occurred while adding legal submission details. Please try again later.";
        exit();
    }
    
    if (!$conn -> commit()) {
        echo "Some error occurred while adding legal submission details. Please try again later.";
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