<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input     */
    $login_id = mysqli_real_escape_string($conn, $_REQUEST['login_id']);
	$therapeutic_id = mysqli_real_escape_string($conn, $_REQUEST['therapeutic_id']);
    $therapeutic_class = mysqli_real_escape_string($conn, $_REQUEST['therapeutic_class']);

    $conn -> autocommit(FALSE);

    if($login_id === "" || $therapeutic_id === "" || $therapeutic_class === ""){
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

    /*  Checking therapeutic class already exists     */
    $therapeutic_exist = "SELECT therapeutic_id FROM therapeutic_class_master 
                    WHERE therapeutic_id = ".$therapeutic_id."
                    AND del_by IS NULL";
    $therapeutic_response = mysqli_query($conn, $therapeutic_exist);
    if (mysqli_num_rows($therapeutic_response) == 0){
        echo "Theapeutic class details not found for therapeutic id ".$therapeutic_id." in the system.";
        exit();
    }

    /*  Checking therapeutic class already exists     */
    $therapeutic_class_exist = "SELECT therapeutic_id FROM therapeutic_class_master 
                        WHERE therapeutic_id != ".$therapeutic_id." 
                        AND UPPER(therapeutic_class) = UPPER('".$therapeutic_class."')
                        AND del_by IS NULL";
    $therapeutic_class_response = mysqli_query($conn, $therapeutic_class_exist);
    if (mysqli_num_rows($therapeutic_class_response) > 0){
        echo "Entered therapeutic class is already exist in the system. Please try with other therapeutic class.";
        exit();
    }
    
    /*  Updating therapeutic class    */
    $update_therapeutic_sql = "UPDATE therapeutic_class_master 
                        SET therapeutic_class = UPPER('".$therapeutic_class."')
                        WHERE therapeutic_id = ".$therapeutic_id;

    if ($conn->query($update_therapeutic_sql) !== TRUE) {
        echo "Some error occurred while updating therapeutic class. Please try again later.";
        exit();
    }
    
    if (!$conn -> commit()) {
        echo "Some error occurred while updating therapeutic class. Please try again later.";
        exit();
    }else{
        echo "1";
    }

    $conn->close();
?>