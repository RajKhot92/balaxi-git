<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input     */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
	$user_name = mysqli_real_escape_string($conn, $_REQUEST['user_name']);
    $contact_no = mysqli_real_escape_string($conn, $_REQUEST['contact_no']);
    $email_id = mysqli_real_escape_string($conn, $_REQUEST['email_id']);
    $user_role = mysqli_real_escape_string($conn, $_REQUEST['user_role']);

    $SECRET_KEY = "balaxi123";

    $conn -> autocommit(FALSE);

    if($login_id === "" || $user_name === "" || $contact_no === "" || $email_id === "" || $user_role === ""){
        echo "Kindly provide valid input.";
        exit();
    }


    /*  Checking role access    */
    $admin_exist = "SELECT user_id FROM user_master 
                    WHERE user_id = ".$login_id." AND user_role IN (1,2)
                    AND del_by IS NULL";
    $admin_response = mysqli_query($conn, $admin_exist);
    if (mysqli_num_rows($admin_response) == 0){
        echo "Invalid access. User don't have permission for this operation.";
        exit();
    }

    /*  Checking user by name already exists     */
    $user_name_exist = "SELECT user_id FROM user_master 
                    WHERE UPPER(user_name) = UPPER('".$user_name."')
                    AND del_by IS NULL";
    $user_name_response = mysqli_query($conn, $user_name_exist);
    if (mysqli_num_rows($user_name_response) > 0){
        echo "Entered user name is already exist in the system. Please try with other user name.";
        exit();
    }

    /*  Checking user by contact already exists     */
    $contact_no_exist = "SELECT user_id FROM user_master 
                    WHERE UPPER(contact_no) = UPPER('".$contact_no."')
                    AND del_by IS NULL";
    $contact_no_response = mysqli_query($conn, $contact_no_exist);
    if (mysqli_num_rows($contact_no_response) > 0){
        echo "Entered contact number is already exist in the system. Please try with other contact number.";
        exit();
    }

    /*  Checking user by email already exists     */
    $email_exist = "SELECT user_id FROM user_master 
                    WHERE UPPER(email_id) = UPPER('".$email_id."')
                    AND del_by IS NULL";
    $email_response = mysqli_query($conn, $email_exist);
    if (mysqli_num_rows($email_response) > 0){
        echo "Entered email id is already exist in the system. Please try with other email id.";
        exit();
    }

    /*  Checking user role exists     */
    $role_exist = "SELECT role_id FROM user_roles 
                    WHERE role_id = ".$user_role."
                    AND del_by IS NULL";
    $role_response = mysqli_query($conn, $role_exist);
    if (mysqli_num_rows($role_response) == 0){
        echo "Entered user role is invalid. Please try with valid user role.";
        exit();
    }
    
    /*  Adding new user     */
    $new_user_id = 0;
    $user_id_result = mysqli_query($conn, "SELECT IFNULL(MAX(user_id),0)+1 AS new_user_id FROM user_master");
    while ($row_uid = mysqli_fetch_array($user_id_result, MYSQLI_ASSOC)) {
        $new_user_id = $row_uid["new_user_id"];
    }

    $add_user_sql = "INSERT INTO user_master (user_id,user_name,contact_no,email_id, user_role,ent_by,ent_dt,del_by,del_dt)
                    VALUES (".$new_user_id.",'".$user_name."','".$contact_no."','".$email_id."',".$user_role.",".$login_id.",CURDATE(),null,null)";

    $add_login_sql = "INSERT INTO user_login (user_id,password,otp,password_change)
                    VALUES (".$new_user_id.",SHA1('".$email_id.$SECRET_KEY."'),null,0)";

    if ($conn->query($add_user_sql) !== TRUE || $conn->query($add_login_sql) !== TRUE) {
        echo "Some error occurred while adding new user. Please try again later.";
        exit();
    }
    
    if (!$conn -> commit()) {
        echo "Some error occurred while adding new user. Please try again later.";
        exit();
    }else{
        echo "1";
    }

    $conn->close();
?>