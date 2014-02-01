<?php
require_once('../../includes/header.php');

if (file_exists(SITE_ROOT.'/themes/'.THEME.'/account/register.php')){//account register from the theme!
	require_once(SITE_ROOT.'/themes/'.THEME.'/account/register.php'); 
}
else{//not found in theme
?>
<h2><?php _e("Register")?></h2>
<?php
$show_form = true;

if ($_POST){
    if(checkMathCaptcha('register'))	{
        $name = cP('name');
        $email = cP('email');
        $password = cP('password');
        $password_confirmation = cP('password_confirmation');
        $agree_terms = cP('agree_terms');
    
        if ($agree_terms == "yes"){
            if (isEmail($email)){        
                if ($password == $password_confirmation){
                    $account = new Account($email);
                    if ($account->exists){
                        echo "<div id='sysmessage' class='logincls'>".T_("Account already exists")."</div>";
                    }
                    else {
                        if ($account->Register($name,$email,$password)){
                            $token=$account->token();
                            
                            $url=accountRegisterURL();
                            if (strpos($url,"?")) $url.='&amp;account='.$email.'&amp;token='.$token.'&amp;action=confirm';
                            else $url.='?account='.$email.'&amp;token='.$token.'&amp;action=confirm';
                            
                            $message='<p>'.T_("Click the following link or copy and paste it into your browser address field to activate your account").'</p>
                        	<p><a href="'.$url.'">'.T_("Confirm account").'</a></p><p>'.$url.'</p>';
                            
							$imageURL		=	SITE_URL."/images/postwala-gif.gif";
			
							$PriceImage		=	SITE_URL."/images/Pricemask_promo.jpg";
							
							$PostwalaURL	=	SITE_URL;

							$PricemaskURL	=	PRICEMASK_URL;

							$BorderImage	=	SITE_URL."/images/Border2.jpg";

                            $array_content[]=array("ACCOUNT", ucwords($name));
							$array_content[]=array("IMAGEURL", $imageURL);
							$array_content[]=array("PRICEIMAGE", $PriceImage);
							$array_content[]=array("POSTURL", $PostwalaURL);
							$array_content[]=array("PRICEURL", $PricemaskURL);
							$array_content[]=array("BORDERIMAGE", $BorderImage);
                            $array_content[]=array("MESSAGE", $message);
                            
                            $bodyHTML=buildEmailBodyHTML($array_content);
                            
                        	sendEmail($email,T_("Confirm your account")." - ".SITE_NAME,$bodyHTML);//email registration confirm request
                            
                            $show_form = false;
                            echo "<div id='sysmessage'>".T_("Instructions to confirm your account has been sent").". ".T_("Please, check your email")."</div>";
                        } else _e("An unexpected error has occurred trying to register your account");
                    }
                } else echo "<div id='sysmessage' class='logincls'>".T_("Passwords do not match")."</div>";
            } else echo "<div id='sysmessage' class='logincls'>".T_("Wrong email")."</div>";
        } else echo "<div id='sysmessage' class='logincls'>".T_("Terms agreement is required")."</div>";
    } else echo "<div id='sysmessage' class='logincls'>".T_("Your answer is wrong")."</div>";//wrong captcha
}

if (trim(cG('account'))!="" && trim(cG('token'))!="" && trim(cG('action'))=="confirm"){
    $show_form = false;
    
    $email = trim(cG('account'));
    $token = trim(cG('token'));
    
    $account = new Account($email);
    if ($account->exists){
        if ($account->Activate($token)){
            echo "<div id='sysmessage'>".T_("Your account has been succesfully confirmed")."</div>";
            
            $bodyHTML="<p>".T_("NEW account registered")."</p><br/>".T_("Email").": ".$account->email." - ".$account->signupTimeStamp();
        	sendEmail(NOTIFY_EMAIL,T_("NEW account")." - ".SITE_NAME,$bodyHTML);//email to the NOTIFY_EMAIL
            
            $account->logOn($account->password());
            
            echo '<p><a href="'.accountURL().'">'.T_("Welcome").' '.$account->name.'</a></p><br/>';
        } else echo "<div id='sysmessage' class='logincls'>".T_("An unexpected error has occurred trying to confirm your account")."</div>";
    } else echo "<div id='sysmessage' class='logincls'>".T_("Account not found")."</div>";
}

if ($show_form){
?>
<div>
<form id="registerForm" action="" onsubmit="return checkRegisterForm(this);" method="post">
    <p class="elementspace"><label for="name"><?php _e("Name")?>:<br />
    <input type="text" id="name" name="name" value="<?php echo $name;?>" maxlength="250" onblur="validateAdText(this); validateTextBox(this);" lang="false" />&nbsp;<span id="namespan" class='errortxt errorwidth'></span></label></p>
    <p class="elementspace"><label for="email"><?php _e("Email")?>:<br />
    <input type="text" id="email" name="email" value="<?php echo $email;?>" maxlength="145" onblur="validateEmails(this);" lang="false" /></label> <label for="email1"> (This will be your username) </label>&nbsp;<span id="emailspan" class='errortxt errorwidth'></span> </p>
    <p class="elementspace"><label for="password"><?php _e("Password")?>:<br />
    <input type="password" id="password" name="password" value="" onblur="validateAdText(this); validateTextBox(this);" lang="false" />&nbsp;<span id="passwordspan" class='errortxt errorwidth'></span></label></p>
    <p class="elementspace"><label for="password_confirmation"><?php _e("Confirm password")?>:<br />
    <input type="password" id="password_confirmation" value="" name="password_confirmation" onblur="validateConfirm(this);" lang="false" />&nbsp;<span id="password_confirmationspan" class='errortxt errorwidth'></span></label></p>
    <p><label for="agree_terms"><input type="checkbox" id="agree_terms" name="agree_terms" value="yes" style="width: 10px;" onblur="CustomCheck(this,'Terms & Conditions');"/> <?php _e("Accept")?> <a href="<?php echo termsURL();?>"><?php _e("Terms")?></a> - <?php echo SITE_NAME?>&nbsp;<span id="agree_termsspan" class='errortxt errorwidth'></span></label></p>
    <br />
	<?php if (CAPTCHA){
		mathCaptcha('register');?>
	<p class="elementspace"><input id="math" name="math" type="text" size="2" maxlength="2"  onblur="validateTextBox(this); validateAdNumber(this);"  onkeypress="return isNumberKeyAd(event);" lang="false" />&nbsp;<span id="mathspan" class='errortxt errorwidth'></span></p>
    <br /><?php }?>
    <p><input name="submit" id="submit" type="submit" class="but" value="<?php _e("Submit")?>" /></p>
</form>
</div>
<?php
}

}//if else

require_once('../../includes/footer.php');
?>