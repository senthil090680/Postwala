<?php
require_once('includes/header.php');
if (is_numeric($idItem)){
	 //contact form
	if(cP("emailemail") != '') {
	if (cP("contact")==1&&$itemAvailable==1){
		//if(checkMathCaptcha($idItem))	{
			if(isEmail(cP("emailemail"))){//is email
				if(!isSpam(cP("name"),cP("emailemail"),cP("emailmsg"))){//check if is spam!
					//generate the email to send to the client that is contacted
					$subject=T_("Contact")." ".html_entity_decode($itemTitle, ENT_QUOTES, CHARSET).SEPARATOR. SITE_NAME;

					$imageURL		=	SITE_URL."/images/postwala-gif.gif";
			
					$PriceImage		=	SITE_URL."/images/Pricemask_promo.jpg";
					
					$PostwalaURL	=	SITE_URL;

					$PricemaskURL	=	PRICEMASK_URL;

					$cusName		=	cP("name");

					$cusName		=	ucwords($cusName);

					$LinkUrl		=	"http://".$_SERVER[SERVER_NAME].urldecode($_SERVER["REQUEST_URI"]);

					$BorderImage	=	SITE_URL."/images/Border2.jpg";
					
                    $message="<p>".$cusName." (".cP("emailemail").") ".T_("contacted you about the Ad") ."&nbsp;<a href='".$LinkUrl."'>".cP("itemTitle")."</a><br /><br />".$cusName."&nbsp;". T_("responded that :") ."<br/>&nbsp;".
							 cP("emailmsg");
					
					if(cP("emailmobile") != '') { $message .= "<br />".$cusName."'s mobile number is :<br/>&nbsp;".
					 cP("emailmobile"); }

					 $message .= "<br /><br />".T_("Do not answer this email, answer to this email").": ".cP("emailemail");

					 $message .= "<br /><br />Thank you for choosing Postwala Classifieds.</p>";
						                  
                    $array_content[]=array("ACCOUNT", T_(ucwords($itemName)));
                    $array_content[]=array("MESSAGE", $message);
					$array_content[]=array("IMAGEURL", $imageURL);
					$array_content[]=array("PRICEIMAGE", $PriceImage);
					$array_content[]=array("POSTURL", $PostwalaURL);
					$array_content[]=array("PRICEURL", $PricemaskURL);
					$array_content[]=array("BORDERIMAGE", $BorderImage);
                    
                    $bodyHTML=buildEmailBodyHTML($array_content);
					sendEmailComplete($itemEmail,$subject,$bodyHTML,cP("emailemail"),cP("name"));
					$errorMsg = "<div id='emailmessage' style='width:200px; height:20px; color:#FF6600;'>".T_("Message sent, thank you").".</div>";//
				}//end akismet
				else $errorMsg = "<div id='sysmessage'>".T_("Oops! Spam? If it was not spam, contact us");
			}
			else $errorMsg = "<div id='sysmessage'>".T_("Wrong email address")."</div>";//Wrong email address
		// }
		//else echo "<div id='sysmessage'>".T_("Wrong Captcha")."</div>";//wrong captcha
	}
}
if(cP("smsemail") != '') {
	$smsmsg				=	cP("smsmsg");
	$smsmsg	 = substr($smsmsg, 80);
	$smsmobile			=	cP("smsmobile");
	$smsemail			=	cP("smsemail");
	$curDate			=	date('dmY');
	
	$title				=	cP("itemTitle");
	
	$message=T_("Reply to ur Ad")." '".$title."': ".$smsmsg." ".$smsemail." ".$smsmobile." WWW.POSTWALA.COM";

	$uid				=	'suresh.seeni';
	$pwd				=	'suresh111';
	$sid				=	'POSTWALA';
			
	$baseurl   = 'http://www.smsintegra.com/smsweb/desktop_sms/desktopsms.asp';
	$finalurl  = $baseurl;
	$finalurl .= "?uid=".$uid;
	$finalurl .= "&pwd=".$pwd;
	$finalurl .= "&mobile=".$itemPhone;
	$finalurl .= "&msg=".urlencode($message);
	$finalurl .= "&sid=".$sid;
	$finalurl .= "&dtNow=".date("Ymd").date("H:m:s");

	$respose_message  = "<br><br><br>Alert Message:" .$message."<br>";
	$respose_message .= "Mobile No:".$itemPhone."<br>";
	$respose_message .= "Date:".date("Ymd").date("H:m:s")."<br>";
				
	require_once 'HTTP/Request2.php';
	$request = new HTTP_Request2($finalurl, HTTP_Request2::METHOD_POST, array('use_brackets' => true));
	$url = $request->getUrl();

	try {
	
		$respose_message ;
		$request->send()->getBody();
		
		
	} catch (HttpException $ex) {
	   $ex;
	}
	$errorMsg = "<div id='sysmessage' style='width:200px; height:20px; color:#FF6600;'>".T_("Message sent successfully").".</div>";//
}
	//remember form
	
	if (cP("remember")==1&&cP("emailR")==$itemEmail){
		//generate the email to send to the client for remember
					$subject=T_("Remember")." ".html_entity_decode($itemTitle, ENT_QUOTES, CHARSET).SEPARATOR. SITE_NAME;
					
					$EditUrl		=	SITE_URL."/manage/?post=$idItem&pwd=$itemPassword&action=edit";
					
					$imageURL		=	SITE_URL."/images/postwala-gif.gif";
			
					$PriceImage		=	SITE_URL."/images/Pricemask_promo.jpg";
					
					$PostwalaURL	=	SITE_URL;

					$PricemaskURL	=	PRICEMASK_URL;

					$BorderImage	=	SITE_URL."/images/Border 2.jpg";
					
					$message="<p>".T_("To edit the post click here").": 
							 &nbsp;<a href='".$EditUrl."'>".cP("itemTitle")."</a></p>";
                    
                    $array_content[]=array("ACCOUNT", T_(ucwords($itemName)));
                    $array_content[]=array("MESSAGE", $message);
					$array_content[]=array("IMAGEURL", $imageURL);
					$array_content[]=array("PRICEIMAGE", $PriceImage);
					$array_content[]=array("POSTURL", $PostwalaURL);
					$array_content[]=array("PRICEURL", $PricemaskURL);
					$array_content[]=array("BORDERIMAGE", $BorderImage);
       
                    $bodyHTML=buildEmailBodyHTML($array_content);
                    
					sendEmailComplete($itemEmail,$subject,$bodyHTML,"","");
					echo "<div id='sysmessage'>".T_("Message sent, thank you").".</div>";//
	}
	

if (file_exists(SITE_ROOT.'/themes/'.THEME.'/item.php')){//itemfrom the theme!
	require_once(SITE_ROOT.'/themes/'.THEME.'/item.php'); 

}
else{	
	//default not found in theme
?>
<script type="text/javascript" src="<?php echo SITE_URL;?>/includes/greybox/AJS.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL;?>/includes/greybox/gb_scripts.js"></script>
<div class="item">
	<div class="item">
		<h1><a title="<?php echo $itemTitle; ?>" href="<?php echo $_SERVER["REQUEST_URI"];?>">
			<?php echo $itemTitle; ?> <?if ($itemPrice!=0) echo " - ".getPrice($itemPrice);?></a>
		</h1>
	</div>
	<div class="item">
	<p>
		<b><?php _e("Publish Date");?>:</b> <?php echo setDate($itemDate);?> <?php echo substr($itemDate,strlen($itemDate)-8);?><?php echo SEPARATOR;?>
        <b><?php _e("Contact name");?>:</b> 
        <?php
        $account=new Account($itemEmail);
        if ($account->exists){ ?>
        <a href="<?php echo SITE_URL."/".accountPostsURL($itemType,$currentCategory,$itemEmail);?>" target="_blank"><?php echo $itemName; ?></a>
        <?php 
        } else {
            echo $itemName;
        } ?>
        <?php echo SEPARATOR;?>
        <?php if ($itemLocation!="0"){?>
        <b><?php _e("Location");?>:</b> <?php echo getLocationName($itemLocation); ?><?php echo SEPARATOR;?>
        <?php }?>
		<?php if ($itemPlace!=""){?>
			<b><?php _e("Place");?>:</b> 
			<?php if (MAP_KEY!=""){?>
				<a title="<?php _e('Map').$itemPlace;?>" href="<?php echo SITE_URL."/".mapURL()."?address=".$itemPlace;?>" rel="gb_page_center[640, 480]"><?php echo $itemPlace;?></a>
			<?php } else echo $itemPlace;?>
			<?php echo SEPARATOR;?> 
		<?php }?>
		<?php if (COUNT_POSTS) echo "$itemViews ".T_("times displayed").SEPARATOR;?>
		<?php if (DISQUS!=""){ ?><a href="<?php echo $_SERVER["REQUEST_URI"];?>#disqus_thread"><?php _e("Comments");?></a><?php echo SEPARATOR;?> <?php }?>
	</p>	
	</div>
	<?php if (MAX_IMG_NUM>0){?>
		<div id="item">
			<?php 
			foreach($itemImages as $img){
				echo '<a href="'.$img[0].'" title="'.$itemTitle.' '.T_("Picture").'" rel="gb_imageset['.$idItem.']">
				 		<img class="thumb" src="'.$img[1].'" title="'.$itemTitle.' '.T_("Picture").'" alt="'.$itemTitle.' '.T_("Picture").'" /></a>';
			}
			?>
		</div>
	<?php }?>
	<div class="item">	
		<?php echo $itemDescription; ?>
		<br /><br />



<!-- AddThis Button BEGIN -->
<div class="addthis_toolbox addthis_default_style">
<a href="http://www.addthis.com/bookmark.php?v=250" class="addthis_button_compact"><?php _e("Share");?></a>
<a class="addthis_button_facebook"></a>
<a class="addthis_button_myspace"></a>
<a class="addthis_button_google"></a>
<a class="addthis_button_twitter"></a>
<a class="addthis_button_print"></a>
<a class="addthis_button_email"></a>
<a href="<?php echo SITE_URL."/".contactURL();?>?subject=<?php _e("Report bad use or Spam");?>: <?php echo $itemName." (".$idItem.")";?>"><?php _e("Report bad use or Spam");?></a>
</div>
<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js"></script>
<!-- AddThis Button END -->
	</div>
	<?php if ($itemAvailable==1){?>
	<h2 style="cursor:pointer;" onclick="openClose('contactmail');"><?php _e("Contact");?> <?php echo $itemName.': '.$itemTitle;?></h2>
	<div id="contactmail">
		<?php if ($itemPhone!=""){?><b><?php _e("Phone");?>:</b> <?php echo encode_str($itemPhone); ?><?php }?>
		<form method="post" action="" id="contactItem" onsubmit="return checkForm(this);">
		<p>
		<?php _e("Your Name");?>*:<br />
		<input id="name" name="name" type="text" value="<?php echo cP("name");?>" maxlength="75" onblur="validateText(this);"  onkeypress="return isAlphaKey(event);" lang="false"  /><br />
		
		<?php _e("Email");?>*:<br />
		<input id="email" name="email" type="text" value="<?php echo cP("email");?>" maxlength="120" onblur="validateEmail(this);" lang="false"  /><br />
		
		<?php _e("Message");?>*:<br />
		<textarea rows="10" cols="79" name="msg" id="msg" onblur="validateText(this);"  lang="false"><?php echo strip_tags(stripslashes($_POST['msg']));?></textarea><br />
		<?php if (CAPTCHA){
		  mathCaptcha($idItem);?>
		<input id="math" name="math" type="text" size="2" maxlength="2"  onblur="validateNumber(this);"  onkeypress="return isNumberKey(event);" lang="false" />
		<?php }?>
		<br />
		<br />
		<input type="hidden" name="contact" value="1" />
		<input type="submit" id="submit" value="<?php _e("Contact");?>" />
		</p>
		</form> 
	</div>
	<?php } else echo "<div id='sysmessage'>".T_("This Ad is no longer available")."</div>";?>
	<br /><span style="cursor:pointer;" onclick="openClose('remembermail');"> <?php _e("Reminder email with links to edit and deactivate");?></span><br />
	<div style="display:none;" id="remembermail" >
		<form method="post" action="" id="remember" onsubmit="return checkForm(this);">
		<input type="hidden" name="remember" value="1" />
		<input onblur="this.value=(this.value=='') ? 'email' : this.value;" 
				onfocus="this.value=(this.value=='email') ? '' : this.value;" 
		id="emailR" name="emailR" type="text" value="email" maxlength="120" onblur="validateEmail(this);" lang="false"  />
		<input type="submit"  value="<?php _e("Remember");?>" />
		</form> 
	</div>
	<?php if (DISQUS!=""){ ?>
		<?php if (DEBUG){ ?><script type="text/javascript"> var disqus_developer = 1;</script><?php } ?>
	    <div id="disqus_thread"></div>
	    <script type="text/javascript">
          (function() {
           var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
           dsq.src = 'http://<?php echo DISQUS;?>.disqus.com/embed.js';
           (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
          })();
        </script>
	    <script type="text/javascript">
	    //<![CDATA[
	    (function() {
		    var links = document.getElementsByTagName('a');
		    var query = '?';
		    for(var i = 0; i < links.length; i++) {
		    if(links[i].href.indexOf('#disqus_thread') >= 0) {
			    query += 'url' + i + '=' + encodeURIComponent(links[i].href) + '&';
		    }
		    }
		    document.write('<script charset="utf-8" type="text/javascript" src="http://disqus.com/forums/<?php echo DISQUS;?>/get_num_replies.js' + query + '"></' + 'script>');
	    })();
	    //]]>
	    </script>
	<?php } ?>
</div>
<?php
	}//else theme
}
else jsRedirect(SITE_URL); //if is numeric
require_once('includes/footer.php');
?>