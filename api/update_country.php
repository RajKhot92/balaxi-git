<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input     */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
	$country_id = mysqli_real_escape_string($conn, $_REQUEST['country_id']);
    $country_name = mysqli_real_escape_string($conn, $_REQUEST['country_name']);

    $conn -> autocommit(FALSE);

    if($login_id === "" || $country_id === "" || $country_name === ""){
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
                    WHERE country_id = ".$country_id."
                    AND del_by IS NULL";
    $country_response = mysqli_query($conn, $country_exist);
    if (mysqli_num_rows($country_response) == 0){
        echo "Country details not found for country id ".$country_id." in the system.";
        exit();
    }

    /*  Checking country name already exists     */
    $country_name_exist = "SELECT country_id FROM country_master 
                        WHERE country_id != ".$country_id." 
                        AND UPPER(country_name) = UPPER('".$country_name."')
                        AND del_by IS NULL";
    $country_name_response = mysqli_query($conn, $country_name_exist);
    if (mysqli_num_rows($country_name_response) > 0){
        echo "Entered country name is already exist in the system. Please try with other country name.";
        exit();
    }
    
    /*  Updating country name    */
    $update_country_sql = "UPDATE country_master 
                        SET country_name = UPPER('".$country_name."')
                        WHERE country_id = ".$country_id;

    if ($conn->query($update_country_sql) !== TRUE) {
        echo "Some error occurred while updating country. Please try again later.";
        exit();
    }
    
    if (!$conn -> commit()) {
        echo "Some error occurred while updating country. Please try again later.";
        exit();
    }else{
        echo "1";
    }

    $conn->close();
?>