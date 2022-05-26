<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input     */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
	$category_name = mysqli_real_escape_string($conn, $_REQUEST['category_name']);

    $conn -> autocommit(FALSE);

    if($login_id === "" || $category_name === ""){
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
                    WHERE UPPER(category_name) = UPPER('".$category_name."')
                    AND del_by IS NULL";
    $category_response = mysqli_query($conn, $category_exist);
    if (mysqli_num_rows($category_response) > 0){
        echo "Entered category name is already exist in the system. Please try with other category name.";
        exit();
    }
    
    /*  Adding new category     */
    $add_category_sql = "INSERT INTO product_category (category_name,ent_by,ent_dt,del_by,del_dt)
                        VALUES (UPPER('".$category_name."'),".$login_id.",CURDATE(),null,null)";

    if ($conn->query($add_category_sql) !== TRUE) {
        echo "Some error occurred while adding new product category. Please try again later.";
        exit();
    }
    
    if (!$conn -> commit()) {
        echo "Some error occurred while adding new product category. Please try again later.";
        exit();
    }else{
        echo "1";
    }

    $conn->close();
?>