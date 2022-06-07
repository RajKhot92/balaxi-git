<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    include "update_sample_finalization_stats.php";

    /*  Taking user input     */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
	$product_id = mysqli_real_escape_string($conn, $_REQUEST['product_id']);
    $prints = mysqli_real_escape_string($conn, $_REQUEST['prints']);
    $ws_package = mysqli_real_escape_string($conn, $_REQUEST['ws_package']);
    $fp_package = mysqli_real_escape_string($conn, $_REQUEST['fp_package']);
    $document = mysqli_real_escape_string($conn, $_REQUEST['document']);
    
    $conn -> autocommit(FALSE);

    if($login_id === "" || $product_id === "" || $prints === "" || $ws_package === "" || $fp_package === "" || $document === ""){
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

    /*  Adding artwork question     */
    $add_artwork_sql = "INSERT INTO product_artwork_finalize (`product_id`, `prints`, `ws_package`, `fp_package`, `document`, `ent_by`, `ent_dt`)
                            VALUES (".$product_id.",STR_TO_DATE('".$prints."', '%m/%d/%Y'), STR_TO_DATE('".$ws_package."', '%m/%d/%Y'), STR_TO_DATE('".$fp_package."', '%m/%d/%Y'), STR_TO_DATE('".$document."', '%m/%d/%Y'),".$login_id.",NOW())";

    if ($conn->query($add_artwork_sql) !== TRUE) {
        echo "Some error occurred while adding finalization artwork details. Please try again later.";
        exit();
    }
    
    if (!$conn -> commit()) {
        echo "Some error occurred while adding finalization artwork details. Please try again later.";
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