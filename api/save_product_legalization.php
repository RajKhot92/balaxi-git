<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    include "update_submission_stats.php";

    /*  Taking user input     */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
	$product_id = mysqli_real_escape_string($conn, $_REQUEST['product_id']);
    $handed_fa = mysqli_real_escape_string($conn, $_REQUEST['handed_fa']);
    $handed_notary = mysqli_real_escape_string($conn, $_REQUEST['handed_notary']);
    $received_fa = mysqli_real_escape_string($conn, $_REQUEST['received_fa']);
    $received_notary = mysqli_real_escape_string($conn, $_REQUEST['received_notary']);
    
    $conn -> autocommit(FALSE);

    if($login_id === "" || $product_id === "" || $handed_fa === "" || $handed_notary === "" || $received_fa === "" || $received_notary === ""){
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

    /*  Adding product translation     */
    $add_legalization_sql = "INSERT INTO product_submission_legal (`product_id`, `handed_fa`, `handed_notary`, `received_fa`, `received_notary`, `ent_by`, `ent_dt`)
                            VALUES (".$product_id.",STR_TO_DATE('".$handed_fa."', '%m/%d/%Y'),STR_TO_DATE('".$handed_notary."', '%m/%d/%Y'),STR_TO_DATE('".$received_fa."', '%m/%d/%Y'),STR_TO_DATE('".$received_notary."', '%m/%d/%Y'),".$login_id.",NOW())";

    if ($conn->query($add_legalization_sql) !== TRUE) {
        echo "Some error occurred while adding legalization details. Please try again later.";
        exit();
    }
    
    if (!$conn -> commit()) {
        echo "Some error occurred while adding legalization details. Please try again later.";
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