<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input     */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
	$category_id = mysqli_real_escape_string($conn, $_REQUEST['category_id']);
    $category_name = mysqli_real_escape_string($conn, $_REQUEST['category_name']);

    $conn -> autocommit(FALSE);

    if($login_id === "" || $category_id === "" || $category_name === ""){
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

    /*  Checking category already exists     */
    $category_exist = "SELECT category_id FROM product_category 
                    WHERE category_id = ".$category_id."
                    AND del_by IS NULL";
    $category_response = mysqli_query($conn, $category_exist);
    if (mysqli_num_rows($category_response) == 0){
        echo "Product category details not found for category id ".$category_id." in the system.";
        exit();
    }

    /*  Checking category name already exists     */
    $category_name_exist = "SELECT category_id FROM product_category 
                        WHERE category_id != ".$category_id." 
                        AND UPPER(category_name) = UPPER('".$category_name."')
                        AND del_by IS NULL";
    $category_name_response = mysqli_query($conn, $category_name_exist);
    if (mysqli_num_rows($category_name_response) > 0){
        echo "Entered category name is already exist in the system. Please try with other category name.";
        exit();
    }
    
    /*  Updating category name    */
    $update_category_sql = "UPDATE product_category 
                        SET category_name = UPPER('".$category_name."')
                        WHERE category_id = ".$category_id;

    if ($conn->query($update_category_sql) !== TRUE) {
        echo "Some error occurred while updating product category. Please try again later.";
        exit();
    }
    
    if (!$conn -> commit()) {
        echo "Some error occurred while updating product category. Please try again later.";
        exit();
    }else{
        echo "1";
    }

    $conn->close();
?>