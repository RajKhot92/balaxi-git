<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    include "update_dossier_development_stats.php";

    /*  Taking user input     */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
	$product_id = mysqli_real_escape_string($conn, $_REQUEST['product_id']);
    $artwork = mysqli_real_escape_string($conn, $_REQUEST['artwork']);
    $everything_complete = mysqli_real_escape_string($conn, $_REQUEST['everything_complete']);
    if (!empty($_FILES['check_list']['name'])) {
        if ($_FILES['check_list']['error'] != 0) {
            echo 'Something wrong with the file.';
            exit();
        }

        $file_name = $_FILES['check_list']['name'];
        $file_tmp = $_FILES['check_list']['tmp_name'];
        $file_content = addslashes(file_get_contents($_FILES['check_list']['tmp_name']));
    }
    
    $conn -> autocommit(FALSE);

    if($login_id === "" || $product_id === "" || $artwork === ""){
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

    $evrthng_cmplt = $everything_complete === "" ? "null" : "STR_TO_DATE('".$everything_complete."', '%m/%d/%Y')";

    /*  Adding artwork question     */
    $add_check_list_sql = "INSERT INTO product_dossier_check (`product_id`, `check_list`, `artwork`, `everything_complete`, `ent_by`, `ent_dt`)
                            VALUES (".$product_id.",'".$file_content."',STR_TO_DATE('".$artwork."', '%m/%d/%Y'),".$evrthng_cmplt.",".$login_id.",NOW())";

    if ($conn->query($add_check_list_sql) !== TRUE) {
        echo "Some error occurred while adding dossier check list details. Please try again later.";
        exit();
    }
    
    if (!$conn -> commit()) {
        echo "Some error occurred while adding dossier check list details. Please try again later.";
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