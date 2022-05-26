<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input   */
    $username = mysqli_real_escape_string($conn, $_REQUEST['username']);
    $password = mysqli_real_escape_string($conn, $_REQUEST['password']);

    $response = new StdClass;

    if($username === "" || $password === ""){
        $response->status_code = 500;
        $response->data = null;
        $response->error = "Kindly provide valid input.";

        echo json_encode($response);
        // echo "Kindly provide valid input.";
        exit();
    }

    /*  Checking user login    */
    $user_exist = "SELECT a.`user_id`,a.`user_name`,a.`contact_no`,a.`email_id`,
                    a.`user_role` 
                    FROM user_master a INNER JOIN user_login b 
                    ON a.`user_id`=b.`user_id`
                    WHERE a.`email_id` = '".$username."' AND b.`password`=SHA1('".$username.$password."') AND del_by IS NULL";
    $user_response = mysqli_query($conn, $user_exist);
    if (mysqli_num_rows($user_response) == 0){
        // echo "Invalid login credentials.";
        $response->status_code = 500;
        $response->data = null;
        $response->error = "Invalid login credentials.";
        echo json_encode($response);
        exit();
    }

    $json_response = array();  
    
    while ($row = mysqli_fetch_array($user_response, MYSQLI_ASSOC)) {  
        $row_array['user_id'] = $row['user_id'];
        $row_array['user_name'] = $row['user_name'];
        $row_array['contact_no'] = $row['contact_no'];
        $row_array['email_id'] = $row['email_id'];
        $row_array['user_role'] = $row['user_role'];
        array_push($json_response,$row_array);
    }

    $response->status_code = 200;
    $response->data = $json_response;
    $response->error = null;

    echo json_encode($response); 
    mysqli_free_result($user_response);

    $conn->close();

?>