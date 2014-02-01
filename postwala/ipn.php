<?php
require_once('includes/functions.php');

//START PAYPAL IPN

//manual checks
if (!is_numeric(cP('item_number'))) paypalProblem('Not any idItem.');
else $idItem=cP('item_number');


///retrieve all the info for the item in DB
$query="select password
		from ".TABLE_PREFIX."posts p
		where idPost=$idItem and isConfirmed=0  Limit 1";
$post_password=$ocdb->getValue($query);
if ($post_password!='') paypalProblem('Coul not find the Item in DB.');//not found
		

if (cP('mc_gross')==PAYPAL_AMOUNT  && cP('mc_currency')==PAYPAL_CURENCY && (cP('receiver_email')==PAYPAL_ACCOUNT || cP('business')==PAYPAL_ACCOUNT)){//same price ,  currency and email no cheating ;)

	if (validate_ipn()) confirmPost($idItem,$post_password); //payment succeed and we confirm the post ;)     
	 
	else{
	    // Log an invalid request to look into 
	    // PAYMENT INVALID & INVESTIGATE MANUALY!
	    $subject = 'Invalid Payment';
	    $message = 'Dear Administrator,<br />
	    A payment has been made but is flagged as INVALID.<br />
	    Please verify the payment manualy and contact the buyer. <br /><br />Here is all the posted info:';
	    sendEmail(PAYPAL_ACCOUNT,$subject,$message.'<br />'.print_r($_POST,true));
	} 

}
//trying to cheat....
else paypalProblem('Trying to fake the post data');	

?>
    
