<?php

require_once('functions.php');


$responseparams = $_POST["responseparams"];
$responseparamsArr = explode("|", $responseparams);

$idPostArr = explode("~", $responseparamsArr[5]); 

$idPost = $idPostArr[1];
$idPostTimestamp = $idPostArr[2];

$status = $responseparamsArr[1];
$Payment = $responseparamsArr[6];

updatePaymentDetails($idPost, $Payment, $status,$idPostTimestamp); 
sendEmail(DIRECTPAY_ACCOUNT_NOTIFY,'Payment Notification from Directpay','Please find the response from Direct Pay:\n\n'. print_r($_POST,true));

header('Location:' . SITE_URL . '/my-account/');
die();


////////////////////////////////////////////////////////////
function updatePaymentDetails($idPost, $payment, $status, $idPostTimestamp){//get the location name
    if (isset($idPost)&&is_numeric($idPost)) {
        $ocdb=phpMyDB::GetInstance();
	if($status == "SUCCESS")
	        $query="UPDATE ".TABLE_PREFIX."premium_posts SET Paid = 'Y', StartDate = CURRENT_DATE, EndDate= ADDDATE(CURRENT_DATE, `ActiveDays`),  PaymentDate = CURRENT_DATE where idPost=$idPost AND Paid IN ('N', 'F') AND PostedDate = '". $idPostTimestamp ."' ";
	else if($status == "FAIL")	
                $query="UPDATE ".TABLE_PREFIX."premium_posts SET Paid = 'F' where idPost=$idPost AND PostedDate = '". $idPostTimestamp ."' ";
	else
	    directpayProblem();
	$ocdb->query($query);
	} 
     else
	    directpayProblem();
}

?>
