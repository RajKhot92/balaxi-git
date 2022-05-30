<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input     */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
	$product_id_data = mysqli_real_escape_string($conn, $_REQUEST['product_id']);
    $menu_id_data = mysqli_real_escape_string($conn, $_REQUEST['menu_id']);
    $user_id_data = mysqli_real_escape_string($conn, $_REQUEST['user_id']);

    $conn -> autocommit(FALSE);

    if($login_id === "" || $product_id_data === "" || $menu_id_data === "" || $user_id_data === ""){
        echo "Kindly provide valid input.";
        exit();
    }

    /*  Checking role access    */
    $admin_exist = "SELECT user_id FROM user_master 
                    WHERE user_id = ".$login_id." AND user_role IN (1,2,3)
                    AND del_by IS NULL";
    $admin_response = mysqli_query($conn, $admin_exist);
    if (mysqli_num_rows($admin_response) == 0){
        echo "Invalid access. User don't have permission for this operation.";
        exit();
    }

    $product_id_arr = explode(",", $product_id_data);
    $menu_id_arr = explode(",", $menu_id_data);
    $user_id_arr = explode(",", $user_id_data);

    $product_id_selected = 0;
    // $isError = false;
    for ($i = 0; $i < count($product_id_arr); $i++) {
        /*  Checking product by product id already exists     */
        $product_exist = "SELECT product_id FROM product_master 
                        WHERE product_id = ".$product_id_arr[$i]."
                        AND del_by IS NULL";
        $product_response = mysqli_query($conn, $product_exist);
        if (mysqli_num_rows($product_response) == 0){
            echo "Product details not found for given product id ".$product_id_arr[$i].".";
            exit();
        }

        $product_id_selected = $product_id_arr[$i];
    }

    for ($i = 0; $i < count($menu_id_arr); $i++) {
        /*  Checking menu by menu id already exists     */
        $menu_exist = "SELECT menu_id FROM menu_master 
                        WHERE menu_id = ".$menu_id_arr[$i];
        $menu_response = mysqli_query($conn, $menu_exist);
        if (mysqli_num_rows($menu_response) == 0){
            echo "Menu details not found for given menu id ".$menu_id_arr[$i].".";
            exit();
        }
    }

    for ($i = 0; $i < count($user_id_arr); $i++) {
        /*  Checking user by user id already exists     */
        $user_exist = "SELECT user_id FROM user_master 
                        WHERE user_id = ".$user_id_arr[$i]."
                        AND del_by IS NULL";
        $user_response = mysqli_query($conn, $user_exist);
        if (mysqli_num_rows($user_response) == 0){
            echo "User details not found for given user id ".$user_id_arr[$i].".";
            exit();
        }
    }

    $delete_step_sql = "DELETE FROM product_step_master WHERE product_id=".$product_id_selected." AND ent_by=".$login_id;
    
    if ($conn->query($delete_step_sql) !== TRUE) {
        echo "Some error occurred while assigning product steps. Please try again later.";
        exit();
    }

    /*  Assigning steps     */
    $assign_step_sql = "INSERT INTO product_step_master (product_id,menu_id,user_id,ent_by,ent_dt)
                    VALUES ";
    if(count($user_id_arr) == 1){
        $assign_step_sql .= "(".$product_id_arr[$i].",".$menu_id_arr[$i].",".$user_id_arr[$i].",".$login_id.",CURDATE())";
    }else if(count($user_id_arr) > 1){
        for ($i = 0; $i < count($user_id_arr); $i++) {
            if($i == count($user_id_arr)-1){
                $assign_step_sql .= "(".$product_id_arr[$i].",".$menu_id_arr[$i].",".$user_id_arr[$i].",".$login_id.",CURDATE())";
            }else{
                $assign_step_sql .= "(".$product_id_arr[$i].",".$menu_id_arr[$i].",".$user_id_arr[$i].",".$login_id.",CURDATE()),";
            }
        }
    }
    
    if ($conn->query($assign_step_sql) !== TRUE) {
        echo "Some error occurred while assigning product steps. Please try again later.";
        exit();
    }
    
    if (!$conn -> commit()) {
        echo "Some error occurred while assigning product steps. Please try again later.";
        exit();
    }else{
        echo "1";
    }

    $conn->close();
?>