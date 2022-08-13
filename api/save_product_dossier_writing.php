<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    include "update_dossier_development_stats.php";

    /*  Taking user input     */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
	$product_id = mysqli_real_escape_string($conn, $_REQUEST['product_id']);
    $everything_complete = mysqli_real_escape_string($conn, $_REQUEST['everything_complete']);
    $file_type = mysqli_real_escape_string($conn, $_REQUEST['file_type']);
    if (!empty($_FILES['dossier']['name'])) {
        if ($_FILES['dossier']['error'] != 0) {
            echo 'Something wrong with the file.';
            exit();
        }

        $file_name = $_FILES['dossier']['name'];
        $file_tmp = $_FILES['dossier']['tmp_name'];
    }
    
    $conn -> autocommit(FALSE);

    if($login_id === "" || $product_id === "" || $everything_complete === ""){
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
    $new_dossier_id = 0;
    $dossier_id_result = mysqli_query($conn, "SELECT IFNULL(MAX(dw_id),0)+1 AS new_dossier_id FROM product_dossier_writing");
    while ($row_did = mysqli_fetch_array($dossier_id_result, MYSQLI_ASSOC)) {
        $new_dossier_id = $row_did["new_dossier_id"];
    }
    
    $target_dir = "dossier/";
    $target_file = $target_dir . $new_dossier_id . "-" . basename($_FILES["dossier"]["name"]);
    if(move_uploaded_file($_FILES["dossier"]["tmp_name"], $target_file)){
        //File successfully uploaded
    }else{
        echo "Some error occurred while uploading file.";
        exit();
    }

    $add_writing_sql = "INSERT INTO product_dossier_writing (`dw_id`, `product_id`, `file_type`, `file_url`, `everything_complete`, `ent_by`, `ent_dt`)
                            VALUES (".$new_dossier_id.",".$product_id.",'".$file_type."','".$target_file."',STR_TO_DATE('".$everything_complete."', '%m/%d/%Y'),".$login_id.",NOW())";

    if ($conn->query($add_writing_sql) !== TRUE) {
        echo "Some error occurred while adding dossier writing details. Please try again later.";
        exit();
    }
    
    if (!$conn -> commit()) {
        echo "Some error occurred while adding dossier writing details. Please try again later.";
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
