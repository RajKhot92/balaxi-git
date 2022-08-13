<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    include "update_dossier_development_stats.php";

    /*  Taking user input     */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
	$product_id = mysqli_real_escape_string($conn, $_REQUEST['product_id']);
    $legal_doc = mysqli_real_escape_string($conn, $_REQUEST['legal_doc']);
    $fp_ws = mysqli_real_escape_string($conn, $_REQUEST['fp_ws']);
    $qnq = mysqli_real_escape_string($conn, $_REQUEST['qnq']);
    $misc = mysqli_real_escape_string($conn, $_REQUEST['misc']);
    $test_lab = mysqli_real_escape_string($conn, $_REQUEST['test_lab']);
    $file_type = mysqli_real_escape_string($conn, $_REQUEST['file_type']);
    if (!empty($_FILES['amv']['name'])) {
        if ($_FILES['amv']['error'] != 0) {
            echo 'Something wrong with the file.';
            exit();
        }

        $file_name = $_FILES['amv']['name'];
        $file_tmp = $_FILES['amv']['tmp_name'];
    }
    
    $conn -> autocommit(FALSE);

    if($login_id === "" || $product_id === "" || $legal_doc === "" || $fp_ws === "" || $qnq === "" || $misc === "" || $test_lab === ""){
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

    /*  Adding pre requisite     */
    $new_pre_requisite_id = 0;
    $pre_requisite_id_result = mysqli_query($conn, "SELECT IFNULL(MAX(dpr_id),0)+1 AS new_pre_requisite_id FROM product_dossier_pre_requisite");
    while ($row_dprid = mysqli_fetch_array($pre_requisite_id_result, MYSQLI_ASSOC)) {
        $new_pre_requisite_id = $row_dprid["new_pre_requisite_id"];
    }
    
    $target_dir = "amv/";
    $target_file = $target_dir . $new_pre_requisite_id . "-" . basename($_FILES["amv"]["name"]);
    if(move_uploaded_file($_FILES["amv"]["tmp_name"], $target_file)){
        //File successfully uploaded
    }else{
        echo "Some error occurred while uploading file.";
        exit();
    }

    /*  Adding artwork question     */
    $add_pre_requisite_sql = "INSERT INTO product_dossier_pre_requisite (`product_id`, `legal_doc`, `fp_ws`, `qnq`, `misc`, `test_lab`, `amv`, `file_type`, `file_url`, `ent_by`, `ent_dt`)
                            VALUES (".$product_id.",'".$legal_doc."','".$fp_ws."','".$qnq."','".$misc."', '".$test_lab."','','".$file_type."','".$target_file."',".$login_id.",NOW())";

    if ($conn->query($add_pre_requisite_sql) !== TRUE) {
        echo "Some error occurred while adding dossier pre-requisite check details. Please try again later.";
        exit();
    }
    
    if (!$conn -> commit()) {
        echo "Some error occurred while adding dossier pre-requisite check details. Please try again later.";
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
