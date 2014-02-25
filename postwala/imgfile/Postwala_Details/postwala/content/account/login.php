<?php
require_once('../../includes/header.php');

if (file_exists(SITE_ROOT.'/themes/'.THEME.'/account/login.php')){//account login from the theme!
	require_once(SITE_ROOT.'/themes/'.THEME.'/account/login.php'); 
}
else{//not found in theme
$account = Account::createBySession();
if ($account->exists) redirect(accountURL()); 
?>
<br />
<h3><?php echo 'Don&#39;t have an account? <a href="'.accountRegisterURL().'">'.T_("Register").'</a> with Postwala Classifieds and start publishing your ads!'; ?> </h3>
<br />

<h2><?php _e("Login")?></h2>
<?php
if ($_POST){
    $email = cP('email');
    $password = cP('password');
    $rememberme = cP('rememberme');
    if ($rememberme == "1") $rememberme = true;
    else $rememberme = false;
    
    $account = new Account($email);
		
		
		if ($account->logOn($password,$rememberme,"ocEmail")){
            if($_SESSION['publishadurl'] !='' && isset($_SESSION['publishadurl'])) { 
				redirect($_SESSION['publishadurl']); 
			}
			else redirect(accountURL());
        }
        else {
            if (!$account->exists) echo "<div id='sysmessage' class='logincls'>".T_("Account not found")."</div>";//account not found by email
            elseif (!$account->status_password) echo "<div id='sysmessage' class='logincls'>".T_("Wrong password")."</div>";//wrong password
            elseif (!$account->active) echo "<div id='sysmessage'>".T_("Account is disabled")."</div>";//account is disabled
    }
} else {
    $email = $_COOKIE["ocEmail"];
    if ($email!="") $rememberme = "1";
}
?>
<div>
<form id="loginForm" name="loginForm" action="" method="post" onsubmit="return checkLoginForm(this);">
	<p class="elementspace"><label for="email"><?php _e("Email")?>:<br />
    <input type="text" name="email" id="email" maxlength="145" value="<?php echo $email;?>" onblur="validateEmails(this);" lang="false" />&nbsp;<span id="emailspan" class='errortxt errorwidth'></span></label></p>
	<p><label for="password"><?php _e("Password")?>:<br />
    <input type="password" name="password" id="password" maxlength="<?php PASSWORD_SIZE?>" onblur="validateAdText(this); validateTextBox(this);" lang="false" />&nbsp;<span id="passwordspan" class='errortxt errorwidth'></span></label></p>
	<p><label for="rememberme"><input type="checkbox" name="rememberme" id="rememberme" value="1" <?php if ($rememberme == "1") echo "checked ";?> style="width: 10px;" /><small><?php _e("Remember me on this computer");?></small></label></p>
	<p style="padding-left:50px;"><button type="submit" id="submit" style="border: 0; background: transparent">
		<img src="<?php echo SITE_URL; ?>/images/login-new.jpg" width="106" heght="51" alt="submit" />
	</button></p>
    <br />
	<p><?php echo '<a href="'.accountRecoverPasswordURL().'">'.T_("Forgot My Password").'</a>';?></p>
</form>
</div>
<?php
}//if else

require_once('../../includes/footer.php');
?>