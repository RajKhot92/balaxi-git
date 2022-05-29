<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input     */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
	$product_id = mysqli_real_escape_string($conn, $_REQUEST['product_id']);
    $is_research = mysqli_real_escape_string($conn, $_REQUEST['is_research']);
    $conducted_on = mysqli_real_escape_string($conn, $_REQUEST['conducted_on']);
    if (!empty($_FILES['research_report']['name'])) {
        if ($_FILES['research_report']['error'] != 0) {
            echo 'Something wrong with the file.';
            exit();
        }

        $file_name = $_FILES['research_report']['name'];
        $file_tmp = $_FILES['research_report']['tmp_name'];
        $file_content = addslashes(file_get_contents($_FILES['research_report']['tmp_name']));
    }
    
    $conn -> autocommit(FALSE);

    if($login_id === "" || $product_id === "" || $is_research === "" || $conducted_on === ""){
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
    $add_market_research_sql = "INSERT INTO product_market_research (`product_id`, `research_complete`, `conducted_on`, `research_report`, `ent_by`, `ent_dt`)
                            VALUES (".$product_id.",'".$is_research."',STR_TO_DATE('".$conducted_on."', '%m/%d/%Y'),'".$file_content."',".$login_id.",NOW())";

    if ($conn->query($add_market_research_sql) !== TRUE) {
        echo "Some error occurred while adding market research details. Please try again later.";
        exit();
    }
    
    if (!$conn -> commit()) {
        echo "Some error occurred while adding market research details. Please try again later.";
        exit();
    }else{
        echo "1";
    }

    $conn->close();
?>