<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input     */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
	$product_id = mysqli_real_escape_string($conn, $_REQUEST['product_id']);
    $product_code = mysqli_real_escape_string($conn, $_REQUEST['product_code']);
    $vendor_id = mysqli_real_escape_string($conn, $_REQUEST['vendor_id']);
    $package_details = mysqli_real_escape_string($conn, $_REQUEST['package_details']);
    $revision_no = mysqli_real_escape_string($conn, $_REQUEST['revision_no']);
    $revision_dt = mysqli_real_escape_string($conn, $_REQUEST['revision_dt']);
    if (!empty($_FILES['artwork_file']['name'])) {
        if ($_FILES['artwork_file']['error'] != 0) {
            echo 'Something wrong with the file.';
            exit();
        }

        $file_name = $_FILES['artwork_file']['name'];
        $file_tmp = $_FILES['artwork_file']['tmp_name'];
        $file_content_artwork = addslashes(file_get_contents($_FILES['artwork_file']['tmp_name']));
    }

    if (!empty($_FILES['commercial_file']['name'])) {
        if ($_FILES['commercial_file']['error'] != 0) {
            echo 'Something wrong with the file.';
            exit();
        }

        $file_name = $_FILES['commercial_file']['name'];
        $file_tmp = $_FILES['commercial_file']['tmp_name'];
        $file_content_commercial = addslashes(file_get_contents($_FILES['commercial_file']['tmp_name']));
    }
    
    $conn -> autocommit(FALSE);

    if($login_id === "" || $product_id === "" || $product_code === "" || $vendor_id === "" || $package_details === "" || $revision_no === "" || $revision_dt === ""){
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
    $add_artwork_sql = "INSERT INTO product_artwork (`product_id`, `product_code`, `vendor_id`, `package_details`, `revision_no`, `revision_dt`, `artwork_file`, `commercial_file`, `ent_by`, `ent_dt`)
                            VALUES (".$product_id.",'".$product_code."',".$vendor_id.",'".$package_details."','".$revision_no."', STR_TO_DATE('".$revision_dt."', '%m/%d/%Y'),'".$file_content_artwork."','".$file_content_commercial."',".$login_id.",NOW())";

    if ($conn->query($add_artwork_sql) !== TRUE) {
        echo "Some error occurred while adding artwork details. Please try again later.";
        exit();
    }
    
    if (!$conn -> commit()) {
        echo "Some error occurred while adding artwork details. Please try again later.";
        exit();
    }else{
        echo "1";
    }

    $conn->close();
?>