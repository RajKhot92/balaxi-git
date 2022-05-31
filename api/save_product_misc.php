<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input     */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
	$product_id = mysqli_real_escape_string($conn, $_REQUEST['product_id']);
    $pckg_specification = mysqli_real_escape_string($conn, $_REQUEST['pckg_specification']);
    $warnings = mysqli_real_escape_string($conn, $_REQUEST['warnings']);
    $remarks = mysqli_real_escape_string($conn, $_REQUEST['remarks']);
    if (!empty($_FILES['manual']['name'])) {
        if ($_FILES['manual']['error'] != 0) {
            echo 'Something wrong with the file.';
            exit();
        }

        $file_name = $_FILES['manual']['name'];
        $file_tmp = $_FILES['manual']['tmp_name'];
        $file_content = addslashes(file_get_contents($_FILES['manual']['tmp_name']));
    }
    
    $conn -> autocommit(FALSE);

    if($login_id === "" || $product_id === "" || $pckg_specification === "" || $warnings === "" || $remarks === ""){
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

    /*  Adding license manufacture     */
    $add_misc_sql = "INSERT INTO product_misc (`product_id`, `pckg_specification`, `warnings`, `manual`, `remarks`, `ent_by`, `ent_dt`)
                            VALUES (".$product_id.",'".$pckg_specification."','".$warnings."','".$file_content."','".$remarks."',".$login_id.",NOW())";

    if ($conn->query($add_misc_sql) !== TRUE) {
        echo "Some error occurred while adding misc details. Please try again later.";
        exit();
    }
    
    if (!$conn -> commit()) {
        echo "Some error occurred while adding misc details. Please try again later.";
        exit();
    }else{
        echo "1";
    }

    $conn->close();
?>  