<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    include "update_document_sample_collection_stats.php";

    /*  Taking user input     */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
	$product_id = mysqli_real_escape_string($conn, $_REQUEST['product_id']);
    $batch_no = mysqli_real_escape_string($conn, $_REQUEST['batch_no']);
    $lab_name = mysqli_real_escape_string($conn, $_REQUEST['lab_name']);
    $test_performed = mysqli_real_escape_string($conn, $_REQUEST['test_performed']);
    $submitted_dt = mysqli_real_escape_string($conn, $_REQUEST['submitted_dt']);
    $received_dt = mysqli_real_escape_string($conn, $_REQUEST['received_dt']);
    $result = mysqli_real_escape_string($conn, $_REQUEST['result']);
    $remarks = mysqli_real_escape_string($conn, $_REQUEST['remarks']);
    if (!empty($_FILES['lab_report']['name'])) {
        if ($_FILES['lab_report']['error'] != 0) {
            echo 'Something wrong with the file.';
            exit();
        }

        $file_name = $_FILES['lab_report']['name'];
        $file_tmp = $_FILES['lab_report']['tmp_name'];
        $file_content = addslashes(file_get_contents($_FILES['lab_report']['tmp_name']));
    }
    
    $conn -> autocommit(FALSE);

    if($login_id === "" || $product_id === "" || $batch_no === "" || $lab_name === "" || $test_performed === "" || $submitted_dt === "" || $received_dt === "" || $result === ""){
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
    $add_lab_test_sql = "INSERT INTO product_lab_test (`product_id`, `batch_no`, `lab_name`, `test_performed`, `submitted_dt`, `received_dt`, `result`, `lab_report`, `remarks`, `ent_by`, `ent_dt`)
                            VALUES (".$product_id.",'".$batch_no."','".$lab_name."','".$test_performed."',STR_TO_DATE('".$submitted_dt."', '%m/%d/%Y'),STR_TO_DATE('".$received_dt."', '%m/%d/%Y'),'".$result."','".$file_content."','".$remarks."',".$login_id.",NOW())";

    if ($conn->query($add_lab_test_sql) !== TRUE) {
        echo "Some error occurred while adding lab test details. Please try again later.";
        exit();
    }
    
    if (!$conn -> commit()) {
        echo "Some error occurred while adding lab test details. Please try again later.";
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