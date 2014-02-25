<?php
#============================================================================================================
# Author 		: J P Senthil Kumar
# Start Date	: 25 May 2011
# End Date		: 25 May 2011
# Project		: Publish A New Ad
# Description	: This file is used to add a new advertisement with the custom fields coming dynamically
#============================================================================================================
?>
<script type="text/javascript" src="<?php echo SITE_URL;?>/includes/greybox/AJS.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL;?>/includes/greybox/gb_scripts.js"></script>
      <div>
	  <div style="float:left; width:600px; padding-top:20px; padding-right:40px;">
	  <div class="single_area">
	  <div class="viewDiv">
		<div class="viewTitle"><?php echo ucwords($itemTitle); ?> <?if ($itemPrice!=0) echo " - ".getPrice($itemPrice);?></div>	
			<span class="viewSpan"><?php _e("Publish Date");?>: <?php echo setDate($itemDate);?> <?php //echo substr($itemDate,strlen($itemDate)-8);?></span>
			<span class="viewSpan"><?php _e("Contact name");?>: 
			<?php
			$account=new Account($itemEmail);
			if ($account->exists){ ?>
			<a href="<?php echo SITE_URL."/".accountPostsURL($itemType,$currentCategory,$itemEmail);?>" target="_blank"><?php echo $itemName; ?></a>
			<?php 
			} else {
				echo $itemName;
			} ?>
			</span>
			<?php if($itemPhone!=""){ ?><span class="viewSpan viewBottom"><?php _e("Phone");?>: <?php echo encode_str($itemPhone); ?></span>
		<?php } ?>
				

		    <!--<?php if ($itemPlace!=""){?>
			    <b><?php _e("Place");?>:</b> 
			    <?php if (MAP_KEY!=""){?>
				    <a title="<?php echo  T_('Map').$itemPlace;?>" href="<?php echo SITE_URL."/".mapURL().".?address=".$itemPlace;?>" rel="gb_page_center[640, 480]"><?php echo $itemPlace;?></a>
			    <?php } else echo $itemPlace;?>
			    <?php echo SEPARATOR;?> 
		    <?php }?>
		    <?php if (COUNT_POSTS) echo "$itemViews ".T_("times displayed").SEPARATOR;?>
		    <?php if (DISQUS!=""){ ?><a href="<?php echo $_SERVER["REQUEST_URI"];?>#disqus_thread">Comments</a><?php echo SEPARATOR;?> <?php }?>
			</div> -->
			
	<!-- ALL CUSTOM FIELDS -->
    <!--<div>-->
		

		<?php
			
		$item = cG("item");
		$queryCat="SELECT idCategory,idPost FROM ".TABLE_PREFIX."posts WHERE idPost = '$item'";
		$res												=	$ocdb->query($queryCat);	
		while($rowCat = mysql_fetch_assoc($res)) 
			$CatField										=	$rowCat[idCategory]; // CATEGORY ID OF THE POST AD	
		
		$queryFieldVal="SELECT idField,idPost,idAddData,FieldValue FROM ".TABLE_PREFIX."posts_ad_data WHERE idPost = '$item'";
		$resFieldVal										=	$ocdb->query($queryFieldVal);	
		$EditField											=	array();
		$r													=	0;
		while($rowFieldVal = mysql_fetch_assoc($resFieldVal)) {
			$EditField[$rowFieldVal[idField]]				=	$rowFieldVal;
			$EditIdFieldArr[$r]								=	$rowFieldVal[idField]; // ID FIELD ARRAY TO USE IN THE BELOW QUERY
			$r++;
		}
		
		$IdFieldStr											=	implode("','",$EditIdFieldArr); // ID FIELD STRING TO USE IN THE BELOW QUERY

		foreach($EditField as $EditKeyArr=>$EditValueArr) {
			$EditFinal[$EditValueArr[idField]][idPost]		=	$EditValueArr[idPost];
			$EditFinal[$EditValueArr[idField]][idAddData]	=	$EditValueArr[idAddData];
			$EditFinal[$EditValueArr[idField]][FieldValue]	=	$EditValueArr[FieldValue];
		}

		$queryOrder="SELECT idField,isMandatory,FieldOrder FROM ".TABLE_PREFIX."categories_field_mapping WHERE idCategory = '$CatField' and idField in ('".$IdFieldStr."') ORDER BY FieldOrder ASC";
		$resOrder											=	$ocdb->query($queryOrder);	
		$OrderField											=	array();
		$w													=	0;
		while($rowOrder = mysql_fetch_assoc($resOrder)) {
			$OrderField[$rowOrder[idField]]					=	$rowOrder;
			$OrderArr[$w]									=	$rowOrder[FieldOrder]; // FIELD ORDER ARRAY
			$w++;
		}

		$OrderStr											=	implode("','",$OrderArr); // FIELD STRING

		foreach($OrderField as $OrderKeyArr=>$OrderValueArr) {
			$OrderField[$OrderValueArr[idField]][idField]		=	$OrderValueArr[idField];
			$OrderField[$OrderValueArr[idField]][FieldOrder]	=	$OrderValueArr[FieldOrder];
			$OrderField[$OrderValueArr[idField]][isMandatory]	=	$OrderValueArr[isMandatory];
		}

		
		$queryEdit="SELECT IdField,FieldName,FieldActualName,FieldType,FieldValues FROM ".TABLE_PREFIX."custom_fields WHERE IdField in ('".$IdFieldStr."') and Active = 'Y'";		
		$resEdit											=	$ocdb->query($queryEdit);
		$CustomFields										=	array();
		while($rowEdit = mysql_fetch_assoc($resEdit)) {
			$CustomFields[$rowEdit[IdField]]				=	$rowEdit;
		}			
		foreach($CustomFields as $CusKeyArr=>$CusValueArr) {
			$Final[$OrderField[$CusValueArr[IdField]][FieldOrder]][IdField]			=	$CusValueArr[IdField];
			$Final[$OrderField[$CusValueArr[IdField]][FieldOrder]][FieldType]		=	$CusValueArr[FieldType];
			$Final[$OrderField[$CusValueArr[IdField]][FieldOrder]][FieldValues]		=	$CusValueArr[FieldValues];
			$Final[$OrderField[$CusValueArr[IdField]][FieldOrder]][FieldName]		=	$CusValueArr[FieldName];
			$Final[$OrderField[$CusValueArr[IdField]][FieldOrder]][FieldActualName]		=	$CusValueArr[FieldActualName];
			$Final[$OrderField[$CusValueArr[IdField]][FieldOrder]][idPost]			=	$EditFinal[$CusValueArr[IdField]][idPost];
			$Final[$OrderField[$CusValueArr[IdField]][FieldOrder]][idAddData]		=	$EditFinal[$CusValueArr[IdField]][idAddData];
			$Final[$OrderField[$CusValueArr[IdField]][FieldOrder]][FieldValue]		=	$EditFinal[$CusValueArr[IdField]][FieldValue];
			$Final[$OrderField[$CusValueArr[IdField]][FieldOrder]][FieldOrder]		=	$OrderField[$CusValueArr[IdField]][FieldOrder];
			$Final[$OrderField[$CusValueArr[IdField]][FieldOrder]][isMandatory]		=	$OrderField[$CusValueArr[IdField]][isMandatory];
		}
		$res = ksort($Final);4/16/2013
		?>			
		<?php $cnt = count($Final); if($cnt != 0) { if($itemPhone != '') { ?> 
				<?php } $v = 1; $r = 1; foreach($Final as $FinalKey=>$FinalValue) {		
				//if($FinalValue[FieldValue] != "" && $itemPhone != '') {
					if($itemPhone == '' && $z!=5){
							$z=5; $r = 3;
				}
					if($FinalValue[FieldValue] != "") {
					if($v != 2 && $v != 3 && $v != 4) {
						$LabelName									=	ucwords($FinalValue[FieldActualName]);
						$FieldValue									=	ucwords($FinalValue[FieldValue]); 		
						//echo $r."wewe";						
						
						?>
						
						<span class="viewSpan<?php if($r==3) { ?> viewBottom<?php } ?>"><?php _e($LabelName); ?> :		
							<?php _e($FieldValue); ?>
						</span>
				
						
				<?php 		//echo $r."rrt";
						
					}
				}
				
				if($r == 3) { 
					$r=0;
				}

				if(($FinalValue[FieldValue] != "") && ($v != 2 && $v != 3 && $v != 4)) { 
								$r++;
				}
				if($cnt == $v) { ?>
					</div>
				<?php }
				$v++;			
			}
			
		} 
			 
			if(($cnt == 0 && $itemPhone == '') || ($cnt == 0 && $itemPhone != '')) { ?>
					</div>
				<?php } 
		?>
		
		<?php $resEmail	=	$ocdb->query("SELECT EMAIL FROM ".TABLE_PREFIX."visitor WHERE USERID = '$_SESSION[ocAccount]'");
		$row		=	mysql_fetch_array($resEmail);
		$UserEmail	=	$row[EMAIL];
		?>
		
		<div class="uppercas">
		<?php echo $itemDescription; ?>
		</div>
		<?php if (MAX_IMG_NUM>0){
			if($itemImages != '') {?>
			<div id="pictures">
			<?php 
			foreach($itemImages as $img){
				echo '<a href="'.$img[0].'" title="'.$itemTitle.' '.T_('Picture').'" rel="gb_imageset['.$idItem.']">
				 		<img class="thumb" src="'.$img[1].'" title="'.$itemTitle.' '.T_('Picture').'" alt="'.$itemTitle.' '.T_('Picture').'" /></a>';
			}
			?>
			<div class="clear"></div>
		</div>

	    <?php } }?>
        <!-- AddThis Button BEGIN -->
        <div class="addthis_toolbox addthis_default_style">
        <a href="http://www.addthis.com/bookmark.php?v=250" class="addthis_button_compact"><?php _e("Share");?></a>
        <a class="addthis_button_facebook"></a>
        <a class="addthis_button_myspace"></a>
        <a class="addthis_button_google"></a>
        <a class="addthis_button_twitter"></a>
        <a class="addthis_button_print"></a>
        <a class="addthis_button_email"></a>
        <?php  if($itemEmail != $UserEmail) { echo SEPARATOR;?><a href="<?php echo SITE_URL."/".contactURL();?>?subject=<?php _e("Report bad use or Spam");?>: <?php echo $itemName." (".$idItem.")";?>"><?php _e("Report bad use or Spam");?></a> <?php } ?>
        </div>
        <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js"></script>
        <!-- AddThis Button END -->
	<!--</div>-->
  </div>
