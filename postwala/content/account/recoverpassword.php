<?php
require_once('../../includes/header.php');

if (file_exists(SITE_ROOT.'/themes/'.THEME.'/account/recoverpassword.php')){//account recoverpassword from the theme!
	require_once(SITE_ROOT.'/themes/'.THEME.'/account/recoverpassword.php'); 
}
else{//not found in theme

?>
<h2><?php _e("Forgot My Password")?></h2>
<?php
$show_form = true;

if ($_POST){
    $email = trim(cP('email'));
    $account = new Account($email);
    
    if ($account->exists){
        $message='<p>'.T_("Your log on information").'</p>
    	<p><label>'.T_("Email").': '.$account->email.'</label><br/>
        <label>'.T_("Password").': '.$account->password().'</label></p>
		<p><label> Post Ad Now : <a href='.SITE_URL.newURL($email,$account->password()).' >'.T_("Go to Post Ad Page ").'</a></label></p>';
        
		$imageURL		=	SITE_URL."/images/postwala-gif.gif";
			
		$PriceImage		=	SITE_URL."/images/Pricemask_promo.jpg";
		
		$PostwalaURL	=	SITE_URL;

		$PricemaskURL	=	PRICEMASK_URL;

		$BorderImage	=	SITE_URL."/images/Border2.jpg";

		$userName		=	ucwords($account->name);
				
        $array_content[]=array("ACCOUNT", $userName);
		$array_content[]=array("IMAGEURL", $imageURL);
		$array_content[]=array("PRICEIMAGE", $PriceImage);
		$array_content[]=array("POSTURL", $PostwalaURL);
		$array_content[]=array("PRICEURL", $PricemaskURL);
		$array_content[]=array("BORDERIMAGE", $BorderImage);
        $array_content[]=array("MESSAGE", $message);

		$bodyHTML=buildEmailBodyHTML($array_content);
		
    	sendEmail($email,T_("Your log on information")." - ".SITE_NAME,$bodyHTML);//password to the account's email	
        
        $show_form = false;
        echo "<div id='sysmessage'>".T_("Your password has been sent").". ".T_("Please, check your email")."</div>";//password sent notice
    } else echo "<div id='sysmessage'>".T_("Account not found")."</div>";//account not found by email
} else $email = $_COOKIE["ocEmail"];

if ($show_form){
?>
<div>
<form name="recoverPasswordForm" action="" onsubmit="return checkForm(this);" method="post">
    <p><label for="email"><?php _e("Email")?>:<br />
    <input type="text" id="email" name="email" value="<?php echo $email?>" maxlength="145" onblur="validateEmail(this);" lang="false" /></label></p>
    <br />
    <p><input name="submit" id="submit" type="submit" class="but" value="<?php _e("Submit")?>" /></p>
</form>
</div>
<?php
}

}//if else

require_once('../../includes/footer.php');
?>