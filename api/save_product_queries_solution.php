<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input     */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
	$product_id = mysqli_real_escape_string($conn, $_REQUEST['product_id']);
    $psq_id = mysqli_real_escape_string($conn, $_REQUEST['psq_id']);
    $solution_desc = mysqli_real_escape_string($conn, $_REQUEST['solution_desc']);
    $file_type = mysqli_real_escape_string($conn, $_REQUEST['file_type']);
    if (!empty($_FILES['solution_file']['name'])) {
        if ($_FILES['solution_file']['error'] != 0) {
            echo 'Something wrong with the file.';
            exit();
        }

        $file_name = $_FILES['solution_file']['name'];
        $file_tmp = $_FILES['solution_file']['tmp_name'];
        $file_content = addslashes(file_get_contents($_FILES['solution_file']['tmp_name']));
    }
    
    $conn -> autocommit(FALSE);

    if($login_id === "" || $product_id === "" || $psq_id === "" || $solution_desc === ""){
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
    $new_solution_id = 0;
    $solution_id_result = mysqli_query($conn, "SELECT IFNULL(MAX(pqs_id),0)+1 AS new_solution_id FROM product_queries_solution");
    while ($row_sid = mysqli_fetch_array($solution_id_result, MYSQLI_ASSOC)) {
        $new_solution_id = $row_sid["new_solution_id"];
    }
    
    $target_dir = "query-solution/";
    $target_file = $target_dir . $new_solution_id . "-" . basename($_FILES["solution_file"]["name"]);
    if(move_uploaded_file($_FILES["solution_file"]["tmp_name"], $target_file)){
        //File successfully uploaded
    }else{
        echo "Some error occurred while uploading file.";
        exit();
    }

    $add_solution_sql = "INSERT INTO product_queries_solution (`pqs_id`, `product_id`, `psq_id`, `solution_desc`, `file_url`, `file_type`, `ent_by`, `ent_dt`)
                            VALUES (".$new_solution_id.",".$product_id.",".$psq_id.",'".$solution_desc."','".$target_file."','".$file_type."',".$login_id.",NOW())";

    if ($conn->query($add_solution_sql) !== TRUE) {
        echo "Some error occurred while adding queries solution details. Please try again later.";
        exit();
    }
    
    if (!$conn -> commit()) {
        echo "Some error occurred while adding queries solution details. Please try again later.";
        exit();
    }else{
        echo "1";
    }

    $conn->close();
?>