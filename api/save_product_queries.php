<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input     */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
	$product_id = mysqli_real_escape_string($conn, $_REQUEST['product_id']);
    $received_date = mysqli_real_escape_string($conn, $_REQUEST['received_date']);
    $query_no = mysqli_real_escape_string($conn, $_REQUEST['query_no']);
    $desc_box = mysqli_real_escape_string($conn, $_REQUEST['desc_box']);
    $query_category = mysqli_real_escape_string($conn, $_REQUEST['query_category']);
    $deadline_dt = mysqli_real_escape_string($conn, $_REQUEST['deadline_dt']);
    
    if (!empty($_FILES['queries_file']['name'])) {
        if ($_FILES['queries_file']['error'] != 0) {
            echo 'Something wrong with the file.';
            exit();
        }

        $file_name = $_FILES['queries_file']['name'];
        $file_tmp = $_FILES['queries_file']['tmp_name'];
        $file_content_queries = addslashes(file_get_contents($_FILES['queries_file']['tmp_name']));
    }
    
    $conn -> autocommit(FALSE);

    if($login_id === "" || $product_id === "" || $received_date === "" || $query_no === "" || $desc_box === "" || $query_category === "" || $deadline_dt === ""){
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

    /*  Adding product queries     */
    $add_queries_sql = "INSERT INTO product_queries_received (`product_id`, `received_date`, `query_no`, `queries_file`, `desc_box`, `query_category`, `deadline_dt`, `ent_by`, `ent_dt`)
                            VALUES (".$product_id.",STR_TO_DATE('".$received_date."', '%m/%d/%Y'),'".$query_no."','".$file_content_queries."','".$desc_box."',".$query_category.",STR_TO_DATE('".$deadline_dt."', '%m/%d/%Y'),".$login_id.",NOW())";

    if ($conn->query($add_queries_sql) !== TRUE) {
        echo "Some error occurred while adding query received details. Please try again later.";
        exit();
    }
    
    if (!$conn -> commit()) {
        echo "Some error occurred while adding query received details. Please try again later.";
        exit();
    }else{
        echo "1";
    }

    $conn->close();
?>  