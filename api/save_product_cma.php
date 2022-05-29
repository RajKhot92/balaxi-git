<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input     */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
	$product_id = mysqli_real_escape_string($conn, $_REQUEST['product_id']);
    $validity = mysqli_real_escape_string($conn, $_REQUEST['validity']);
    $received_date = mysqli_real_escape_string($conn, $_REQUEST['received_date']);
    if (!empty($_FILES['cma']['name'])) {
        if ($_FILES['cma']['error'] != 0) {
            echo 'Something wrong with the file.';
            exit();
        }

        $file_name = $_FILES['cma']['name'];
        $file_tmp = $_FILES['cma']['tmp_name'];
        $file_content = addslashes(file_get_contents($_FILES['cma']['tmp_name']));
    }
    
    $conn -> autocommit(FALSE);

    if($login_id === "" || $product_id === "" || $validity === "" || $received_date === ""){
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
    $add_cma_sql = "INSERT INTO product_cma (`product_id`, `validity`, `received_date`, `cma`, `ent_by`, `ent_dt`)
                            VALUES (".$product_id.",STR_TO_DATE('".$validity."', '%m/%d/%Y'),STR_TO_DATE('".$received_date."', '%m/%d/%Y'),'".$file_content."',".$login_id.",NOW())";

    if ($conn->query($add_cma_sql) !== TRUE) {
        echo "Some error occurred while adding cma details. Please try again later.";
        exit();
    }
    
    if (!$conn -> commit()) {
        echo "Some error occurred while adding cma details. Please try again later.";
        exit();
    }else{
        echo "1";
    }

    $conn->close();
?>