<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input     */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
	$product_id = mysqli_real_escape_string($conn, $_REQUEST['product_id']);
    $batch_no = mysqli_real_escape_string($conn, $_REQUEST['batch_no']);
    $test_result = mysqli_real_escape_string($conn, $_REQUEST['test_result']);
    $file_type = mysqli_real_escape_string($conn, $_REQUEST['file_type']);
    if (!empty($_FILES['working_standard']['name'])) {
        if ($_FILES['working_standard']['error'] != 0) {
            echo 'Something wrong with the file.';
            exit();
        }

        $file_name = $_FILES['working_standard']['name'];
        $file_tmp = $_FILES['working_standard']['tmp_name'];
    }
    
    $conn -> autocommit(FALSE);

    if($login_id === "" || $product_id === "" || $batch_no === "" || $test_result === ""){
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
    $new_tr_id = 0;
    $tr_id_result = mysqli_query($conn, "SELECT IFNULL(MAX(tr_id),0)+1 AS new_tr_id FROM product_test_report");
    while ($row_did = mysqli_fetch_array($tr_id_result, MYSQLI_ASSOC)) {
        $new_tr_id = $row_did["new_tr_id"];
    }
    
    $target_dir = "test-report/";
    $target_file = $target_dir . $new_tr_id . "-" . basename($_FILES["working_standard"]["name"]);
    if(move_uploaded_file($_FILES["working_standard"]["tmp_name"], $target_file)){
        //File successfully uploaded
    }else{
        echo "Some error occurred while uploading file.";
        exit();
    }

    $add_writing_sql = "INSERT INTO product_test_report (`tr_id`, `product_id`, `file_type`, `file_url`, `batch_no`, `test_result`, `ent_by`, `ent_dt`)
                            VALUES (".$new_tr_id.",".$product_id.",'".$file_type."','".$target_file."','".$batch_no."','".$test_result."',".$login_id.",NOW())";

    if ($conn->query($add_writing_sql) !== TRUE) {
        echo "Some error occurred while adding test report details. Please try again later.";
        exit();
    }
    
    if (!$conn -> commit()) {
        echo "Some error occurred while adding test report details. Please try again later.";
        exit();
    }else{
        echo "1";
    }

    $conn->close();
?>
