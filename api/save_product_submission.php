<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    include "update_submission_stats.php";

    /*  Taking user input     */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
	$product_id = mysqli_real_escape_string($conn, $_REQUEST['product_id']);
    $submitted_moh = mysqli_real_escape_string($conn, $_REQUEST['submitted_moh']);
    $submission_slip_no = mysqli_real_escape_string($conn, $_REQUEST['submission_slip_no']);
    $registration_complete = mysqli_real_escape_string($conn, $_REQUEST['registration_complete']);
    $registration_certificate = mysqli_real_escape_string($conn, $_REQUEST['registration_certificate']);
    
    if (!empty($_FILES['submission_doc']['name'])) {
        if ($_FILES['submission_doc']['error'] != 0) {
            echo 'Something wrong with the file.';
            exit();
        }

        $file_name = $_FILES['submission_doc']['name'];
        $file_tmp = $_FILES['submission_doc']['tmp_name'];
        $file_content_submission = addslashes(file_get_contents($_FILES['submission_doc']['tmp_name']));
    }
    if (!empty($_FILES['certificate_file']['name'])) {
        if ($_FILES['certificate_file']['error'] != 0) {
            echo 'Something wrong with the file.';
            exit();
        }

        $file_name = $_FILES['certificate_file']['name'];
        $file_tmp = $_FILES['certificate_file']['tmp_name'];
        $file_content_certificate = addslashes(file_get_contents($_FILES['certificate_file']['tmp_name']));
    }
    
    $conn -> autocommit(FALSE);

    if($login_id === "" || $product_id === "" || $submitted_moh === "" || $submission_slip_no === "" || $registration_complete === "" || $registration_certificate === ""){
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
    $add_submission_sql = "INSERT INTO product_submission (`product_id`, `submitted_moh`, `submission_slip_no`, `registration_complete`, `registration_certificate`, `submission_doc`, `certificate_file`, `ent_by`, `ent_dt`)
                            VALUES (".$product_id.",STR_TO_DATE('".$submitted_moh."', '%m/%d/%Y'),STR_TO_DATE('".$submission_slip_no."', '%m/%d/%Y'),STR_TO_DATE('".$registration_complete."', '%m/%d/%Y'),STR_TO_DATE('".$registration_certificate."', '%m/%d/%Y'),'".$file_content_submission."','".$file_content_certificate."',".$login_id.",NOW())";

    if ($conn->query($add_submission_sql) !== TRUE) {
        echo "Some error occurred while adding submission details. Please try again later.";
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