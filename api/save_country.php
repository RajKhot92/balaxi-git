<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input     */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
	$country_name = mysqli_real_escape_string($conn, $_REQUEST['country_name']);

    $conn -> autocommit(FALSE);

    if($login_id === "" || $country_name === ""){
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

    /*  Checking country already exists     */
    $country_exist = "SELECT country_id FROM country_master 
                    WHERE UPPER(country_name) = UPPER('".$country_name."')
                    AND del_by IS NULL";
    $country_response = mysqli_query($conn, $country_exist);
    if (mysqli_num_rows($country_response) > 0){
        echo "Entered country name is already exist in the system. Please try with other country name.";
        exit();
    }
    
    /*  Adding new country     */
    $add_country_sql = "INSERT INTO country_master (country_name,ent_by,ent_dt,del_by,del_dt)
                        VALUES (UPPER('".$country_name."'),".$login_id.",CURDATE(),null,null)";

    if ($conn->query($add_country_sql) !== TRUE) {
        echo "Some error occurred while adding new country. Please try again later.";
        exit();
    }
    
    if (!$conn -> commit()) {
        echo "Some error occurred while adding new country. Please try again later.";
        exit();
    }else{
        echo "1";
    }

    $conn->close();
?>