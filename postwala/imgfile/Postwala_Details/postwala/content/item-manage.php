<?php
#============================================================================================================
# Author 		: J P Senthil Kumar
# Start Date	: 24 May 2011
# End Date		: 25 May 2011
# Project		: Publish A New Ad
# Description	: This file is used to add a new advertisement with the custom fields coming dynamically
#============================================================================================================

require_once('../includes/header.php');
require_once('../includes/classes/resize.php');

if (file_exists(SITE_ROOT.'/themes/'.THEME.'/item-manage.php')){//item-manage from the theme!
	require_once(SITE_ROOT.'/themes/'.THEME.'/item-manage.php'); 
}
else{//not found in theme

?>
<?php
if (cG("pwd")&&is_numeric(cG("post"))){//delete ,activate or deactivate
	$action=cG("action");
	$post_password=cG("pwd");
	$post_id=cG("post");
	$edited=cG("edited");
	
	if ($action=="confirm" && !PAYPAL_ACTIVE){//confirm a new post, if paypal enabled you can't confirm from here
	    if(isset($_SESSION['admin'])) {  //only for admin can confirm
		confirmPost($post_id,$post_password,$edited);
	    }
	    else
	       echo "Only Postwala Classifieds Admin can confirm your post" ;
	}	
	elseif ($action=="deactivate"){
		deactivatePost($post_id,$post_password);
	}
	elseif ($action=="activate"){
		activatePost($post_id,$post_password);
	}
	elseif ($action=="delete"&&(isset($_SESSION['admin']) || isset($_SESSION['ocAccount']))){
		deletePost($post_id,$post_password);
	}
	elseif ($action=="spam"&&isset($_SESSION['admin'])){//only for admin mark as spam
		spamPost($post_id,$post_password);
	}
	elseif ($action=="edit"){//edit post
		if ($_POST){//update post
			editPost($post_id,$post_password);
		}

		$query="select p.*,friendlyName,c.name cname,p.description,
		        (select friendlyName from ".TABLE_PREFIX."categories where idCategory=c.idCategoryParent limit 1) parent
				    from ".TABLE_PREFIX."posts p 
				    inner join ".TABLE_PREFIX."categories c
				    on c.idCategory=p.idCategory
				where idPost=$post_id and password='$post_password' and isAvailable!=2 Limit 1";
		$result=$ocdb->query($query);
		if (mysql_num_rows($result)){
			$row=mysql_fetch_assoc($result);
			
            if (VIDEO){
                $description_with_video=$row['description'];
                $descriptionParts=explode('[youtube=',$description_with_video);
                $videoParts=explode(']',$descriptionParts[1]);
                $row['description']=$descriptionParts[0];
                $row["video"]=$videoParts[0];
            }       
            
			if($row['isConfirmed']!=1) {//the ad is not confirmed!
				$linkConfirm=SITE_URL."/manage/?post=$post_id&pwd=$post_password&action=confirm&edited=$edited";
				echo "<b><a href='$linkConfirm'>".T_("To confirm your Ad click here")."</a></b><br />";
			}
			
			if($row['isAvailable']==1){//able to deactivate it
				$linkDeactivate=SITE_URL."/manage/?post=$post_id&pwd=$post_password&action=deactivate";
				echo "<a href='$linkDeactivate'>".T_("If this Ad is no longer available please click here")."</a>";
			}
			else {//activate it
				$linkActivate=SITE_URL."/manage/?post=$post_id&pwd=$post_password&action=activate";
				echo "<a href='$linkActivate'>".T_("Activate")."</a>";
			}
			
			$postTitle=$row["title"];
			$postTitleF=friendly_url($postTitle);
			$postTypeName=getTypeName($row["type"]);
			$fcategory=$row["friendlyName"];
			$parent=$row["parent"];
			$insertDate=setDate($row['insertDate']);
			
			$postUrl=itemURL($post_id,$fcategory,$postTypeName,$postTitleF,$parent);	

			 //ocaku update post
			if (OCAKU && $_POST && $action=="edit" ){
				$ocaku=new ocaku();
				
				if ($row["hasImages"]==1){//images
					$itemImages=getPostImages($post_id,$insertDate);//getting the images
					$numImages=count($itemImages);
					if ($numImages>0) $imagePost=$itemImages[0][1];//thumb
					else $imagePost='';
				}
				
				if (LOCATION) $oplace=getLocationName(cP("location"));
				else  $oplace=cP("place");
				
				$data=array(
					'KEY'=>OCAKU_KEY,
					'idPostInClass'=>$post_id,
					'Category'=>$row["cname"],
					'Place'=>$oplace,
					'URL'=>SITE_URL.$postUrl,
					'type'=>$postTypeName,
					'title'=>$postTitle,
					'description'=>$row["description"],
					'name'=>$row["name"],
					'price'=>$row["price"],
					'currency'=>CURRENCY,
					'language'=>substr(LANGUAGE,0,2),
					'image'=>$imagePost,
					'num_images'=>$numImages
					);
				$ocaku->updatePost($data);
				unset($ocaku);
			}
			//end ocaku
			?>
<h3><?php _e("Edit Ad");?>: <a target="_blank" href="<?php echo SITE_URL.$postUrl;?>"><?php echo $postTitle;?></a></h3>
<form action="" method="post" onsubmit="return ValidateReg(this);" enctype="multipart/form-data">
	<?php	$SITE_URL									=	SITE_URL; ?>
	<div class='pfdivlt smalltxt fleft tlright'><?php _e("Category");?><font class="clr3">*</font>
	<?php echo "<img align='absmiddle' title='Select the category that best suits your advertisement' src='$SITE_URL/images/information.png'  alt=''>"; ?>
	</div><div class="pfdivrt smalltxt fleft">
	<?php 
	$query="SELECT idCategory,name,(select name from ".TABLE_PREFIX."categories where idCategory=C.idCategoryParent) FROM ".TABLE_PREFIX."categories C order by idCategoryParent, `order`";
	sqlOptionGroup($query,"category",$row["idCategory"]);
	?>
	<span id='categoryspan' class='errortxt errorwidth'></span></div><br clear="all"/>
	<!-- <div class='pfdivlt smalltxt fleft tlright'><?php _e("Type");?><font class="clr3">*</font>
	<?php echo "<img align='absmiddle' title='The Location where your requirement stands' src='$SITE_URL/images/information.png'  alt=''>"; ?>
	</div><div class="pfdivrt smalltxt fleft">
	<select id="type" name="type" class='borderedge' onBlur="validateSelect(this); validateAdText(this);" onChange="validateAdNumber(this);" lang=false>
		<option value=''></option>
		<option value="<?php echo TYPE_OFFER;?>" <?php if($row['type']==TYPE_OFFER)echo 'selected="selected"';?> ><?php _e("offer");?></option>
		<option value="<?php echo TYPE_NEED;?>"  <?php if($row['type']==TYPE_NEED)echo 'selected="selected"';?> ><?php _e("need");?></option>
	</select>
	<span id='typespan' class='errortxt errorwidth'></span></div><br clear="all"/>	-->
    <?php if (LOCATION){?>
    <div class='pfdivlt smalltxt fleft tlright'>
	<?php _e("City");?><font class="clr3">*</font> 
	<?php echo "<img align='absmiddle' title='The City where your requirement stands' src='$SITE_URL/images/information.png'  alt=''>"; ?>
	</div><div class="pfdivrt smalltxt fleft">
	<?php  $query="SELECT idLocation,name,(select name from ".TABLE_PREFIX."locations where idLocation=C.idLocationParent) FROM ".TABLE_PREFIX."locations C where idLocationParent != 0 order by idLocationParent, idLocation";
	echo sqlOptionGroup($query,"location",$row["idLocation"]); ?>	
	<span id='locationspan' class='errortxt errorwidth'></span></div><br clear="all"/>
    <?php }?>	
	
	<!--<div class='pfdivlt smalltxt fleft tlright'>
	<?php _e("Place");?><font class="clr3">*</font>
	<?php echo "<img align='absmiddle' title='The Location where your requirement stands' src='$SITE_URL/images/information.png'  alt=''>"; ?>
	</div><div class="pfdivrt smalltxt fleft">
	<?php if (MAP_KEY==""){//not google maps?>
	<input id="place" name="place" class='borderedge' type="text" value="<?php echo $row["place"];?>" size="69" maxlength="120" onblur="validateTextBox(this); validateAdText(this);" /><span id='placespan' class='errortxt errorwidth'></span></div><br clear="all"/>
	<?php }
	else{//google maps
		if ($_POST["place"]!="") $m_value=$_POST["place"];
		elseif($row["place"]!="") $m_value=$row["place"];
		else $m_value=MAP_INI_POINT;
	?>
	<input id="place" name="place" type="text" class='borderedge' value="<?php echo $m_value;?>" onblur="showAddress(this.value); validateTextBox(this); validateAdText(this);" lang="false" size="69" maxlength="120" /><span id='placespan' class='errortxt errorwidth'></span></div><br clear="all"/>
	<div id="map" style="width: 100%; height: 200px;"></div>
	<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?php echo MAP_KEY;?>" type="text/javascript"></script>
	<script type="text/javascript">var init_street="<?php echo MAP_INI_POINT;?>";</script>
	<script type="text/javascript" src="<?php echo SITE_URL;?>/includes/js/map.js"></script>
	<script type="text/javascript" src="<?php echo SITE_URL;?>/includes/js/mapSmall.js"></script>
	<script type="text/javascript">showAddress("<?php echo $m_value;?>");</script>
	<?php }?>
	-->
	<!-- <div class='pfdivlt smalltxt fleft tlright'>
	<?php _e("Title");?><font class="clr3">*</font>
	<?php echo "<img align='absmiddle' title='Enter the title that best suits your advertisement' src='$SITE_URL/images/information.png'  alt=''>"; ?>
	</div><div class="pfdivrt smalltxt fleft">	
	<input id="title" name="title" class='borderedge' type="text" value="<?php echo $postTitle;?>" size="61" maxlength="120" onblur="validateTextBox(this); validateAdText(this);"  lang="false" />
	<?php //echo CURRENCY;?>
	 <input id="price" name="price" type="text" size="3" value="<?php echo $row["price"];?>" maxlength="25"   />
	<span id='titlespan' class='errortxt errorwidth'></span></div><br clear="all"/> -->
	
	  

	<?php
	
	
	$queryCat="SELECT idCategory,idPost FROM ".TABLE_PREFIX."posts WHERE idPost = '$post_id'";
	$res												=	$ocdb->query($queryCat);	
	while($rowCat = mysql_fetch_assoc($res)) 
		$CatField										=	$rowCat[idCategory]; // CATEGORY ID OF THE POST AD	
	
	$queryFieldVal="SELECT idField,idPost,idAddData,FieldValue FROM ".TABLE_PREFIX."posts_ad_data WHERE idPost = '$post_id'";
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

	
	$queryEdit="SELECT IdField,FieldName,FieldActualName,FieldToolTip,FieldSize,FieldLength,FieldType,FieldValues FROM ".TABLE_PREFIX."custom_fields WHERE IdField in ('".$IdFieldStr."') and Active = 'Y'";		
	$resEdit											=	$ocdb->query($queryEdit);
	$CustomFields										=	array();
	while($rowEdit = mysql_fetch_assoc($resEdit)) {
		$CustomFields[$rowEdit[IdField]]				=	$rowEdit;
	}			
	foreach($CustomFields as $CusKeyArr=>$CusValueArr) {
		$Final[$OrderField[$CusValueArr[IdField]][FieldOrder]][IdField]			=	$CusValueArr[IdField];
		$Final[$OrderField[$CusValueArr[IdField]][FieldOrder]][FieldType]		=	$CusValueArr[FieldType];
		$Final[$OrderField[$CusValueArr[IdField]][FieldOrder]][FieldActualName]	=	$CusValueArr[FieldActualName];
		$Final[$OrderField[$CusValueArr[IdField]][FieldOrder]][FieldToolTip]	=	$CusValueArr[FieldToolTip];
		$Final[$OrderField[$CusValueArr[IdField]][FieldOrder]][FieldSize]		=	$CusValueArr[FieldSize];
		$Final[$OrderField[$CusValueArr[IdField]][FieldOrder]][FieldLength]		=	$CusValueArr[FieldLength];
		$Final[$OrderField[$CusValueArr[IdField]][FieldOrder]][FieldValues]		=	$CusValueArr[FieldValues];
		$Final[$OrderField[$CusValueArr[IdField]][FieldOrder]][FieldName]		=	$CusValueArr[FieldName];
		$Final[$OrderField[$CusValueArr[IdField]][FieldOrder]][idPost]			=	$EditFinal[$CusValueArr[IdField]][idPost];
		$Final[$OrderField[$CusValueArr[IdField]][FieldOrder]][idAddData]		=	$EditFinal[$CusValueArr[IdField]][idAddData];
		$Final[$OrderField[$CusValueArr[IdField]][FieldOrder]][FieldValue]		=	$EditFinal[$CusValueArr[IdField]][FieldValue];
		$Final[$OrderField[$CusValueArr[IdField]][FieldOrder]][FieldOrder]		=	$OrderField[$CusValueArr[IdField]][FieldOrder];
		$Final[$OrderField[$CusValueArr[IdField]][FieldOrder]][isMandatory]		=	$OrderField[$CusValueArr[IdField]][isMandatory];
	}
	$res = ksort($Final);
	?>
	<!-- ALL DYNAMIC CUSTOM FIELDS -->
	<?php	$CustomRows									=	count($Final);
			$m = 1;
			foreach($Final as $FinalKey=>$FinalValue) {
			$FieldValue									=	explode(',',$FinalValue[FieldValues]);
			$LabelName									=	ucwords($FinalValue[FieldActualName]);
	?>
		
	<?php if($FinalValue[FieldType] != 4 && $FinalValue[FieldType] != 5) { ?>
			<div class='pfdivlt smalltxt fleft tlright'>		
	<?php _e($LabelName); if($FinalValue[isMandatory] == 'Y') { ?><font class="clr3">*</font> <?php } ?>: 
	<?php } ?>
	<?php	if($FinalValue[FieldType] == 1) { ?>				
				&nbsp;<img align='absmiddle' title='<?php echo $FinalValue[FieldToolTip]; ?>' src='<?php echo $SITE_URL; ?>/images/information.png'  alt=''></div>
				<div class="pfdivrt smalltxt fleft"><?php $ValidPrice = stristr($LabelName,"price"); if($ValidPrice) { ?><span>Rs.</span> <?php } $ValidSalary = stristr($LabelName,"salary"); if($ValidSalary) { ?><span>Rs.</span><?php } ?><input title='<?php echo $FinalValue[FieldToolTip]; ?>' type='text' maxlength='<?php echo $FinalValue[FieldLength]; ?>' name='CustomField<?php echo $m; ?>' id='CustomField<?php echo $m; ?>' class='borderedge' style="width:<?php echo $FinalValue[FieldSize]; ?>px;" value='<?php echo $FinalValue[FieldValue]; ?>'
				<?php if($FinalValue[isMandatory] == 'Y') { ?>
					onblur="CustomText(this,'<?php echo $LabelName; ?>'); validateAdText(this);" lang='custom'
				<?php } ?>
				echo "maxlength='120' labelValue='<?php echo $LabelName; ?>'/><span id='CustomField<?php echo $m; ?>span' class='errortxt errorwidth'></span></div><br clear="all"/> </span>
			<?php } 
			elseif($FinalValue[FieldType] == 6) { ?>				
				&nbsp;<img align='absmiddle' title='<?php echo $FinalValue[FieldToolTip]; ?>' src='<?php echo $SITE_URL; ?>/images/information.png' alt=''></div>
				<div class="pfdivrt smalltxt fleft"><?php $ValidPrice = stristr($LabelName,"price"); if($ValidPrice) { ?><span>Rs.</span> <?php } $ValidSalary = stristr($LabelName,"salary"); if($ValidSalary) { ?><span>Rs.</span><?php } ?><span><input type='text' title='<?php echo $FinalValue[FieldToolTip]; ?>' name='CustomField<?php echo $m; ?>' id='CustomField<?php echo $m; ?>' maxlength='<?php echo $FinalValue[FieldLength]; ?>' class='borderedge' value='<?php echo $FinalValue[FieldValue]; ?>'
				<?php if($FinalValue[isMandatory] == 'Y') { ?>
					onBlur="CustomText(this,'<?php echo $LabelName; ?>'); validateAdNumber(this);" lang='custom'
				<?php } ?>
				maxlength='120' style="width:<?php echo $FinalValue[FieldSize]; ?>px;" labelValue='<?php echo $LabelName; ?>' onkeypress='return isNumberKeyAd(event);' /><span id='CustomField<?php echo $m; ?>span' class='errortxt errorwidth'></span></div><br clear="all"/></span>
			<?php }
			elseif($FinalValue[FieldType] == 2) { ?>			
				&nbsp;<img align='absmiddle' title='<?php echo $FinalValue[FieldToolTip]; ?>' src='<?php echo $SITE_URL; ?>/images/information.png'  alt=''></div>
				<div class="pfdivrt smalltxt fleft"><select title='<?php echo $FinalValue[FieldToolTip]; ?>' name='CustomField<?php echo $m; ?>' id='CustomField<?php echo $m; ?>' style="width:<?php echo $FinalValue[FieldSize]; ?>px;" class='borderedge' labelValue='<?php echo $LabelName; ?>'
				<?php if($FinalValue[isMandatory] == 'Y') {  ?>
					onChange="validateAdNumber(this);" onBlur	="CustomSelect(this,'<?php echo $LabelName; ?>'); validateAdText(this);" lang='custom'
				<?php } ?>
					><option value=''>Select</option>
				<?php $so										=	1;			
				foreach($FieldValue as $FieldKey=>$FieldVal){ ?>
					<option value='<?php echo $FieldVal; ?>' 
					<?php if($FinalValue[FieldValue] == $FieldVal) { ?>
						selected='selected'
					<?php }	?>			
						>
					<?php echo ucwords($FieldVal); ?>
					</option>
					<?php $so++; ?>
				<?php } ?>
				</select><span id='CustomField<?php echo $m; ?>span' class='errortxt errorwidth'></span></div><br/><br clear="all"/>
			<?php }
			elseif($FinalValue[FieldType] == 3) { ?>
				&nbsp;<img align='absmiddle' title='<?php echo $FinalValue[FieldToolTip]; ?>' src='<?php echo $SITE_URL; ?>/images/information.png'  alt=''></div>
				<div class="pfdivrtaddl smalltxt fleft"><textarea minlength='5' onkeypress='return imposeMaxLength(this, <?php echo $FinalValue[FieldLength]; ?>);' class='borderedge bordercolor'  title='<?php echo $FinalValue[FieldToolTip]; ?>' rows='3' cols='<?php echo $FinalValue[FieldSize]; ?>' class='borderedge bordercolor' name='CustomField<?php echo $m; ?>' id='CustomField<?php echo $m; ?>' labelValue='<?php echo $LabelName; ?>'
				<?php if($FinalValue[isMandatory] == 'Y') { ?>
					onblur="CustomText(this,'<?php echo $LabelName; ?>'); validateAdText(this);" lang='custom'
				<?php } ?>
				><?php echo $FinalValue[FieldValue]; ?></textarea><span id='CustomField<?php echo $m; ?>span' class='errortxt errorwidth'></span></div><br/><br clear="all"/>
			<?php }
			elseif($FinalValue[FieldType] == 4) { ?>				
				<?php $checkValue						=	explode(',',$FinalValue[FieldValue]);
				$FinalValue[FieldName]					= str_replace(" ","",$FinalValue[FieldName]); ?>
				<div class='pfdivlt smalltxt fleft tlright'><?php _e($LabelName); 
					if($FinalValue[isMandatory] == 'Y') { ?><font class="clr3">*</font> <?php } ?>
				&nbsp;<img align='absmiddle' title='<?php echo $FinalValue[FieldToolTip]; ?>' src='<?php echo $SITE_URL; ?>/images/information.png'  alt=''></div>
				<div class="pfdivrtaddl smalltxt fleft"><div>
				<?php foreach($FieldValue as $FieldKey=>$FieldVal) { 
					$RadioName							=	"CustomField".$m."[]"; ?>
					<span class="checkboxpad"><input title='<?php echo $FinalValue[FieldToolTip]; ?>' class='RadioStyle' type='radio' name='<?php echo $RadioName; ?>' id='CustomField<?php echo $m; ?>' labelValue='<?php echo $LabelName; ?>'
					<?php if($FinalValue[isMandatory] == 'Y') { ?>
						onblur="CustomRadio(this,'<?php echo $LabelName; ?>');" lang='custom'
					<?php }	
					foreach($checkValue as $checkVal) {
						if($checkVal == $FieldVal) { ?>
							checked='checked'
						<?php } 
					} ?>
					value='<?php echo $FieldVal; ?>'>	
					<?php echo ucwords($FieldVal); ?></span>
				<?php } ?>
				<span id='CustomField<?php echo $m; ?>span' class='errortxt errorwidth'></span></div></div><br/><br clear="all"/>			
			<?php }
			elseif($FinalValue[FieldType] == 5) { ?>			
				<?php $checkValue							=	explode(',',$FinalValue[FieldValue]); ?>
				<div class='pfdivlt smalltxt fleft tlright'><?php _e($LabelName); 
					if($FinalValue[isMandatory] == 'Y') { ?><font class="clr3">*</font> <?php } ?>
				&nbsp;<img align='absmiddle' title='<?php echo $FinalValue[FieldToolTip]; ?>' src='<?php echo $SITE_URL; ?>/images/information.png'  alt=''></div>
				<div class="pfdivrtaddl smalltxt fleft"><div>
				<?php foreach($FieldValue as $FieldKey=>$FieldVal) { 
					$CheckName							=	"CustomField".$m."[]"; ?>
					<span class="checkboxpad"><input title='<?php echo $FinalValue[FieldToolTip]; ?>' type='checkbox' class='RadioStyle' name='<?php echo $CheckName; ?>' id='CustomField<?php echo $m; ?>' labelValue='<?php echo $LabelName; ?>'
					<?php if($FinalValue[isMandatory] == 'Y') { ?>
						onblur="CustomCheck(this,'<?php echo $LabelName; ?>');" lang='custom'
					<?php } 
					foreach($checkValue as $checkVal) {
						if($checkVal == $FieldVal) { ?>
							checked='checked'
						<?php }
					} ?>
					value='<?php echo $FieldVal; ?>'>
					<?php echo ucwords($FieldVal); ?></span>
				<?php } ?>
				<span id='CustomField<?php echo $m; ?>span' class='errortxt errorwidth'></span></div></div><br/><br clear="all"/>
			<?php }
			echo "<input type='hidden' name='CustomRows' id='CustomRows' value='$CustomRows' />";
			echo "<input type='hidden' name='CustomFieldId$m' id='CustomFieldId$m' value='$FinalValue[IdField]' />";
			echo "<input type='hidden' name='CustomPostId$m' id='CustomPostId$m' value='$FinalValue[idAddData]' />";
			echo "<input type='hidden' name='PostId' id='PostId' value='$FinalValue[idPost]' />";
			$m++;		
		}
	?>
	<!-- END OF ALL DYNAMIC CUSTOM FIELDS -->


<script type="text/javascript" src="<?php echo SITE_URL; ?>/content/jscripts/tiny_mce/tiny_mce.js"></script>
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
		<div class="pfdivrtaddl smalltxt fleft"><textarea rows="10" cols="73" name="description" id="description" onblur="validateTextBox(this); validateAdText(this);" lang="false"><?php echo stripslashes($row['description']);?></textarea>
		
	<?php }else{?>
		<textarea rows="10" cols="73" name="description" class='borderedge bordercolor' id="description" onblur="validateTextBox(this); validateAdText(this);" lang="false"><?php echo strip_tags($row['description']);?></textarea>
    <?php }?>
    <span id='descriptionspan' class='errortxt errorwidth'></span></div><br clear="all"/><br/>


    <?php if (VIDEO){?>
   <div class='pfdivlt smalltxt fleft tlright'><span style="cursor:pointer;" onclick="youtubePrompt();"><?php _e("YouTube video");?></span>:</div><div class="pfdivrt smalltxt fleft">
    <input id="video" name="video" class='borderedge' type="text" value="<?php echo $row["video"];?>" onclick="youtubePrompt();" size="40" /></div><br/><br clear="all"/>
    <div id="youtubeVideo"></div>
    
    <?php } ?><br/>
	<!-- <div class='pfdivlt smalltxt fleft tlright'><?php _e("Your Name");?><font class="clr3">*</font> 
	<?php echo "<img align='absmiddle' title='Your name comes here' src='$SITE_URL/images/information.png'  alt=''>"; ?></div>
	<div class="pfdivrt smalltxt fleft">
	<input id="name" name="name" type="text" class='borderedge' value="<?php echo $row["name"];?>" maxlength="75" onblur="validateTextBox(this); validateAdText(this);" lang="false"  /><span id='namespan' class='errortxt errorwidth'></span></div><br/><br clear="all"/><br/> -->
	<div class='pfdivlt smalltxt fleft tlright'><?php _e("Mobile number (optional)");?>:
	</div><div class="pfdivrt smalltxt fleft"><input id="phone" name="phone" type="text" class='borderedge' value="<?php echo $row["phone"];?>" maxlength="11"/></div><br/><br clear="all"/>
	<?php if (MAX_IMG_NUM>0){
	echo "<br />".T_("Upload pictures max file size").": ".(MAX_IMG_SIZE/1000000)."Mb ".T_("format")." ".IMG_TYPES."<br />";
	echo "<input type='hidden' name='MAX_FILE_SIZE' value='".MAX_IMG_SIZE."' />";
	echo "<b>".T_("These images will be permanently removed if you upload new ones")."</b><br />";?>
	<?php 
		$images=getPostImages($post_id,$insertDate);
		foreach($images as $img){
			echo '<a href="'.$img[0].'" title="'.$itemTitle.' '.T_("Picture").'" target="_blank">
			 		<img class="thumb" src="'.$img[1].'" title="'.$itemTitle.' '.T_("Picture").'" alt="'.$itemTitle.' '.T_("Picture").'" /></a>';
		}
		for ($i=1;$i<=MAX_IMG_NUM;$i++){
			?><div class='pfdivlt smalltxt fleft tlright'><?php _e("Picture");?> <?php echo $i?></div><div class="pfdivrt smalltxt fleft"><input type="file" name="pic<?php echo $i?>" id="pic<?php echo $i?>" value="<?php echo $_POST["pic".$i];?>" /></div><br/><br clear="all"/><?php
		 }
	 }?>	
	<?php if (CAPTCHA){ ?>
		<div class='pfdivlt smalltxt fleft tlright'><?php mathCaptcha('edititem');?><font class="clr3">*</font>
		<?php echo "<img align='absmiddle' title='Please add these numbers' src='$SITE_URL/images/information.png'  alt=''>"; ?>
	</div><div class="pfdivrt smalltxt fleft"><p><input id="math" name="math" class='borderedge' type="text" size="2" maxlength="2"  onkeypress="return isNumberKeyAd(event);" onblur="validateTextBox(this); validateAdNumber(this);" lang="false" /><span id='mathspan' class='errortxt errorwidth'></span></p></div><br/><br clear="all"/>
	<?php }?>
	<div style="padding-left:100px;"><input type="submit" title="Click to Update the Advertisement" class="but" id="submit" value="<?php _e("Update");?>" /><div>
</form>		
<?php 
		}
		else _e("Nothing found");//nothing returned for that item		
	}
}

}//if else

require_once('../includes/footer.php');
?>