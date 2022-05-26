<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input     */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
	$product_id = mysqli_real_escape_string($conn, $_REQUEST['product_id']);
    $strength = mysqli_real_escape_string($conn, $_REQUEST['strength']);
    $description = mysqli_real_escape_string($conn, $_REQUEST['description']);
    $composition = mysqli_real_escape_string($conn, $_REQUEST['composition']);
    $fill_weight = mysqli_real_escape_string($conn, $_REQUEST['fill_weight']);
    $pack_style = mysqli_real_escape_string($conn, $_REQUEST['pack_style']);
    $packaging_description = mysqli_real_escape_string($conn, $_REQUEST['packaging_description']);

    $conn -> autocommit(FALSE);

    if($login_id === "" || $product_id === "" || $strength === "" || $description === "" || $composition === "" || $fill_weight === "" || $pack_style === "" || $packaging_description === ""){
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

    /*  Adding product details     */
    $add_product_details_sql = "INSERT INTO product_details (`product_id`, `strength`, `description`, `composition`, `fill_weight`, `pack_style`, `package_description`, `ent_by`, `ent_dt`)
                            VALUES (".$product_id.",'".$strength."','".$description."','".$composition."','".$fill_weight."','".$pack_style."','".$packaging_description."',".$login_id.",NOW())";

    if ($conn->query($add_product_details_sql) !== TRUE) {
        echo "Some error occurred while adding product details. Please try again later.";
        exit();
    }
    
    if (!$conn -> commit()) {
        echo "Some error occurred while adding product details. Please try again later.";
        exit();
    }else{
        echo "1";
    }

    $conn->close();
?>