<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input     */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
	$product_name = mysqli_real_escape_string($conn, $_REQUEST['product_name']);
    $product_category = mysqli_real_escape_string($conn, $_REQUEST['product_category']);
    $product_owner = mysqli_real_escape_string($conn, $_REQUEST['product_owner']);
    $country_id = mysqli_real_escape_string($conn, $_REQUEST['country_id']);
    $deadline_dt = mysqli_real_escape_string($conn, $_REQUEST['deadline_dt']);

    $conn -> autocommit(FALSE);

    if($login_id === "" || $product_name === "" || $product_category === "" || $product_owner === "" || $country_id === "" || $deadline_dt == ""){
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

    /*  Checking product category exists     */
    $category_exist = "SELECT category_id FROM product_category 
                    WHERE category_id = ".$product_category."
                    AND del_by IS NULL";
    $category_response = mysqli_query($conn, $category_exist);
    if (mysqli_num_rows($category_response) == 0){
        echo "Entered product category is invalid. Please try with valid product category.";
        exit();
    }

    /*  Checking product owner exists     */
    $owner_exist = "SELECT user_id FROM user_master 
                    WHERE user_id = ".$product_owner."
                    AND del_by IS NULL";
    $owner_response = mysqli_query($conn, $owner_exist);
    if (mysqli_num_rows($owner_response) == 0){
        echo "Entered product owner is invalid. Please try with valid product owner.";
        exit();
    }

    /*  Checking country exists     */
    $country_exist = "SELECT country_id FROM country_master 
                    WHERE country_id = ".$country_id."
                    AND del_by IS NULL";
    $country_response = mysqli_query($conn, $country_exist);
    if (mysqli_num_rows($country_response) == 0){
        echo "Entered product country is invalid. Please try with valid product country.";
        exit();
    }

    /*  Checking product already exists     */
    $product_exist = "SELECT product_id FROM product_master 
                    WHERE UPPER(product_name) = UPPER('".$product_name."')
                    AND product_category = ".$product_category."
                    AND product_owner = ".$product_owner."
                    AND country_id = ".$country_id."
                    AND del_by IS NULL";
    $product_response = mysqli_query($conn, $product_exist);
    if (mysqli_num_rows($product_response) > 0){
        echo "Entered product is already exist in the system. Please try with other product.";
        exit();
    }
    
    /*  Adding new product     */
    $add_product_sql = "INSERT INTO product_master (product_name,product_category,product_owner, country_id,deadline_dt,ent_by,ent_dt,del_by,del_dt)
                    VALUES ('".$product_name."',".$product_category.",".$product_owner.",".$country_id.",STR_TO_DATE('".$deadline_dt."', '%m/%d/%Y'),".$login_id.",NOW(),null,null)";

    if ($conn->query($add_product_sql) !== TRUE) {
        echo "Some error occurred while adding new product. Please try again later.";
        exit();
    }
    
    if (!$conn -> commit()) {
        echo "Some error occurred while adding new product. Please try again later.";
        exit();
    }else{
        echo "1";
    }

    $conn->close();
?>