<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input     */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
	$product_id = mysqli_real_escape_string($conn, $_REQUEST['product_id']);
    $legal_doc = mysqli_real_escape_string($conn, $_REQUEST['legal_doc']);
    $fp_ws = mysqli_real_escape_string($conn, $_REQUEST['fp_ws']);
    $qnq = mysqli_real_escape_string($conn, $_REQUEST['qnq']);
    $misc = mysqli_real_escape_string($conn, $_REQUEST['misc']);
    $test_lab = mysqli_real_escape_string($conn, $_REQUEST['test_lab']);
    if (!empty($_FILES['amv']['name'])) {
        if ($_FILES['amv']['error'] != 0) {
            echo 'Something wrong with the file.';
            exit();
        }

        $file_name = $_FILES['amv']['name'];
        $file_tmp = $_FILES['amv']['tmp_name'];
        $file_content = addslashes(file_get_contents($_FILES['amv']['tmp_name']));
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

    /*  Adding artwork question     */
    $add_pre_requisite_sql = "INSERT INTO product_dossier_pre_requisite (`product_id`, `legal_doc`, `fp_ws`, `qnq`, `misc`, `test_lab`, `amv`, `ent_by`, `ent_dt`)
                            VALUES (".$product_id.",'".$legal_doc."','".$fp_ws."','".$qnq."','".$misc."', '".$test_lab."','".$file_content."',".$login_id.",NOW())";

    if ($conn->query($add_pre_requisite_sql) !== TRUE) {
        echo "Some error occurred while adding dossier pre-requisite check details. Please try again later.";
        exit();
    }
    
    if (!$conn -> commit()) {
        echo "Some error occurred while adding dossier pre-requisite check details. Please try again later.";
        exit();
    }else{
        echo "1";
    }

    $conn->close();
?>