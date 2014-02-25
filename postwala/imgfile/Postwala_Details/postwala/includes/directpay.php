<?php
//directpay functions

function directpayForm($idItem, $AdCost, $PostedDate){
					 
	if (DIRECTPAY_REGION == 'DEV') 
	{	
	        $directpayWeb =  DIRECTPAY_DEV_URL;
		$directpayMID = DIRECTPAY_TEST_MID;
		$collaborator = "TOML";
	}
	else 
	if (DIRECTPAY_REGION == 'TEST') 
	{	
		$directpayWeb = DIRECTPAY_TEST_URL; 
		$directpayMID = DIRECTPAY_TEST_MID;
		$collaborator = "TOML";
	}
	else 
	if (DIRECTPAY_REGION == 'PROD') 
	{
		$directpayWeb = DIRECTPAY_PROD_URL;
		$directpayMID = DIRECTPAY_PROD_MID;
		$collaborator = "DirecPay";
	}
	
	
	$city = getLocationName(cP("location")) ; 
	$state = getLocationParentName(cP("location")) ; 
	
	
	?>
	        <div style="font-family: Arial; font-size: 20px; text-align: center; margin-top: 200px;">
	        	<?php  _e('Please wait while transferring to Payment Gateway...');?><br /> <br /> <br />
			<img src="<?php echo SITE_URL; ?>/images/loader.gif" border="0"> 
		</div>

		<script type="text/javascript" src="<?php echo SITE_URL;?>/includes/js/dpEncodeRequest.js"></script>

		<form name="ecom" method="post" action="<?php echo $directpayWeb;?>">
			<input type="hidden" name="custName" value="<?php echo cP("name"); ?>">
			<input type="hidden" name="custAddress" value="<?php echo cP("place"); ?>">
			<input type="hidden" name="custCity" value="<?php echo $city; ?> ">
			<input type="hidden" name="custState" value="<?php echo $state; ?> ">
			<input type="hidden" name="custPinCode" value="">
			<input type="hidden" name="custCountry" value="IN">
			<input type="hidden" name="custPhoneNo1" value="<?php echo cP("phone"); ?>">
			<input type="hidden" name="custPhoneNo2" value="0">
			<input type="hidden" name="custPhoneNo3" value="0">
			<input type="hidden" name="custMobileNo" value="<?php echo cP("phone"); ?>">
			<input type="hidden" name="custEmailId" value="<?php echo cP("email"); ?>">
			<input type="hidden" name="deliveryName" value="<?php echo cP("name"); ?>">
			<input type="hidden" name="deliveryAddress" value="<?php echo cP("place"); ?>">
			<input type="hidden" name="deliveryCity" value="<?php echo $city; ?> ">
			<input type="hidden" name="deliveryState" value="<?php echo $state; ?> ">
			<input type="hidden" name="deliveryPinCode" value="">
			<input type="hidden" name="deliveryCountry" value="IN">
			<input type="hidden" name="deliveryPhNo1" value="<?php echo cP("phone"); ?>">
			<input type="hidden" name="deliveryPhNo2" value="0">
			<input type="hidden" name="deliveryPhNo3" value="0">
			<input type="hidden" name="deliveryMobileNo" value="<?php echo cP("phone"); ?>">
			<input type="hidden" name="otherNotes" value="<?php echo "Payment for postwala post: " . $idItem; ?>">
			<input type="hidden" name="editAllowed" value="Y">
			<input type="hidden" name="requestparameter"
			value="<?php echo $directpayMID; ?>|DOM|IND|INR|<?php echo $AdCost."|Postwala~".$idItem."~".$PostedDate; ?>|Payment for postwala post|<?php echo SITE_URL;?>/includes/payment-response.php|
			<?php echo SITE_URL;?>/includes/payment-response.php|<?php echo $collaborator;  ?>">
                        
			
	        </form>
		<script type="text/javascript">
			
			<?php  if (DIRECTPAY_REGION != 'DEV') { ?>			
			document.ecom.requestparameter.value = encodeValue(document.ecom.requestparameter.value);
			<?php  }  ?>			
			document.ecom.submit();
			
		</script>
	        
	<?php
	require_once(SITE_ROOT.'/includes/footer.php');

	die();
}

//validates the IPN
function validate_ipn(){
	if (DIRECTPAY_SANDBOX) $URL='ssl://www.sandbox.directpay.com';
	else $URL='ssl://www.directpay.com';
	$result = FALSE;

	// read the post from PayPal system and add 'cmd'
	$req = 'cmd=_notify-validate';

	foreach ($_REQUEST as $key => $value) {
		$value = urlencode(stripslashes($value));
		if($key=="sess" || $key=="session") continue;
		$req .= "&$key=$value";
	}

	// post back to PayPal system to validate
	$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
	$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
	$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
	$fp = fsockopen ($URL, 443, $errno, $errstr, 30);

	if (!$fp) {
		//error email
		directpayProblem('Paypal connection error');
	} else {
		fputs ($fp, $header . $req);
		while (!feof($fp)) {
			$res = fgets ($fp, 1024);
			if (strcmp ($res, "VERIFIED") == 0) {
				$result = TRUE;
			}
			else if (strcmp ($res, "INVALID") == 0) {
				//log the error in some system?
				//directpayProblem('Invalid payment');
			}
		}
		fclose ($fp);
	}
	return $result;
}

//directpay problem on payment for IPN
function directpayProblem($problem='Problem with directpay payment'){
	sendEmail(DIRECTPAY_ACCOUNT_NOTIFY,$problem,$problem.'This email informs you that somebody tried to cheat the payment system of directpay, please check next values:'. print_r($_POST,true));
	die();
}


?>