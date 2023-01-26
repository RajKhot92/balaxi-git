<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    include "update_document_sample_collection_stats.php";

    /*  Taking user input     */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
	$product_id = mysqli_real_escape_string($conn, $_REQUEST['product_id']);
    $manufacture_name = mysqli_real_escape_string($conn, $_REQUEST['manufacture_name']);
    $validity = mysqli_real_escape_string($conn, $_REQUEST['validity']);
    $received_date = mysqli_real_escape_string($conn, $_REQUEST['received_date']);
    $file_type = mysqli_real_escape_string($conn, $_REQUEST['file_type']);
    $file_content = '';
    if (!empty($_FILES['license']['name'])) {
        if ($_FILES['license']['error'] != 0) {
            echo 'Something wrong with the file.';
            exit();
        }

        $file_name = $_FILES['license']['name'];
        $file_tmp = $_FILES['license']['tmp_name'];
        $file_content = addslashes(file_get_contents($_FILES['license']['tmp_name']));
    }
    
    $conn -> autocommit(FALSE);

    if($login_id === "" || $product_id === "" || $manufacture_name === ""){
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

    $vldt = $validity === "" ? "null" : "STR_TO_DATE('".$validity."', '%m/%d/%Y')";
    $rcvd_dt = $received_date === "" ? "null" : "STR_TO_DATE('".$received_date."', '%m/%d/%Y')";
    $fil_typ = $file_type === "" ? "null" : "'".$file_type."'";
    $fil_url = "null";

    /*  Adding license manufacture     */
    if ($file_type != "") {
        $new_license_id = 0;
        $license_id_result = mysqli_query($conn, "SELECT IFNULL(MAX(lm_id),0)+1 AS new_license_id FROM product_license_manufacture");
        while ($row_lmid = mysqli_fetch_array($license_id_result, MYSQLI_ASSOC)) {
            $new_license_id = $row_lmid["new_license_id"];
        }
        
        $target_dir = "license-manufacture/";
        $target_file = $target_dir . $new_license_id . "-" . basename($_FILES["license"]["name"]);
        if(move_uploaded_file($_FILES["license"]["tmp_name"], $target_file)){
            //File successfully uploaded
            $fil_url = "'".$target_file."'";
        }else{
            echo "Some error occurred while uploading file.";
            exit();
        }
    }

    $add_license_manufacture_sql = "INSERT INTO product_license_manufacture (`product_id`, `manufacture_name`, `validity`, `received_date`, `file_type`, `file_url`, `ent_by`, `ent_dt`)
                            VALUES (".$product_id.",'".$manufacture_name."',".$vldt.",".$rcvd_dt.",".$fil_typ.",".$fil_url.",".$login_id.",NOW())";

    if ($conn->query($add_license_manufacture_sql) !== TRUE) {
        echo "Some error occurred while adding license manufacture details. Please try again later.";
        exit();
    }
    
    if (!$conn -> commit()) {
        echo "Some error occurred while adding license manufacture details. Please try again later.";
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