<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    include "update_submission_stats.php";

    /*  Taking user input     */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
	$product_id = mysqli_real_escape_string($conn, $_REQUEST['product_id']);
    $received_date = mysqli_real_escape_string($conn, $_REQUEST['received_date']);
    $total_queries = mysqli_real_escape_string($conn, $_REQUEST['total_queries']);
    $queries = mysqli_real_escape_string($conn, $_REQUEST['queries']);
    $solution_received = mysqli_real_escape_string($conn, $_REQUEST['solution_received']);
    $solution_submitted = mysqli_real_escape_string($conn, $_REQUEST['solution_submitted']);
    $submission_slip_date = mysqli_real_escape_string($conn, $_REQUEST['submission_slip_date']);
    
    if (!empty($_FILES['queries_file']['name'])) {
        if ($_FILES['queries_file']['error'] != 0) {
            echo 'Something wrong with the file.';
            exit();
        }

        $file_name = $_FILES['queries_file']['name'];
        $file_tmp = $_FILES['queries_file']['tmp_name'];
        $file_content_queries = addslashes(file_get_contents($_FILES['queries_file']['tmp_name']));
    }

    if (!empty($_FILES['submission_slip']['name'])) {
        if ($_FILES['submission_slip']['error'] != 0) {
            echo 'Something wrong with the file.';
            exit();
        }

        $file_name = $_FILES['submission_slip']['name'];
        $file_tmp = $_FILES['submission_slip']['tmp_name'];
        $file_content_submission = addslashes(file_get_contents($_FILES['submission_slip']['tmp_name']));
    }
    
    $conn -> autocommit(FALSE);

    if($login_id === "" || $product_id === "" || $received_date === "" || $total_queries === "" || $queries === "" || $solution_received === "" || $solution_submitted === "" || $submission_slip_date === ""){
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

    /*  Adding product queries     */
    $add_queries_sql = "INSERT INTO product_submission_query (`product_id`, `received_date`, `total_queries`, `queries_file`, `queries`, `solution_received`, `solution_submitted`, `submission_slip`, `submission_slip_date`, `ent_by`, `ent_dt`)
                            VALUES (".$product_id.",STR_TO_DATE('".$received_date."', '%m/%d/%Y'),".$total_queries.",'".$file_content_queries."','".$queries."',STR_TO_DATE('".$solution_received."', '%m/%d/%Y'),STR_TO_DATE('".$solution_submitted."', '%m/%d/%Y'),'".$file_content_submission."',STR_TO_DATE('".$submission_slip_date."', '%m/%d/%Y'),".$login_id.",NOW())";

    if ($conn->query($add_queries_sql) !== TRUE) {
        echo "Some error occurred while adding queries details. Please try again later.";
        exit();
    }
    
    if (!$conn -> commit()) {
        echo "Some error occurred while adding queries details. Please try again later.";
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