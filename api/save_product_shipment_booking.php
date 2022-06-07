<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    include "update_shipment_stats.php";

    /*  Taking user input     */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
	$product_id = mysqli_real_escape_string($conn, $_REQUEST['product_id']);
    $dhl_approval = mysqli_real_escape_string($conn, $_REQUEST['dhl_approval']);
    $slot_booking = mysqli_real_escape_string($conn, $_REQUEST['slot_booking']);
    $pickup_date = mysqli_real_escape_string($conn, $_REQUEST['pickup_date']);
    if (!empty($_FILES['book_invoice']['name'])) {
        if ($_FILES['book_invoice']['error'] != 0) {
            echo 'Something wrong with the file.';
            exit();
        }

        $file_name = $_FILES['book_invoice']['name'];
        $file_tmp = $_FILES['book_invoice']['tmp_name'];
        $file_content = addslashes(file_get_contents($_FILES['book_invoice']['tmp_name']));
    }
    
    $conn -> autocommit(FALSE);

    if($login_id === "" || $product_id === "" || $dhl_approval === "" || $slot_booking === "" || $pickup_date === ""){
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

    /*  Adding shipment booking     */
    $add_shipment_booking_sql = "INSERT INTO product_shipment_booking (`product_id`, `dhl_approval`, `slot_booking`, `pick_up_date`, `book_invoice`, `ent_by`, `ent_dt`)
                            VALUES (".$product_id.",STR_TO_DATE('".$dhl_approval."', '%m/%d/%Y'),'".$slot_booking."',STR_TO_DATE('".$pickup_date."', '%m/%d/%Y'),'".$file_content."',".$login_id.",NOW())";

    if ($conn->query($add_shipment_booking_sql) !== TRUE) {
        echo "Some error occurred while adding shipment booking details. Please try again later.";
        exit();
    }
    
    if (!$conn -> commit()) {
        echo "Some error occurred while adding shipment booking details. Please try again later.";
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