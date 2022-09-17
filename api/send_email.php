<?php
    header('Access-Control-Allow-Origin: *');
    
    function sendEmail($mail_to,$mail_cc,$subject,$content){
        $MAIL_FROM = 'info@regulatorybalaxi.in';
        $MAIL_SANDEEP = 'sandeep@balaxi.com';
        $retval = "0";
        
        ini_set( 'display_errors', 1 );
        error_reporting( E_ALL );
        $headers = "From:" . $MAIL_FROM . "\r\n";

        if($mail_cc != null){
            $headers .= "Cc:". $mail_cc . ", ". $MAIL_SANDEEP ." \r\n";    
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