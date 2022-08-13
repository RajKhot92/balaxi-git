<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    include "update_submission_stats.php";

    /*  Taking user input     */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
	$product_id = mysqli_real_escape_string($conn, $_REQUEST['product_id']);
    $received_translator = mysqli_real_escape_string($conn, $_REQUEST['received_translator']);
    $file_type = mysqli_real_escape_string($conn, $_REQUEST['file_type']);
    if (!empty($_FILES['dossier_local']['name'])) {
        if ($_FILES['dossier_local']['error'] != 0) {
            echo 'Something wrong with the file.';
            exit();
        }

        $file_name = $_FILES['dossier_local']['name'];
        $file_tmp = $_FILES['dossier_local']['tmp_name'];
    }
    
    $conn -> autocommit(FALSE);

    if($login_id === "" || $product_id === "" || $received_translator === ""){
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

    /*  Adding product translation     */
    $new_translation_received_id = 0;
    $translation_received_id_result = mysqli_query($conn, "SELECT IFNULL(MAX(ptr_id),0)+1 AS new_translation_received_id FROM product_translation_received");
    while ($row_trid = mysqli_fetch_array($translation_received_id_result, MYSQLI_ASSOC)) {
        $new_translation_received_id = $row_trid["new_translation_received_id"];
    }
    
    $target_dir = "translation-received/";
    $target_file = $target_dir . $new_translation_received_id . "-" . basename($_FILES["dossier_local"]["name"]);
    if(move_uploaded_file($_FILES["dossier_local"]["tmp_name"], $target_file)){
        //File successfully uploaded
    }else{
        echo "Some error occurred while uploading file.";
        exit();
    }

    $add_translation_received_sql = "INSERT INTO product_translation_received (`ptr_id`, `product_id`, `received_translator`, `file_type`, `file_url`, `ent_by`, `ent_dt`)
                            VALUES (".$new_translation_received_id.",".$product_id.",STR_TO_DATE('".$received_translator."', '%m/%d/%Y'),'".$file_type."','".$target_file."',".$login_id.",NOW())";

    if ($conn->query($add_translation_received_sql) !== TRUE) {
        echo "Some error occurred while adding translation received details. Please try again later.";
        exit();
    }
    
    if (!$conn -> commit()) {
        echo "Some error occurred while adding translation received details. Please try again later.";
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