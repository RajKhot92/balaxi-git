<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    include "send_email.php";
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

    $product_name = '';
    $product_exist = "SELECT product_id,product_name FROM product_master 
                WHERE product_id = ".$product_id."
                AND del_by IS NULL";
    $product_response = mysqli_query($conn, $product_exist);
    if (mysqli_num_rows($product_response) == 0){
        echo "Product details not found for product id ".$product_id." in the system.";
        exit();
    }else{
        while ($row = mysqli_fetch_array($product_response, MYSQLI_ASSOC)) {
            $product_name = $row['product_name'];
        }
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

        $cc_mail = array();

        $product_owner_mail = '';
        $product_owner_name = '';
        $owner_exist = "SELECT b.`user_name`,b.`email_id` 
                        FROM `product_master` a INNER JOIN `user_master` b 
                        ON a.`product_owner` = b.`user_id` 
                        WHERE `product_id`=".$product_id." 
                        AND a.`del_by` IS NULL AND b.`del_by` IS NULL";
        $owner_response = mysqli_query($conn, $owner_exist);
        if (mysqli_num_rows($owner_response) == 0){
            echo "Product owner details not found for current product.";
            exit();
        }else{
            while ($row = mysqli_fetch_array($owner_response, MYSQLI_ASSOC)) {
                $product_owner_name = $row['user_name'];
                $product_owner_mail = $row['email_id'];
            }
        }

        /*  All Admin Email  */
        $admin_exist = "SELECT DISTINCT `email_id`
                        FROM `user_master` 
                        WHERE `user_role` = 2 AND `del_by` IS NULL";
        $admin_response = mysqli_query($conn, $admin_exist);
        if (mysqli_num_rows($admin_response) == 0){
            //Skipping Admin Emails
        }else{
            while ($row = mysqli_fetch_array($admin_response, MYSQLI_ASSOC)) {
                array_push($cc_mail, $row['email_id']);
            }
        }
        // array_push($cc_mail, 'regulatory@balaxi.in','sandeep@balaxi.com')
        // $mail_to = $product_owner_mail;
        // $subject = 'Query Received ';
        // $content = 'Hello '.$product_owner_name.',<br/><br/>
        //             We have received queries against product '.$product_name.'. It requires your attention. Kindly provide your solution to these queries by visiting portal.<br/><br/>
        //             Please do not reply to this mail.<br/><br/>
        //             Thanks,<br/>
        //             Team Balaxi';
        // $retval = sendEmail($mail_to,$cc_mail,$subject,$content);
        $retval = "1";
        if($retval == "0"){
            echo "Some error occurred while sending mail to product owner. Please try again later.";
            exit();
        }else{
            echo "1";
        }
    }

    $conn->close();
?>  
