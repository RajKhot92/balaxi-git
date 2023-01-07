<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    include "send_email.php";

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

    $product_id_arr = array();
    $menu_id_arr = array();
    $user_id_arr = array();
    if (strpos($product_id_data, ',') !== false) { 
        $product_id_arr = explode(",", $product_id_data);    
    }else{
        array_push($product_id_arr,$product_id_data);
    }
    if (strpos($menu_id_data, ',') !== false) { 
        $menu_id_arr = explode(",", $menu_id_data);    
    }else{
        array_push($menu_id_arr,$menu_id_data);
    }
    if (strpos($user_id_data, ',') !== false) { 
        $user_id_arr = explode(",", $user_id_data);    
    }else{
        array_push($user_id_arr,$user_id_data);
    }

    $product_name = array();
    $product_id_selected = 0;
    // $isError = false;
    for ($i = 0; $i < count($product_id_arr); $i++) {
        /*  Checking product by product id already exists     */
        $product_exist = "SELECT product_id,product_name FROM product_master 
                        WHERE product_id = ".$product_id_arr[$i]."
                        AND del_by IS NULL";
        $product_response = mysqli_query($conn, $product_exist);
        if (mysqli_num_rows($product_response) == 0){
            echo "Product details not found for given product id ".$product_id_arr[$i].".";
            exit();
        }else{
            while ($row_product = mysqli_fetch_array($product_response, MYSQLI_ASSOC)) {
                array_push($product_name, $row_product['product_name']);
            }
        }

        $product_id_selected = $product_id_arr[$i];
    }

    $user_step = array();
    for ($i = 0; $i < count($menu_id_arr); $i++) {
        /*  Checking menu by menu id already exists     */
        $menu_exist = "SELECT menu_id,menu_name FROM menu_master 
                        WHERE menu_id = ".$menu_id_arr[$i];
        $menu_response = mysqli_query($conn, $menu_exist);
        if (mysqli_num_rows($menu_response) == 0){
            echo "Menu details not found for given menu id ".$menu_id_arr[$i].".";
            exit();
        }else{
            while ($row_menu = mysqli_fetch_array($menu_response, MYSQLI_ASSOC)) {
                array_push($user_step, $row_menu['menu_name']);
            }
        }
    }

    /*  Sending email to users  */
    $user_mail = array();
    $user_name = array();

    for ($i = 0; $i < count($user_id_arr); $i++) {
        /*  Checking user by user id already exists     */
        $user_exist = "SELECT `user_id`,`user_name`,`email_id` FROM user_master 
                        WHERE user_id = ".$user_id_arr[$i]."
                        AND del_by IS NULL";
        $user_response = mysqli_query($conn, $user_exist);
        if (mysqli_num_rows($user_response) == 0){
            echo "User details not found for given user id ".$user_id_arr[$i].".";
            exit();
        }else{
            while ($row_user = mysqli_fetch_array($user_response, MYSQLI_ASSOC)) {
                array_push($user_mail, $row_user['email_id']);
                array_push($user_name, $row_user['user_name']);
            }
        }
    }

    $delete_step_sql = "DELETE FROM product_step_master WHERE product_id=".$product_id_selected." AND ent_by=".$login_id." AND (progress IS NULL OR progress = 0)";
    
    if ($conn->query($delete_step_sql) !== TRUE) {
        echo "Some error occurred while assigning product steps. Please try again later.";
        exit();
    }

    /*  Assigning steps     */
    $assign_step_sql = "INSERT INTO product_step_master (product_id,menu_id,user_id,ent_by,ent_dt,updt_by,updt_dt)
                    VALUES ";
    if(count($user_id_arr) == 1){
        $assign_step_sql .= "(".$product_id_arr[0].",".$menu_id_arr[0].",".$user_id_arr[0].",".$login_id.",CURDATE(),null,null)";
    }else if(count($user_id_arr) > 1){
        for ($i = 0; $i < count($user_id_arr); $i++) {
            if($i == count($user_id_arr)-1){
                $assign_step_sql .= "(".$product_id_arr[$i].",".$menu_id_arr[$i].",".$user_id_arr[$i].",".$login_id.",CURDATE(),null,null)";
            }else{
                $assign_step_sql .= "(".$product_id_arr[$i].",".$menu_id_arr[$i].",".$user_id_arr[$i].",".$login_id.",CURDATE(),null,null),";
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
        $counter = 0;
        $cc_mail = array('regulatory@balaxi.in','sandeep@balaxi.com');
        for ($i = 0; $i < count($user_mail); $i++) {
            $mail_to = $user_mail[$i];
            $subject = 'Product step assigned ';
            $content = 'Hello '.$user_name[$i].',<br/><br/>
                        Your product owner has assigned <b>'.$user_step[$i].'</b> step to you for the product <b>'.$product_name[$i].'</b>.<br/><br/>
                        Please do not reply to this mail.<br/><br/>
                        Thanks,<br/>
                        Team Balaxi';
            $mail_retval = sendEmail($mail_to,$cc_mail,$subject,$content);
            if($mail_retval == "1" || $mail_retval == 1){
                $counter++;
            }
        }
        $counter = count($user_mail);
        if($counter != count($user_mail)){
            echo "Some error occurred while sending mail to users.";
            exit();
        }else{
            echo "1";
        }
    }

    $conn->close();
?>