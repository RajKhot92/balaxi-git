<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    include "update_document_sample_collection_stats.php";

    /*  Taking user input     */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
	$product_id = mysqli_real_escape_string($conn, $_REQUEST['product_id']);
    $strength = mysqli_real_escape_string($conn, $_REQUEST['strength']);
    $composition = mysqli_real_escape_string($conn, $_REQUEST['composition']);
    $pharmacopeia_type = mysqli_real_escape_string($conn, $_REQUEST['pharmacopeia_type']);
    $validity = mysqli_real_escape_string($conn, $_REQUEST['validity']);
    $received_date = mysqli_real_escape_string($conn, $_REQUEST['received_date']);
    $file_type = mysqli_real_escape_string($conn, $_REQUEST['file_type']);
    $file_content = "";
    if (!empty($_FILES['pp']['name'])) {
        if ($_FILES['pp']['error'] != 0) {
            echo 'Something wrong with the file.';
            exit();
        }

        $file_name = $_FILES['pp']['name'];
        $file_tmp = $_FILES['pp']['tmp_name'];
        $file_content = addslashes(file_get_contents($_FILES['pp']['tmp_name']));
    }
    
    $conn -> autocommit(FALSE);

    if($login_id === "" || $product_id === "" || $strength === ""){
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

    $vldt = $validity === "" ? "null" : "STR_TO_DATE('".$validity."', '%m/%d/%Y')";
    $rcvd_dt = $received_date === "" ? "null" : "STR_TO_DATE('".$received_date."', '%m/%d/%Y')";
    $fil_typ = $file_type === "" ? "null" : "'".$file_type."'";
    $fil_url = "null";

    /*  Adding market research     */
    if ($file_type != "") {
        $new_pp_id = 0;
        $pp_id_result = mysqli_query($conn, "SELECT IFNULL(MAX(pp_id),0)+1 AS new_pp_id FROM product_pp");
        while ($row_lmid = mysqli_fetch_array($pp_id_result, MYSQLI_ASSOC)) {
            $new_pp_id = $row_lmid["new_pp_id"];
        }
        
        $target_dir = "pp/";
        $target_file = $target_dir . $new_pp_id . "-" . basename($_FILES["pp"]["name"]);
        if(move_uploaded_file($_FILES["pp"]["tmp_name"], $target_file)){
            //File successfully uploaded
            $fil_url = "'".$target_file."'";
        }else{
            echo "Some error occurred while uploading file.";
            exit();
        }
    }

    $add_pp_sql = "INSERT INTO product_pp (`product_id`, `strength`, `composition`, `pharmacopeia_type`, `validity`, `received_date`, `file_type`, `file_url`, `ent_by`, `ent_dt`)
                            VALUES (".$product_id.",'".$strength."','".$composition."','".$pharmacopeia_type."',".$vldt.",".$rcvd_dt.",".$fil_typ.",".$fil_url.",".$login_id.",NOW())";

    if ($conn->query($add_pp_sql) !== TRUE) {
        echo "Some error occurred while adding pp details. Please try again later.";
        exit();
    }
    
    if (!$conn -> commit()) {
        echo "Some error occurred while adding pp details. Please try again later.";
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