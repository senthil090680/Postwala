<?php
#============================================================================================================
# Author 		: J P Senthil Kumar
# Start Date	: 20 May 2011
# End Date		: 24 May 2011
# Project		: Publish A New Ad
# Description	: This file is used to add a new advertisement with the custom fields coming dynamically
#============================================================================================================

require_once('../includes/header.php');
if (file_exists(SITE_ROOT.'/themes/'.THEME.'/item-new.php')){//item-new from the theme!
	require_once(SITE_ROOT.'/themes/'.THEME.'/item-new.php');
}
else{//not found in theme

unset($_SESSION['publishadurl']);

if(cG('email') != ''){
	$email = cG('email');
    $password = cG('pass');
	$password = base64_decode($password);
	
	$account = new Account($email);		
	$account->logOn($password,$rememberme,"ocEmail");
	redirect(SITE_URL.newURL());
}
if (LOGON_TO_POST){
    $account = Account::createBySession();
    if ($account->exists){
        $name = $account->name;
        $email = $account->email;
    } 
    else { 	
		$_SESSION['publishadurl'] = curPageURL();
		redirect(accountLoginURL()."?nofilter=1"); 
	}
}





if (!isInSpamList($client_ip)){//no spammer
	require_once('../includes/classes/resize.php');
	if ($_POST){	
		newPost();
	}//if post
	

/*if (!isset($idCategory) ||  cG("type") == "")
{

	 $redirURL = SITE_URL.'/content/item-new-select-cat.php' ;
     if(cG("category") != "")
     $redirURL .= "?category=". cG("category") ;
     redirect($redirURL);
}*/


?>
<!--<div class="sectionTitle"><?php _e("Publish a new Ad on category ");?>  <b> <?php echo $categoryName;?> </b> 
<small> <a href="<?php echo SITE_URL.'/content/item-new-select-cat.php'; ?>">  (Change) </a> </small> </div>-->

<form action="" method="post" onsubmit="return checkFormEx(this);" enctype="multipart/form-data" name="postad">
	
	<?php echo "<input type='hidden' name='category' value='$idCategory' />" ?>
	<?php echo "<input type=\"hidden\" name=\"type\" value=\"". cG("type") ."\" />" ?>
	<br />	
	
	<?php	$SITE_URL									=	SITE_URL; ?>

	<!-- CATEGORY AND OFFER DETAILS -->
	<h3><?php _e("Select a Category to Publish a new Ad");?> </h3>
		
		<div class='pfdivlt smalltxt fleft tlright'><?php _e("Category");?><font class="clr3">*</font> 
		<?php echo "<img align='absmiddle' title='Select the category that best suits your advertisement' src='$SITE_URL/images/information.png'  alt=''>"; ?>
		</div>
		<div class="pfdivrt smalltxt fleft">
		<?php 
			if(cG("category")) { 
			$catValue = cG("category"); 
			$query="select idCategory from ".TABLE_PREFIX."categories where friendlyName='$catValue' Limit 1";
			$selectedCategory = $ocdb->getValue($query);
		} 
		$category =	"cus";
		if (PARENT_POSTS){
			$query="SELECT idCategory,name,(select name from ".TABLE_PREFIX."categories where idCategory=C.idCategoryParent) FROM ".TABLE_PREFIX."categories C order by idCategoryParent, `order`";
			sqlOptionGroup($query,"category",$selectedCategory,$category);
		}
		else{
			$query="SELECT idCategory,name,(select name from ".TABLE_PREFIX."categories where idCategory=C.idCategoryParent) 
				FROM ".TABLE_PREFIX."categories C where C.idCategoryParent!=0 order by idCategoryParent, `order`";
			sqlOptionGroup($query,"category",$selectedCategory,$category);
		}
		?>
		<span id='categoryspan' class='errortxt errorwidth'></span></div><br clear="all"/>

		<!-- <br />
	        <?php _e("Type");?>:
		<select id="type" name="type" class='borderedge'>
			<option value="<?php echo TYPE_OFFER;?>"><?php _e("offer");?></option>
			<option value="<?php echo TYPE_NEED;?>"><?php _e("need");?></option>
		</select>
		<br /> 
	
	<input type="submit" id="submit" class="but" value="<?php _e("Go >>");?>" />
	</form> -->
		
	<!-- END OF CATEGORY AND OFFER DETAILS -->
	


    <?php if (LOCATION){?>
    <div class='pfdivlt smalltxt fleft tlright'><?php _e("City");?><font class="clr3">*</font> 
	<?php echo "<img align='absmiddle' title='The City where your requirement stands' src='$SITE_URL/images/information.png'  alt=''>"; ?>
	</div>
	<?php if(cG("location")) { 
		$locaValue = cG("location"); 
		$query="select idLocation from ".TABLE_PREFIX."locations where name='$locaValue' Limit 1";
		$selectedLocation = $ocdb->getValue($query);
	} ?>
	<div class="pfdivrt smalltxt fleft">
    <?php $query="SELECT idLocation, name, 
                (SELECT name
		FROM classifieds_locations
		WHERE idLocation = C.idLocationParent )
            FROM classifieds_locations C
	    WHERE idLocationParent != 0
	    ORDER BY idLocationParent, idLocation";
	    
	echo sqlOptionGroup($query,"location",$selectedLocation); ?>
	<span id='locationspan' class='errortxt errorwidth'></span></div><br clear="all"/>	
    <?php }?>
	<!-- <div class='pfdivlt smalltxt fleft tlright'><?php _e("Title");?><font class="clr3">*</font> 
	<?php echo "<img align='absmiddle' title='Enter the title that best suits your advertisement' src='$SITE_URL/images/information.png'  alt=''>"; ?>
	</div>
	<div class="pfdivrt smalltxt fleft"><input class='borderedge' id="title" name="title" type="text" value="<?php echo $_POST["title"];?>" size="61" maxlength="120" onblur="validateTextBox(this); validateAdText(this);"  lang="false" /><span id='titlespan' class='errortxt errorwidth'></span></div><br clear="all"/>-->
	
	<?php //echo CURRENCY;?>
	<!-- <input id="price" name="price" type="text" size="3" value="<?php echo $_POST["price"];?>" maxlength="25"  /><br />-->
		
	
	
	<!-- ALL DYNAMIC CUSTOM FIELDS -->
	<div id="dycustom" style="display:none;">
	

	</div>
	<!-- END OF ALL DYNAMIC CUSTOM FIELDS -->


<script type="text/javascript" src= "<?php echo SITE_URL; ?>/content/jscripts/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
	tinyMCE.init({
		// General options
		mode : "textareas",
		theme : "advanced",
		plugins : "",

		// Theme options
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "",
		theme_advanced_buttons3 : "",
		theme_advanced_buttons4 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,

		// Example content CSS (should be your site CSS)
		content_css : "css/content.css",

		// Drop lists for link/image/media/template dialogs
		template_external_list_url : "lists/template_list.js",
		external_link_list_url : "lists/link_list.js",
		external_image_list_url : "lists/image_list.js",
		media_external_list_url : "lists/media_list.js",

		// Style formats
		style_formats : [
			{title : 'Bold text', inline : 'b'},
			{title : 'Red text', inline : 'span', styles : {color : '#ff0000'}},
			{title : 'Red header', block : 'h1', styles : {color : '#ff0000'}},
			{title : 'Example 1', inline : 'span', classes : 'example1'},
			{title : 'Example 2', inline : 'span', classes : 'example2'},
			{title : 'Table styles'},
			{title : 'Table row 1', selector : 'tr', classes : 'tablerow1'}
		],

		// Replace values for the template plugin
		template_replace_values : {
			username : "Some User",
			staffid : "991234"
		}
	});
</script>
	
	 <div class='pfdivlt smalltxt fleft tlright'><?php _e("Description");?><font class="clr3">*</font> 
	<?php echo "<img align='absmiddle' title='Give a clear description of your Requirement/Advertisement' src='$SITE_URL/images/information.png'  alt=''>"; ?>
	</div>
		<?php if (HTML_EDITOR){?>
		<div class="pfdivrtaddl smalltxt fleft" id="descfocus"><textarea rows="10" cols="73" name="description" id="description" onblur="validateTextBox(this); validateTextBox(this);" lang="false"><?php echo stripslashes($_POST['description']);?></textarea>
		

	<?php }
	    else{?>
		<div class="pfdivrtaddl smalltxt fleft" id="descfocus"><textarea class='borderedge bordercolor' rows="10" cols="73" name="description" id="description" onblur="validateAdText(this); validateTextBox(this);" lang="false"><?php echo strip_tags($_POST['description']);?></textarea><?php }?>
	<span id='descriptionspan' class='errortxt errorwidth'></span></div><br clear="all"/><br/>

	<!-- <div class='pfdivlt smalltxt fleft tlright'><?php _e("Your Name");?><font class="clr3">*</font>  
	<?php echo "<img align='absmiddle' title='Your name comes here' src='$SITE_URL/images/information.png'  alt=''>"; ?></div>
	<div class="pfdivrt smalltxt fleft"><input id="name" name="name" class='borderedge' type="text" value="<?php if ($_POST) echo $_POST["name"]; else echo $name;?>" maxlength="75" onblur="validateTextBox(this); validateAdText(this);" lang="false"  /><span id='namespan' class='errortxt errorwidth'></span></div><br/><br clear="all"/>-->
	
    <?php if ($email!=""){?>
	<div class='pfdivlt smalltxt fleft tlright'><?php echo T_("Your E-mail :"); ?>
	</div><div class="pfdivrt smalltxt fleft">
    <input type="text" class='borderedge boxwidth' value="<?php echo $email;?>" disabled /><input id="email" name="email" type="hidden" value="<?php echo $email;?>" /></div><br/><br clear="all"/>
    <?php } else { ?>
	<div class='pfdivlt smalltxt fleft tlright'>
       <?php echo T_("Email (not published)"); ?><font class="clr3">*</font>
    </div>
	<div class="pfdivrt smalltxt fleft"><input id="email" name="email" type="text" class='borderedge' value="<?php echo $_POST["email"];?>" maxlength="120" onblur="validateEmail(this);" /></div><br/><br clear="all"/>
    <?php }?><br/>
	 <div class='pfdivlt smalltxt fleft tlright'><?php _e("Mobile number");?>:
	</div><div class="pfdivrt smalltxt fleft"><input id="phone" name="phone" class='borderedge' type="text" value="<?php echo $_POST["phone"];?>" maxlength="11" /></div><br/><br clear="all"/> 
	<?php if (VIDEO){?>
    	<div class='pfdivlt smalltxt fleft tlright'><span style="cursor:pointer;" onclick="youtubePrompt();"><?php _e("YouTube video");?></span>:</div><div class="pfdivrt smalltxt fleft">
    	<input id="video" name="video" class='borderedge' type="text" value="<?php echo $_POST["video"];?>" onclick="youtubePrompt();" size="40" /></div><br/><br clear="all"/>
    	<div id="youtubeVideo"></div>
	<?php } ?>

	<!--<div class='pfdivlt smalltxt fleft tlright'><?php _e("Place");?>:</div><div class="pfdivrt smalltxt fleft">
	<?php if (MAP_KEY==""){//not google maps?>
	<input id="place" name="place" class='borderedge' type="text" value="<?php echo $_POST["place"];?>" size="69" maxlength="120" /></div><br/><br clear="all"/>
	<?php }
	else{//google maps
		if ($_POST["place"]!="") $m_value=$_POST["place"];
		else $m_value=MAP_INI_POINT;
	?>
	<input id="place" name="place" type="text" class='borderedge' value="<?php echo $m_value;?>" onblur="showAddress(this.value);" size="69" maxlength="120" /></div><br/><br clear="all"/>
	<div id="map" style="width: 100%; height: 200px;"></div>
	<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?php echo MAP_KEY;?>" type="text/javascript"></script>
	<script type="text/javascript">var init_street="<?php echo MAP_INI_POINT;?>";</script>
	<script type="text/javascript" src="<?php echo SITE_URL;?>/includes/js/map.js"></script>
	<script type="text/javascript" src="<?php echo SITE_URL;?>/includes/js/mapSmall.js"></script>
	<?php }?>
    <br />-->
	<?php 
	if (MAX_IMG_NUM>0){
		echo "<input type='hidden' name='MAX_FILE_SIZE' value='".MAX_IMG_SIZE."' /><br />"; ?>		
		<?php echo T_("Upload pictures - Max file size").": ".(MAX_IMG_SIZE/1000000)."Mb & ".T_("Format")." : ".IMG_TYPES."<br/>"; ?>
		
		<?php for ($i=1;$i<=MAX_IMG_NUM;$i++){?>
			<div class='pfdivlt smalltxt fleft tlright'><?php _e("Picture");?> <?php echo $i?></div><div class="pfdivrt smalltxt fleft"><input type="file" class='borderedge' name="pic<?php echo $i?>" id="pic<?php echo $i?>" value="<?php echo $_POST["pic".$i];?>" /></div><br/><br clear="all"/>
	<?php } ?>
	
	<?php }
	?>
	<br />
	<?php if (CAPTCHA){ ?>
		<div class='pfdivlt smalltxt fleft tlright'><?php mathCaptcha('newitem');?><font class="clr3">*</font>
		<?php echo "<img align='absmiddle' title='Please add these numbers' src='$SITE_URL/images/information.png'  alt=''>"; ?>
	</div><div class="pfdivrt smalltxt fleft"><p><input id="math" name="math" class='borderedge' type="text" size="2" maxlength="2"  onkeypress="return isNumberKeyAd(event);" onblur="validateTextBox(this); validateAdNumber(this);" lang="false" /><span id='mathspan' class='errortxt errorwidth'></span></p></div><br/><br clear="all"/>
	<br /><?php }?>
	<div id="dyads" style="display:none;"><input type="checkbox" class="feature checkbox" name="featured_ad" id="featured_ad" value="Yes" onClick="featured_adOnClick()" style="width: 19px;"></div>
		<script language=javascript>
		function featured_adOnClick()
		{
			if(document.getElementById('featured_ad').checked)
			{
				for (i=0;i<document.postad.AdTypeGroup.length;i++)
				{
					document.postad.AdTypeGroup[i].disabled=false;
                                }
				for (i=0;i<document.postad.BatchGroup.length;i++)
				{
					document.postad.BatchGroup[i].disabled=false;
				}
				document.getElementById('submit').value="Pay & Post it"	;	
			}
			else
			{
				for (i=0;i<document.postad.AdTypeGroup.length;i++)
				{
					document.postad.AdTypeGroup[i].checked=false;
					document.postad.AdTypeGroup[i].disabled=true;
                                }
				for (i=0;i<document.postad.BatchGroup.length;i++)
				{
				 	document.postad.BatchGroup[i].checked=false;
					document.postad.BatchGroup[i].disabled=true;
				}	
				document.getElementById('submit').value="Post it"	;	
                        }		
		}
		
		function checkFormEx(form)
		{
			var ok = true;
			
			if(ok && document.getElementById('featured_ad').checked)
			{
				var adTypeSelected = false;
				var adBatchSelected = false;
				for (i=0;i<document.postad.AdTypeGroup.length;i++)
				{
					if(document.postad.AdTypeGroup[i].checked) { adTypeSelected = true; break; }
								}
				for (i=0;i<document.postad.BatchGroup.length;i++)
				{
					if(document.postad.BatchGroup[i].checked)  { adBatchSelected = true; break; }
				}
				
				if(adTypeSelected == false)
				{
					ok = false;
					alert("Select the premium ad option!");
				}
				if(adBatchSelected == false)
				{
					ok = false;
					alert("Select the favorite tag for your ad!");
				}
										
			}
			
			if(ok) ok = ValidateReg(form);
			
			return ok;					
		}
		</script>

	<?php
	//}	
	?>
	<p style="padding-left:170px;"><button type="submit" id="submit" style="border: 0; background: transparent">
		<img src="<?php echo SITE_URL; ?>/images/Post-ad-New.jpg" width="106" heght="51" alt="submit" />
	</button><p>
</form>
<?php
}
else {//is spammer
	alert(T_("NO Spam!"));
	jsRedirect(SITE_URL);
}

}//if else
require_once('../includes/footer.php');
?>