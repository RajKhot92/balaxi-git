<?php

	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input     */
    $tbl = mysqli_real_escape_string($conn, $_REQUEST['tbl']);
	$col = mysqli_real_escape_string($conn, $_REQUEST['col']);
	$col1 = mysqli_real_escape_string($conn, $_REQUEST['col1']);
	$val = mysqli_real_escape_string($conn, $_REQUEST['val']);
	
	$file_sql = "SELECT ".$col." FROM ".$tbl." WHERE ".$col1."=".$val;

	//echo $file_sql;
	$result = mysqli_query($conn, $file_sql);

	if (mysqli_num_rows($result) > 0){
        $json_response = array();

		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {  
			$row_array['file_content'] = base64_encode($row[''.$col]);
			array_push($json_response,$row_array);
		}  
		  
		echo json_encode($json_response);
    }else{
    	echo "Invalid.";
    }

	mysqli_free_result($result);

	$conn->close();
?>