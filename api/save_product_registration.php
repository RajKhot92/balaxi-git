<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    include "update_submission_stats.php";

    /*  Taking user input     */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
	$product_id = mysqli_real_escape_string($conn, $_REQUEST['product_id']);
    $registration_complete = mysqli_real_escape_string($conn, $_REQUEST['registration_complete']);
    $registration_expired = mysqli_real_escape_string($conn, $_REQUEST['registration_expired']);
    $rejection_slip_date = mysqli_real_escape_string($conn, $_REQUEST['rejection_slip_date']);
    $rejection_remark = mysqli_real_escape_string($conn, $_REQUEST['rejection_remark']);
    
    $file_content_certificate = '';
    if (!empty($_FILES['certificate_file']['name'])) {
        if ($_FILES['certificate_file']['error'] != 0) {
            echo 'Something wrong with the file.';
            exit();
        }

        $file_name = $_FILES['certificate_file']['name'];
        $file_tmp = $_FILES['certificate_file']['tmp_name'];
        $file_content_certificate = addslashes(file_get_contents($_FILES['certificate_file']['tmp_name']));
    }

    $file_content_rejection = '';
    if (!empty($_FILES['rejection_slip']['name'])) {
        if ($_FILES['rejection_slip']['error'] != 0) {
            echo 'Something wrong with the file.';
            exit();
        }

        $file_name = $_FILES['rejection_slip']['name'];
        $file_tmp = $_FILES['rejection_slip']['tmp_name'];
        $file_content_rejection = addslashes(file_get_contents($_FILES['rejection_slip']['tmp_name']));
    }
    
    $conn -> autocommit(FALSE);

    if($login_id === "" || $product_id === ""){
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

    $register_category = 0;
    $register_category_sql = "SELECT category_id FROM product_category 
                WHERE UPPER(category_name) = UPPER('REGISTERED')
                AND del_by IS NULL";
    $register_category_response = mysqli_query($conn, $register_category_sql);
    if (mysqli_num_rows($register_category_response) == 0){
        echo "Category details not found for category name Registered in the system.";
        exit();
    }else{
        while ($row_category = mysqli_fetch_array($register_category_response, MYSQLI_ASSOC)) {
            $register_category = $row_category['category_id'];
        }
    }

    $rgstr_complete = $registration_complete === "" ? "null" : "STR_TO_DATE('".$registration_complete."', '%m/%d/%Y')";
    $rgstr_expired = $registration_expired === "" ? "null" : "STR_TO_DATE('".$registration_expired."', '%m/%d/%Y')";
    $fil_certificate = $file_content_certificate === "" ? "null" : "'".$file_content_certificate."'";
    $rjctn_slip_date = $rejection_slip_date === "" ? "null" : "STR_TO_DATE('".$rejection_slip_date."', '%m/%d/%Y')";
    $fil_rejection = $file_content_rejection === "" ? "null" : "'".$file_content_rejection."'";

    /*  Adding product registration    */
    $add_registration_sql = "INSERT INTO product_registration (`product_id`, `registration_complete`, `registration_expired`, `certificate_file`, `rejection_slip_date`, `rejection_slip`, `rejection_remark`, `ent_by`, `ent_dt`)
                            VALUES (".$product_id.",".$rgstr_complete.",".$rgstr_expired.",".$fil_certificate.",".$rjctn_slip_date.",".$fil_rejection.",'".$rejection_remark."',".$login_id.",NOW())";

    $update_category_sql = "UPDATE product_master 
                            SET product_category=".$register_category." WHERE product_id=".$product_id;

    if ($conn->query($add_registration_sql) !== TRUE || $conn->query($update_category_sql) !== TRUE) {
        echo "Some error occurred while adding registration details. Please try again later.";
        exit();
    }
    
    if (!$conn -> commit()) {
        echo "Some error occurred while adding registration details. Please try again later.";
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