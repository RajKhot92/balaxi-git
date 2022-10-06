<?php
    header('Access-Control-Allow-Origin: *');
    
    function sendEmail($mail_to,$mail_cc,$subject,$content){
        $MAIL_FROM = 'info@regulatorybalaxi.in';
        $retval = "0";
        $cc_string = '';
        $cc_updt_string = '';
        
        ini_set( 'display_errors', 1 );
        error_reporting( E_ALL );
        $headers = "From:" . $MAIL_FROM . "\r\n";

        if(!empty($mail_cc)){
            foreach($mail_cc as $cc_id){
                $cc_string .= $cc_id . ",";
            }
            $cc_updt_string = rtrim($cc_string, ",");

            $headers .= "Cc:". $cc_updt_string . " \r\n";
        }
        
        
        $headers .= "MIME-Version: 1.0\r\n";
    	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
        if (mail($mail_to,$subject,$content, $headers)){
            $retval = "1";
        }else{
            $retval = "0";
        }
        
        return $retval;
    }
?> 