</div>
<div style="float:right;">
<?php if($errorMsg != '') { echo $errorMsg; } ?>
<div style="width:300px; <?php if($errorMsg == '') { ?> float:right; <?php } ?> background:#35CBFD">
  <?php 
  $diff = str_replace(SITE_URL,"",$_SERVER[HTTP_REFERER]);
  
  if($diff != $_SERVER[HTTP_REFERER]) 
  {
  if($itemEmail != $UserEmail) {
		
  if ($itemAvailable==1){ ?>
	

	<div id="contactmails" style="<?php if ($itemPhone!="") { ?> cursor:pointer; float:left; width:150px; height:39px;  <?php } else { ?> width:300px; height:33px;  <?php } ?>" <?php if ($itemPhone!="") { ?> onclick="mailshow('contactmails','contactsms','contactmail','contactmailsms','<img width=151px height=39px src=<?php echo SITE_URL; ?>/themes/<?php echo THEME; ?>/images/');" <?php } ?>><img border='0' <?php if ($itemPhone!="") { ?> width='151px' height='39px'  <?php } else { ?> width='300px' height='33px'  <?php } ?> src="<?php echo SITE_URL; ?>/themes/<?php echo THEME; ?>/images/<?php if ($itemPhone!="") { ?>replyemail1.jpg <?php } else { ?>sendreply.jpg <?php } ?>" /></div>
	<?php if ($itemPhone!="") { ?>
	<div id="contactsms" style="cursor:pointer; float:right; width:150px; height:39px;" onclick="smsshow('contactsms','contactmails','contactmailsms','contactmail','<img width=151px height=39px src=<?php echo SITE_URL; ?>/themes/<?php echo THEME; ?>/images/');"><img src="<?php echo SITE_URL; ?>/themes/<?php echo THEME; ?>/images/replysms.jpg" /></div>
	<?php } ?>
	<div id="contactmail" class="contactform form" >		
		<form method="post" action="" id="contactItem" onsubmit="return emailValidate(this);">
		<p class="<?php if ($itemPhone!="") { ?> padtop50  <?php } else { ?> padtop15  <?php } ?> padleft10">
		    <label><small><?php _e("Name");?></small></label>*
		    <span style="padding-left:1px;"><input id="name" name="name" type="text" class="ico_person" value="" maxlength="75" onblur="validateAdText(this); validateTextBox(this);" lang="false"  /></span>
			<span id="namespan" class='errortxt errorwidth'></span><br />
		</p>
		<p class="padtop10 padleft10">
            <label><small><?php _e("Email");?></small></label>*
		    <span style="padding-left:4px;"><input id="emailemail" name="emailemail"  class="ico_mail" type="text" maxlength="75" onblur="validateEmails(this);" lang="false"  /></span>
			<input id="itemId" name="itemId"  type="hidden" value="<?php echo $item; ?>" />
			<input id="itemTitle" name="itemTitle"  type="hidden" value="<?php echo $itemTitle; ?>" />
			<span id="emailemailspan" class='errortxt errorwidth'></span>
		</p>		
		<p class="padtop10 padleft10">
            <label><small><?php _e("Mobile");?></small></label>
		    <span style="padding-left:4px;"><input id="emailmobile" name="emailmobile"  class="ico_mobile" type="text" onkeypress="return isNumberKeyAd(event);" onblur="validMobile(this)" maxlength="10" lang="false"  /></span>
			<input id="itemId" name="itemId"  type="hidden" value="<?php echo $item; ?>" />
			<input id="itemTitle" name="itemTitle"  type="hidden" value="<?php echo $itemTitle; ?>" />		
			<span id="emailmobilespan" class='errortxt errorwidth'></span>
		</p>		
		<p class="padtop10 padleft10">
            <label><small><?php _e("Message");?></small></label>*<br />
		    <textarea maxlength="250" rows="5" cols="30" name="emailmsg" id="emailmsg" onblur="validateAdText(this); validateTextBox(this);"  lang="false"></textarea><br />
			<span id="emailmsgspan" class='errortxt errorwidth'></span>
		</p>
		<?php if (CAPTCHA){?>
		<!-- <p class="padtop10 padleft10">
            <label><small><?php  mathCaptcha($idItem."email");?></small></label>
		    <input id="emailmath" name="emailmath" type="text" size="2" maxlength="2"  onblur="validateTextBox(this); validateAdNumber(this);"  onkeypress="return isNumberKeyAd(event);" lang="false" />
            <br />
			<span id="emailmathspan" class='errortxt errorwidth'></span>
		</p> -->
		<?php }?>
        <p class="padleft10">
		<input type="hidden" name="contact" value="1" />
		<button type="submit" id="submit" style="border: 0; background: transparent; cursor:pointer;">
		<img src="<?php echo SITE_URL; ?>/themes/<?php echo THEME; ?>/images/send-button.jpg" width="100" heght="30" alt="submit" />
		</button>

		</p>
		</form> 
	</div>
	<div id="contactmailsms" class="contactform form" >		
		<form method="post" action="" id="contactItem" onsubmit="return smsValidate(this);">
		<!--<p style="padding-top:50px; padding-left:10px;">
		    <label><small><?php _e("Name");?></small></label>*
		    <span style="padding-left:5px;"><input id="name" name="name" type="text" class="ico_person" value="<?php echo cP("name");?>" maxlength="75" onblur="validateText(this);"  onkeypress="return isAlphaKey(event);" lang="false"  /></span><br />
		</p>-->
		<p class="padtop50 padleft10">
            <label><small><?php _e("Email");?></small></label>*
		    <span style="padding-left:4px;"><input id="smsemail" name="smsemail"  class="ico_mail" type="text" maxlength="75" onblur="validateEmails(this);" lang="false"  /></span>
			<input id="itemId" name="itemId"  type="hidden" value="<?php echo $item; ?>" />
			<input id="itemTitle" name="itemTitle"  type="hidden" value="<?php echo $itemTitle; ?>" />
			<span id="smsemailspan" class='errortxt errorwidth'></span>
		</p>
		<p class="padtop10 padleft10">
            <label><small><?php _e("Mobile");?></small></label>
		    <span style="padding-left:4px;"><input id="smsmobile" name="smsmobile"  class="ico_mobile" type="text" onkeypress="return isNumberKeyAd(event);" onblur="validMobile(this)" maxlength="10" lang="false"  /></span>
			<input id="itemId" name="itemId"  type="hidden" value="<?php echo $item; ?>" />
			<input id="itemTitle" name="itemTitle"  type="hidden" value="<?php echo $itemTitle; ?>" />
			<span id="smsmobilespan" class='errortxt errorwidth'></span>
		</p>
		<p class="padtop10 padleft10"
            <label><small><?php _e("Message");?></small></label>*<br />
		    <textarea maxlength="80" rows="5" cols="30" name="smsmsg" id="smsmsg" onkeypress='return validateChar(this);' onblur="validateAdText(this); clearspan(this); validateTextBox(this);"  lang="false"></textarea><br />
			<span id="smsmsgspan" class='errortxt errorwidth'></span>
		</p>
		<?php if (CAPTCHA){?>
		<!-- <p class="padtop10 padleft10">
            <label><small><?php  mathCaptcha($idItem."sms");?></small></label>
		    <input id="smsmath" name="smsmath" type="text" size="2" maxlength="2"  onblur="validateTextBox(this); validateAdNumber(this);" onkeypress="return isNumberKeyAd(event);" lang="false" />          
            <br />
			<span id="smsmathspan" class='errortxt errorwidth'></span>
		</p>-->	
		<?php }?>
        <p class="padleft10">
		<input type="hidden" name="contact" value="1" />
		<button type="submit" id="submit" style="border: 0; background: transparent; cursor:pointer;">
		<img src="<?php echo SITE_URL; ?>/themes/<?php echo THEME; ?>/images/send-button.jpg" width="100" heght="30" alt="submit" />
		</button>
	</p>
		</form> 
	</div>
	<?php } else echo "<div id='sysmessage'>".T_("This Ad is no longer available")."</div>";?>
	<?php 
	} } ?>
	<!--<span style="padding-left:10px; cursor:pointer;" onclick="openClose('remembermail');"> <?php _e("Send me an email with links to manage my Ad");?></span><br />-->
	<div style="display:none;" id="remembermail" >
		<form method="post" action="" id="remember" onsubmit="return checkForm(this);">
		<p style="padding-left:10px;">
        	<input type="hidden" name="remember" value="1" />
		<input onblur="this.value=(this.value=='') ? 'email' : this.value;" 
				onfocus="this.value=(this.value=='email') ? '' : this.value;" 
		id="emailR" name="emailR" type="text" value="email" maxlength="120" onblur="validateEmail(this);" lang="false"  />
		<input id="itemTitle" name="itemTitle"  type="hidden" value="<?php echo $itemTitle; ?>" />
			<input type="submit" class="but" value="<?php _e("Remember");?>" />
        </p>
		</form> 
	</div>
	<?php if (DISQUS!=""){ ?>
		<?php if (DEBUG){ ?><script type="text/javascript"> var disqus_developer = 1;</script><?php } ?>
	
	<div id="disqus_thread"></div><script type="text/javascript" src="http://disqus.com/forums/<?php echo DISQUS;?>/embed.js"></script>
	<noscript><a href="http://disqus.com/forums/<?php echo DISQUS;?>/?url=ref">View the discussion thread.</a></noscript>
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
</div>
</div>