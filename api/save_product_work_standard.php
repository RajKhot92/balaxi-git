<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input     */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
	$product_id = mysqli_real_escape_string($conn, $_REQUEST['product_id']);
    $appearance = mysqli_real_escape_string($conn, $_REQUEST['appearance']);
    $validity = mysqli_real_escape_string($conn, $_REQUEST['validity']);
    $received_date = mysqli_real_escape_string($conn, $_REQUEST['received_date']);
    $remarks = mysqli_real_escape_string($conn, $_REQUEST['remarks']);
    
    $conn -> autocommit(FALSE);

    if($login_id === "" || $product_id === "" || $appearance === "" || $validity === "" || $received_date === "" || $remarks === ""){
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

    /*  Adding license manufacture     */
    $add_work_standard_sql = "INSERT INTO product_work_standard (`product_id`, `appearance`, `validity`, `received_date`, `remarks`, `ent_by`, `ent_dt`)
                            VALUES (".$product_id.",'".$appearance."',STR_TO_DATE('".$validity."', '%m/%d/%Y'), STR_TO_DATE('".$received_date."', '%m/%d/%Y'),'".$remarks."',".$login_id.",NOW())";

    if ($conn->query($add_work_standard_sql) !== TRUE) {
        echo "Some error occurred while adding working standard details. Please try again later.";
        exit();
    }
    
    if (!$conn -> commit()) {
        echo "Some error occurred while adding working standard details. Please try again later.";
        exit();
    }else{
        echo "1";
    }

    $conn->close();
?>  