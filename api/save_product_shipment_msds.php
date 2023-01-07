<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input     */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
	$product_id = mysqli_real_escape_string($conn, $_REQUEST['product_id']);
    $msds_dt = mysqli_real_escape_string($conn, $_REQUEST['msds_dt']);
    $file_type = mysqli_real_escape_string($conn, $_REQUEST['file_type']);
    if (!empty($_FILES['msds']['name'])) {
        if ($_FILES['msds']['error'] != 0) {
            echo 'Something wrong with the file.';
            exit();
        }

        $file_name = $_FILES['msds']['name'];
        $file_tmp = $_FILES['msds']['tmp_name'];
    }
    
    $conn -> autocommit(FALSE);

    if($login_id === "" || $product_id === "" || $msds_dt === ""){
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
    $new_msds_id = 0;
    $msds_id_result = mysqli_query($conn, "SELECT IFNULL(MAX(msds_id),0)+1 AS new_msds_id FROM product_shipment_msds");
    while ($row_msds_id = mysqli_fetch_array($msds_id_result, MYSQLI_ASSOC)) {
        $new_msds_id = $row_msds_id["new_msds_id"];
    }
    
    $target_dir = "msds/";
    $target_file = $target_dir . $new_msds_id . "-" . basename($_FILES["msds"]["name"]);
    if(move_uploaded_file($_FILES["msds"]["tmp_name"], $target_file)){
        //File successfully uploaded
    }else{
        echo "Some error occurred while uploading file.";
        exit();
    }

    $add_msds_sql = "INSERT INTO product_shipment_msds (`msds_id`, `product_id`, `msds_dt`, `file_type`, `file_url`, `ent_by`, `ent_dt`)
                            VALUES (".$new_msds_id.",".$product_id.",STR_TO_DATE('".$msds_dt."', '%m/%d/%Y'),'".$file_type."','".$target_file."',".$login_id.",NOW())";

    if ($conn->query($add_msds_sql) !== TRUE) {
        echo "Some error occurred while adding msds details. Please try again later.";
        exit();
    }
    
    if (!$conn -> commit()) {
        echo "Some error occurred while adding msds details. Please try again later.";
        exit();
    }else{
        echo "1";
    }

    $conn->close();
?>
