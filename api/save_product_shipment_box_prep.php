<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    include "update_shipment_stats.php";

    /*  Taking user input     */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
	$product_id = mysqli_real_escape_string($conn, $_REQUEST['product_id']);
    $box_list_date = mysqli_real_escape_string($conn, $_REQUEST['box_list_date']);
    if (!empty($_FILES['box_list_file']['name'])) {
        if ($_FILES['box_list_file']['error'] != 0) {
            echo 'Something wrong with the file.';
            exit();
        }

        $file_name = $_FILES['box_list_file']['name'];
        $file_tmp = $_FILES['box_list_file']['tmp_name'];
        $box_list_file = addslashes(file_get_contents($_FILES['box_list_file']['tmp_name']));
    }

    if (!empty($_FILES['box']['name'])) {
        if ($_FILES['box']['error'] != 0) {
            echo 'Something wrong with the file.';
            exit();
        }

        $file_name = $_FILES['box']['name'];
        $file_tmp = $_FILES['box']['tmp_name'];
        $box = addslashes(file_get_contents($_FILES['box']['tmp_name']));
    }

    if (!empty($_FILES['finish_product']['name'])) {
        if ($_FILES['finish_product']['error'] != 0) {
            echo 'Something wrong with the file.';
            exit();
        }

        $file_name = $_FILES['finish_product']['name'];
        $file_tmp = $_FILES['finish_product']['tmp_name'];
        $finish_product = addslashes(file_get_contents($_FILES['finish_product']['tmp_name']));
    }

    if (!empty($_FILES['work_standard']['name'])) {
        if ($_FILES['work_standard']['error'] != 0) {
            echo 'Something wrong with the file.';
            exit();
        }

        $file_name = $_FILES['work_standard']['name'];
        $file_tmp = $_FILES['work_standard']['tmp_name'];
        $work_standard = addslashes(file_get_contents($_FILES['work_standard']['tmp_name']));
    }

    if (!empty($_FILES['document']['name'])) {
        if ($_FILES['document']['error'] != 0) {
            echo 'Something wrong with the file.';
            exit();
        }

        $file_name = $_FILES['document']['name'];
        $file_tmp = $_FILES['document']['tmp_name'];
        $document = addslashes(file_get_contents($_FILES['document']['tmp_name']));
    }

    if (!empty($_FILES['box_list_box']['name'])) {
        if ($_FILES['box_list_box']['error'] != 0) {
            echo 'Something wrong with the file.';
            exit();
        }

        $file_name = $_FILES['box_list_box']['name'];
        $file_tmp = $_FILES['box_list_box']['tmp_name'];
        $box_list_box = addslashes(file_get_contents($_FILES['box_list_box']['tmp_name']));
    }
    
    $conn -> autocommit(FALSE);

    if($login_id === "" || $product_id === "" || $box_list_date === ""){
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

    /*  Adding shipment booking     */
    $add_shipment_box_prep_sql = "INSERT INTO product_shipment_box_prep (`product_id`, `box_list_date`, `box_list_file`, `box`, `finish_product`, `work_standard`, `document`, `box_list_box`, `ent_by`, `ent_dt`)
                            VALUES (".$product_id.",STR_TO_DATE('".$box_list_date."', '%m/%d/%Y'),'".$box_list_file."','".$box."','".$finish_product."','".$work_standard."','".$document."','".$box_list_box."',".$login_id.",NOW())";

    if ($conn->query($add_shipment_box_prep_sql) !== TRUE) {
        echo "Some error occurred while adding shipment box prep details. Please try again later.";
        exit();
    }
    
    if (!$conn -> commit()) {
        echo "Some error occurred while adding shipment box prep details. Please try again later.";
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