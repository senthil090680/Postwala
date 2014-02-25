<?php
require_once('../includes/header.php');

if (file_exists(SITE_ROOT.'/themes/'.THEME.'/contact.php')){//contact from the theme!
	require_once(SITE_ROOT.'/themes/'.THEME.'/contact.php'); 
}
else{//not found in theme

if ($_POST){//contact form
	if(checkMathCaptcha('contact'))	{
		if (isEmail(cP("email"))){//is email
			if(!isSpam(cP("name"),cP("email"),cP("msg"))){//check if is spam!
				//generate the email to send to the client that is contacted
				$subject=T_("Contact").SEPARATOR.cP("subject").SEPARATOR. $_SERVER['SERVER_NAME'];
				$body=cP("name")." (".cP("email").") ".T_("contacted you about the Ad") . "<br /><br />".cP("msg");
	
				sendEmailComplete(NOTIFY_EMAIL,$subject,$body,cP("email"),cP("name"));
				
				echo "<div id='sysmessage'>".T_("Message sent, thank you")."</div>";
			}//end akismet
			else echo "<div id='sysmessage'>".T_("Oops! Spam? If it was not spam, contact us")."</div>";
		}
		else echo "<div id='sysmessage'>".T_("Wrong email")."</div>";	
	}
	else echo "<div id='sysmessage'>".T_("Wrong captcha")."</div>";
}
?>
<a href="<?php echo SITE_URL."/".contactURL()."?subject=".T_("Suggest new category");?>"><?php _e("Suggest new category");?></a>
<h3><?php _e("Contact");?></h3>
<form method="post" action="" id="contactItem" onsubmit="return checkForm(this);">
<p>
<?php _e("Your Name");?>*:<br />
<input id="name" name="name" type="text" value="<?php echo cP("name");?>" maxlength="75" onblur="validateText(this);"  onkeypress="return isAlphaKey(event);" lang="false"  /><br />
<?php _e("Email");?>*:<br />
<input id="email" name="email" type="text" value="<?php echo cP("email");?>" maxlength="120" onblur="validateEmail(this);" lang="false"  /><br />
<?php _e("Subject");?>*:<br />
<input id="subject" name="subject" type="text" value="<?php echo cP("subject");?><?php echo cG("subject");?>" maxlength="75" onblur="validateText(this);"  onkeypress="return isAlphaKey(event);" lang="false"  /><br />
<?php _e("Message");?>*:<br />
<textarea rows="10" cols="79" name="msg" id="msg" onblur="validateText(this);"  lang="false"><?php echo strip_tags(stripslashes($_POST['msg']));?></textarea><br />
<?php if (CAPTCHA){
	mathCaptcha('contact');?>
<input id="math" name="math" type="text" size="2" maxlength="2"  onblur="validateNumber(this);"  onkeypress="return isNumberKey(event);" lang="false" />
<?php }?>
<br />
<br />
<input type="submit" id="submit" value="<?php _e("Contact");?>" />
</p>
</form>
<?php

}//if else
?>

<br/> <br/>
<p>If you need any help in using the site or are facing site related issues, please don't hesitate to contact us at <a href="mailto:classifieds@postwala.com" target="_blank">classifieds@postwala.com</a> or fill the above form.  We will get in touch with you soon.</p>
<br/>
<p>  To share your ideas, marketing proposals, partnership relationship or want us to be your classifieds partner, please contact us at <a href="mailto:classifieds@postwala.com" target="_blank">classifieds@postwala.com</a> and provide us with below details in your email:</p>
<div class="list-disc-order">
	<ul>
	  <li>Your name</li>
	  <li>Name of the company</li>
	  <li>Your phone/mobile number</li>
	  <li>Your comments/proposals</li>
	</ul>
</div>
<br />
<p><strong>Our Address:</strong></p>
<p><strong></strong><strong>Podhigai Info Media<br />
</strong> </p>
<div>
<br/>
  <div>Registered Address: No:93, 6th Block, Muthamizh Nagar, Kodungaiyur, Chennai - 600 118.</div>
  <div>Office Address: Office No: 10, 1st Floor, Sun Plaza, No: 19, G.N. Chetty Road, Chennai - 600 006</div>
  <div>Phone: 044-42359332, M: 9840878901</div>
</div>

<?php
require_once('../includes/footer.php');
?>