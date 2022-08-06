<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";
    include "delete_product_substeps.php";

    /*  Taking user input     */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
	$product_id = mysqli_real_escape_string($conn, $_REQUEST['product_id']);

    $conn -> autocommit(FALSE);

    if($login_id === "" || $product_id === ""){
        echo "Kindly provide valid input.";
        exit();
    }

    /*  Checking role access    */
    $admin_exist = "SELECT user_id FROM user_master 
                    WHERE user_id = ".$login_id." AND user_role IN (1,2)
                    AND del_by IS NULL";
    $admin_response = mysqli_query($conn, $admin_exist);
    if (mysqli_num_rows($admin_response) == 0){
        echo "Invalid access. User don't have permission for this operation.";
        exit();
    }

    /*  Checking country already exists     */
    $product_exist = "SELECT product_id FROM product_master 
                    WHERE product_id = ".$product_id."
                    AND del_by IS NULL";
    $product_response = mysqli_query($conn, $product_exist);
    if (mysqli_num_rows($product_response) == 0){
        echo "Product details not found for product id ".$product_id." in the system.";
        exit();
    }
    
    /*  Deleting country   */
    $delete_product_sql = "UPDATE product_master 
                        SET del_by = ".$login_id.",del_dt = CURDATE()
                        WHERE product_id = ".$product_id;

    if ($conn->query($delete_product_sql) !== TRUE) {
        echo "Some error occurred while deleting product. Please try again later.";
        exit();
    }
    
    if (!$conn -> commit()) {
        echo "Some error occurred while deleting product. Please try again later.";
        exit();
    }else{
        $retval = deleteAllSteps($login_id,$product_id,$conn);
        if($retval == "0"){
            echo "Some error occurred while deleting all steps. Please try again later.";
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