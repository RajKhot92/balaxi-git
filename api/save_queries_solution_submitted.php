<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    include "update_document_sample_collection_stats.php";

    /*  Taking user input     */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
	$product_id = mysqli_real_escape_string($conn, $_REQUEST['product_id']);
    $submitted_dt = mysqli_real_escape_string($conn, $_REQUEST['submitted_dt']);
    $slip_no = mysqli_real_escape_string($conn, $_REQUEST['slip_no']);
    $remarks = mysqli_real_escape_string($conn, $_REQUEST['remarks']);
    
    $conn -> autocommit(FALSE);

    if($login_id === "" || $product_id === "" || $submitted_dt === "" || $slip_no === "" || $remarks === ""){
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

    /*  Adding market research     */
    $add_solution_sql = "INSERT INTO product_queries_submitted (`product_id`, `submitted_dt`, `slip_no`, `remarks`, `ent_by`, `ent_dt`)
                            VALUES (".$product_id.",STR_TO_DATE('".$submitted_dt."', '%m/%d/%Y'),'".$slip_no."','".$remarks."',".$login_id.",NOW())";

    if ($conn->query($add_solution_sql) !== TRUE) {
        echo "Some error occurred while adding solution submitted details. Please try again later.";
        exit();
    }
    
    if (!$conn -> commit()) {
        echo "Some error occurred while adding solution submitted details. Please try again later.";
        exit();
    }else{
        echo "1";
    }

    $conn->close();
?>