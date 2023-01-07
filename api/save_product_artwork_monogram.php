<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input     */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
	$product_id = mysqli_real_escape_string($conn, $_REQUEST['product_id']);
    $monogram_remark = mysqli_real_escape_string($conn, $_REQUEST['monogram_remark']);
    $file_type = mysqli_real_escape_string($conn, $_REQUEST['file_type']);
    if (!empty($_FILES['monogram']['name'])) {
        if ($_FILES['monogram']['error'] != 0) {
            echo 'Something wrong with the file.';
            exit();
        }

        $file_name = $_FILES['monogram']['name'];
        $file_tmp = $_FILES['monogram']['tmp_name'];
    }
    
    $conn -> autocommit(FALSE);

    if($login_id === "" || $product_id === "" || $monogram_remark === ""){
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
    $new_monogram_id = 0;
    $monogram_id_result = mysqli_query($conn, "SELECT IFNULL(MAX(monogram_id),0)+1 AS new_monogram_id FROM product_artwork_monogram");
    while ($row_monogram_id = mysqli_fetch_array($monogram_id_result, MYSQLI_ASSOC)) {
        $new_monogram_id = $row_monogram_id["new_monogram_id"];
    }
    
    $target_dir = "monogram/";
    $target_file = $target_dir . $new_monogram_id . "-" . basename($_FILES["monogram"]["name"]);
    if(move_uploaded_file($_FILES["monogram"]["tmp_name"], $target_file)){
        //File successfully uploaded
    }else{
        echo "Some error occurred while uploading file.";
        exit();
    }

    $add_monogram_sql = "INSERT INTO product_artwork_monogram (`monogram_id`, `product_id`, `monogram_remark`, `file_type`, `file_url`, `ent_by`, `ent_dt`)
                            VALUES (".$new_monogram_id.",".$product_id.",'".$monogram_remark."','".$file_type."','".$target_file."',".$login_id.",NOW())";

    if ($conn->query($add_monogram_sql) !== TRUE) {
        echo "Some error occurred while adding monogram details. Please try again later.";
        exit();
    }
    
    if (!$conn -> commit()) {
        echo "Some error occurred while adding monogram details. Please try again later.";
        exit();
    }else{
        echo "1";
    }

    $conn->close();
?>
