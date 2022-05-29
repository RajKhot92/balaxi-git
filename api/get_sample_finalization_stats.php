<?php
	header('Access-Control-Allow-Origin: *');
	include "db_connect.php";

    /*  Taking user input   */
    $user_id = mysqli_real_escape_string($conn, $_REQUEST['user_id']);
    $menu_id = mysqli_real_escape_string($conn, $_REQUEST['menu_id']);
    $product_id = mysqli_real_escape_string($conn, $_REQUEST['product_id']);

    if($user_id === "" || $menu_id === "" || $product_id === ""){
        echo "Kindly provide valid input.";
        exit();
    }

    $user_exist = "SELECT user_id FROM user_master 
                WHERE user_id = ".$user_id."
                AND del_by IS NULL";
    $user_response = mysqli_query($conn, $user_exist);
    if (mysqli_num_rows($user_response) == 0){
        echo "User details not found for user id ".$user_id." in the system.";
        exit();
    }

    $menu_exist = "SELECT menu_id FROM menu_master 
                WHERE menu_id = ".$menu_id;
    $menu_response = mysqli_query($conn, $menu_exist);
    if (mysqli_num_rows($menu_response) == 0){
        echo "Menu details not found for menu id ".$menu_id." in the system.";
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

    /*  Getting Progress    */
    $total_progress_artwork = 0;
    $total_entries_artwork = 0;

    $get_artwork_question_sql = "SELECT count(*) cnt FROM `product_artwork_question` 
                            WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
    $result_artwork_question = mysqli_query($conn,$get_artwork_question_sql);
    while ($row_artwork_question = mysqli_fetch_array($result_artwork_question, MYSQLI_ASSOC)) {  
        $row_array['artwork_question'] = $row_artwork_question['cnt'] > 0 ? 100 : 0;
        $total_progress_artwork += (int)$row_artwork_question['cnt'] > 0 ? 1 : 0;
        $total_entries_artwork++;
    }

    $get_artwork_sql = "SELECT count(*) cnt FROM `product_artwork` 
                            WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
    $result_artwork = mysqli_query($conn,$get_artwork_sql);
    while ($row_artwork = mysqli_fetch_array($result_artwork, MYSQLI_ASSOC)) {  
        $row_array['artwork_form'] = $row_artwork['cnt'] > 0 ? 100 : 0;
        $total_progress_artwork += (int)$row_artwork['cnt'] > 0 ? 1 : 0;
        $total_entries_artwork++;
    }

    $get_commercial_doc_sql = "SELECT count(*) cnt FROM `product_commercial_doc` 
                            WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
    $result_commercial_doc = mysqli_query($conn,$get_commercial_doc_sql);
    while ($row_commercial_doc = mysqli_fetch_array($result_commercial_doc, MYSQLI_ASSOC)) {  
        $row_array['commercial_doc'] = $row_commercial_doc['cnt'] > 0 ? 100 : 0;
        $total_progress_artwork += (int)$row_commercial_doc['cnt'] > 0 ? 1 : 0;
        $total_entries_artwork++;
    }

    $total_progress_finalize = 0;
    $total_entries_finalize = 0;

    $get_artwork_finalize_sql = "SELECT count(*) cnt FROM `product_artwork_finalize` 
                            WHERE `ent_by`=".$user_id." AND product_id=".$product_id;
    $result_artwork_finalize = mysqli_query($conn,$get_artwork_finalize_sql);
    while ($row_artwork_finalize = mysqli_fetch_array($result_artwork_finalize, MYSQLI_ASSOC)) {  
        $row_array['artwork_finalize'] = $row_artwork_finalize['cnt'] > 0 ? 100 : 0;
        $total_progress_finalize += (int)$row_artwork_finalize['cnt'] > 0 ? 1 : 0;
        $total_entries_finalize++;
    }

    // echo "progess=".$total_progress_artwork;
    // echo "entries=".$total_entries_artwork;
    $total_percent_artwork = (int)$total_progress_artwork / (int)$total_entries_artwork * 100;
    $row_array['artwork'] = $total_percent_artwork;

    // echo "progess=".$total_progress_finalize;
    // echo "entries=".$total_entries_finalize;
    $total_percent_finalize = (int)$total_progress_finalize / (int)$total_entries_finalize * 100;
    $row_array['finalization'] = $total_percent_finalize;

    $json_response = array();
    array_push($json_response,$row_array);

    echo json_encode($json_response); 
    mysqli_free_result($result_artwork_question);
    mysqli_free_result($result_artwork);
    mysqli_free_result($result_commercial_doc);
    mysqli_free_result($result_artwork_finalize);

    $conn->close();

?>