<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    include "update_dossier_development_stats.php";

    /*  Taking user input     */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
	$product_id = mysqli_real_escape_string($conn, $_REQUEST['product_id']);
    $score = mysqli_real_escape_string($conn, $_REQUEST['score']);
    if (!empty($_FILES['marker_file']['name'])) {
        if ($_FILES['marker_file']['error'] != 0) {
            echo 'Something wrong with the file.';
            exit();
        }

        $file_name = $_FILES['marker_file']['name'];
        $file_tmp = $_FILES['marker_file']['tmp_name'];
        $file_content = addslashes(file_get_contents($_FILES['marker_file']['tmp_name']));
    }
    
    $conn -> autocommit(FALSE);

    if($login_id === "" || $product_id === "" || $score === ""){
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
    $add_validation_sql = "INSERT INTO product_dossier_valid_marker (`product_id`, `marker_file`, `score`, `ent_by`, `ent_dt`)
                            VALUES (".$product_id.",'".$file_content."','".$score."',".$login_id.",NOW())";

    /*  Adding Notification     */
    $add_notification_sql = "INSERT INTO notification_master (`product_id`, `notify_type`, `user_id`, `status`, `ent_by`, `ent_dt`) VALUES ";
    $admin_exist = "SELECT DISTINCT `user_id`
                    FROM `user_master` 
                    WHERE `user_role` IN (1,2) AND `del_by` IS NULL";
    $admin_response = mysqli_query($conn, $admin_exist);
    if (mysqli_num_rows($admin_response) == 0){
        //Skipping Admin Ids
    }else{
        while ($row = mysqli_fetch_array($admin_response, MYSQLI_ASSOC)) {
            $add_notification_sql .= "(".$product_id.",'Dossier',".$row['user_id'].",'No',".$login_id.",NOW()),";
        }
    }
    $add_notification_sql = rtrim($add_notification_sql,",");

    if ($conn->query($add_validation_sql) !== TRUE || $conn->query($add_notification_sql) !== TRUE) {
        echo "Some error occurred while adding dossier validation details. Please try again later.";
        exit();
    }
    
    if (!$conn -> commit()) {
        echo "Some error occurred while adding dossier validation details. Please try again later.";
        exit();
    }else{
        $retval = processStats($login_id,$product_id,$conn);
        if($retval == "0"){
            echo "Some error occurred while updating progress details. Please try again later.";
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