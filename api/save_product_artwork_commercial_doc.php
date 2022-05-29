<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input     */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
	$product_id = mysqli_real_escape_string($conn, $_REQUEST['product_id']);
    $product_code = mysqli_real_escape_string($conn, $_REQUEST['product_code']);
    if (!empty($_FILES['doc_1']['name'])) {
        if ($_FILES['doc_1']['error'] != 0) {
            echo 'Something wrong with the file.';
            exit();
        }

        $file_name = $_FILES['doc_1']['name'];
        $file_tmp = $_FILES['doc_1']['tmp_name'];
        $file_content_doc_1 = addslashes(file_get_contents($_FILES['doc_1']['tmp_name']));
    }

    if (!empty($_FILES['doc_2']['name'])) {
        if ($_FILES['doc_2']['error'] != 0) {
            echo 'Something wrong with the file.';
            exit();
        }

        $file_name = $_FILES['doc_2']['name'];
        $file_tmp = $_FILES['doc_2']['tmp_name'];
        $file_content_doc_2 = addslashes(file_get_contents($_FILES['doc_2']['tmp_name']));
    }
    
    $conn -> autocommit(FALSE);

    if($login_id === "" || $product_id === "" || $product_code === ""){
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
    $add_commercial_doc_sql = "INSERT INTO product_commercial_doc (`product_id`, `product_code`,  `doc_1`, `doc_2`, `ent_by`, `ent_dt`)
                            VALUES (".$product_id.",'".$product_code."','".$file_content_doc_1."','".$file_content_doc_2."',".$login_id.",NOW())";

    if ($conn->query($add_commercial_doc_sql) !== TRUE) {
        echo "Some error occurred while adding commercial document details. Please try again later.";
        exit();
    }
    
    if (!$conn -> commit()) {
        echo "Some error occurred while adding commercial document details. Please try again later.";
        exit();
    }else{
        echo "1";
    }

    $conn->close();
?>