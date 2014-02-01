<?php
#============================================================================================================
# Author 		: J P Senthil Kumar
# Start Date	: 16 June 2011
# End Date		: 
# Project		: Dynamic Custom Fields
# Description	: This file is used to bring the custom fields dynamically when choosing category
#============================================================================================================

require_once('../includes/functions.php');
if (file_exists(SITE_ROOT.'/themes/'.THEME.'/dynamiccustomfields.php')){//item-new from the theme!
	require_once(SITE_ROOT.'/themes/'.THEME.'/dynamiccustomfields.php');
}
else{//not found in theme

$idCategory = cG("idCategory");
?>
	<!-- VALUES TO GET THE CUSTOM FIELDS DYNAMICALLY-->
	<?php
	$ocdb												=	phpMyDB::GetInstance();
	$query="SELECT idField,isMandatory,FieldOrder FROM ".TABLE_PREFIX."categories_field_mapping WHERE idCategory = '$idCategory' and Active = 'Y' ORDER BY FieldOrder ASC";
	$res												=	$ocdb->query($query);	
	$Mapping											=	array();
	$r													=	0;
	while($row = mysql_fetch_assoc($res)) {
		$Mapping[$row[idField]]							=	$row;
		$MapIdFieldArr[$r]								=	$row[idField]; // ID FIELD ARRAY TO USE IN THE BELOW QUERY
		$r++;
	}
	$IdFieldStr											=	implode("','",$MapIdFieldArr); // ID FIELD STRING TO USE IN THE BELOW QUERY

	foreach($Mapping as $MapKeyArr=>$MapValueArr) {
		$MapFinal[$MapValueArr[idField]][idField]		=	$MapValueArr[idField];
		$MapFinal[$MapValueArr[idField]][isMandatory]	=	$MapValueArr[isMandatory];
		$MapFinal[$MapValueArr[idField]][FieldOrder]	=	$MapValueArr[FieldOrder];
	}		
	$queryCustom="SELECT IdField,FieldName,FieldActualName,FieldDescription,FieldToolTip,FieldSize,FieldLength,FieldType,FieldValues FROM ".TABLE_PREFIX."custom_fields WHERE IdField in ('".$IdFieldStr."') and Active = 'Y'";		
	$resCustom											=	$ocdb->query($queryCustom);
	$CustomFields										=	array();
	while($rowCustom = mysql_fetch_assoc($resCustom)) {
		$CustomFields[$rowCustom[IdField]]				=	$rowCustom;
	}			
	foreach($CustomFields as $CusKeyArr=>$CusValueArr) {
		$Final[$MapFinal[$CusValueArr[IdField]][FieldOrder]][FieldName]		=	$CusValueArr[FieldName];
		$Final[$MapFinal[$CusValueArr[IdField]][FieldOrder]][FieldActualName]=	$CusValueArr[FieldActualName];
		$Final[$MapFinal[$CusValueArr[IdField]][FieldOrder]][FieldDescription]	=	$CusValueArr[FieldDescription];
		$Final[$MapFinal[$CusValueArr[IdField]][FieldOrder]][FieldToolTip]	=	$CusValueArr[FieldToolTip];
		$Final[$MapFinal[$CusValueArr[IdField]][FieldOrder]][FieldSize]		=	$CusValueArr[FieldSize];
		$Final[$MapFinal[$CusValueArr[IdField]][FieldOrder]][FieldLength]	=	$CusValueArr[FieldLength];
		$Final[$MapFinal[$CusValueArr[IdField]][FieldOrder]][FieldType]		=	$CusValueArr[FieldType];
		$Final[$MapFinal[$CusValueArr[IdField]][FieldOrder]][FieldValues]	=	$CusValueArr[FieldValues];
		$Final[$MapFinal[$CusValueArr[IdField]][FieldOrder]][idField]		=	$MapFinal[$CusValueArr[IdField]][idField];
		$Final[$MapFinal[$CusValueArr[IdField]][FieldOrder]][isMandatory]	=	$MapFinal[$CusValueArr[IdField]][isMandatory];
		$Final[$MapFinal[$CusValueArr[IdField]][FieldOrder]][FieldOrder]	=	$MapFinal[$CusValueArr[IdField]][FieldOrder];
	}

	$res = ksort($Final);	
	?>

	<!-- END OF VALUES TO GET CUSTOM FIELDS DYNAMICALLY-->



	<!-- START OF CUSTOM FIELDS DYNAMICALLY-->
	<?php	$SITE_URL									=	SITE_URL; ?>
	<?php	$CustomRows									=	count($Final);
			$m = 1;
			foreach($Final as $FinalKey=>$FinalValue) {
			$FieldValue									=	explode(',',$FinalValue[FieldValues]);
			$LabelName									=	ucwords($FinalValue[FieldActualName]);
			$FieldDescription							=	$FinalValue[FieldDescription];
	?>
			
	<?php if($FinalValue[FieldType] != 4 && $FinalValue[FieldType] != 5) { ?>
			<div class='pfdivlt smalltxt fleft tlright'>
		  <?php _e($LabelName); if($FinalValue[isMandatory] == 'Y') { ?><font class="clr3">*</font> <?php } ?> 
	<?php } ?>
	<?php	if($FinalValue[FieldType] == 1) { ?>
				&nbsp;<img align='absmiddle' title='<?php echo $FinalValue[FieldToolTip]; ?>' src='<?php echo $SITE_URL; ?>/images/information.png' alt=''></div>
				<div class="pfdivrt smalltxt fleft"><?php $ValidPrice = stristr($LabelName,"price"); if($ValidPrice) { ?><span>Rs.</span> <?php } $ValidSalary = stristr($LabelName,"salary"); if($ValidSalary) { ?><span>Rs.</span><?php } ?><span><input type='text' maxlength='<?php echo $FinalValue[FieldLength]; ?>' name='CustomField<?php echo $m; ?>' id='CustomField<?php echo $m; ?>' class='borderedge' style="width:<?php echo $FinalValue[FieldSize]; ?>px;"
				<?php if($FinalValue[isMandatory] == 'Y') { ?>
					onblur="CustomText(this,'<?php echo $LabelName; ?>'); validateAdText(this);" lang='custom'
				<?php }  ?>
				maxlength='120' labelValue='<?php echo $LabelName; ?>'/><span id='CustomField<?php echo $m; ?>span' class='errortxt errorwidth'></span></div><br clear="all"/> 	 </span>			
			<?php } 
			elseif($FinalValue[FieldType] == 6) { ?>
				&nbsp;<img align='absmiddle' title='<?php echo $FinalValue[FieldToolTip]; ?>' src='<?php echo $SITE_URL; ?>/images/information.png' alt=''></div>
				<div class="pfdivrt smalltxt fleft"><?php $ValidPrice = stristr($LabelName,"price"); if($ValidPrice) { ?><span>Rs.</span> <?php } $ValidSalary = stristr($LabelName,"salary"); if($ValidSalary) { ?><span>Rs.</span><?php } ?><span><input type='text' name='CustomField<?php echo $m; ?>' id='CustomField<?php echo $m; ?>' maxlength='<?php echo $FinalValue[FieldLength]; ?>' class='borderedge'
				<?php if($FinalValue[isMandatory] == 'Y') { ?>
					onBlur="CustomText(this,'<?php echo $LabelName; ?>'); validateAdNumber(this);" lang='custom'
				<?php } ?>
				maxlength='120' style="width:<?php echo $FinalValue[FieldSize]; ?>px;" labelValue='<?php echo $LabelName; ?>' onkeypress='return isNumberKeyAd(event);' /><span id='CustomField<?php echo $m; ?>span' class='errortxt errorwidth'></span></div><br clear="all"/> </span>
			<?php }
			elseif($FinalValue[FieldType] == 2) { ?>
				&nbsp;<img align='absmiddle' title='<?php echo $FinalValue[FieldToolTip]; ?>' src='<?php echo $SITE_URL; ?>/images/information.png'  alt=''></div>
				<div class="pfdivrt smalltxt fleft"><select name='CustomField<?php echo $m; ?>' id='CustomField<?php echo $m; ?>' style="width:<?php echo $FinalValue[FieldSize]; ?>px;" class='borderedge' labelValue='<?php echo $LabelName; ?>'
				<?php if($FinalValue[isMandatory] == 'Y') { ?>
					onChange="validateAdNumber(this);" onBlur	="CustomSelect(this,'<?php echo $LabelName; ?>'); validateAdText(this);" lang='custom'
				<?php } ?>
					><option value=''>Select</option>
				<?php $so										=	1;			
				foreach($FieldValue as $FieldKey=>$FieldVal){ ?>
					<option value='<?php echo $FieldVal; ?>'>
					<?php echo ucwords($FieldVal); ?>
					</option>
					<?php $so++; ?>
				<?php } ?>
				</select><span id='CustomField<?php echo $m; ?>span' class='errortxt errorwidth'></span></div><br/><br clear="all"/>
			<?php	}
			elseif($FinalValue[FieldType] == 3) { ?>
				&nbsp;<img align='absmiddle' title='<?php echo $FinalValue[FieldToolTip]; ?>' src='<?php echo $SITE_URL; ?>/images/information.png'  alt=''></div>
				<div class="pfdivrtaddl smalltxt fleft"><textarea minlength='5' onkeypress='return imposeMaxLength(this, <?php echo $FinalValue[FieldLength]; ?>);' class='borderedge bordercolor' rows='3' cols='<?php echo $FinalValue[FieldSize]; ?>' name='CustomField<?php echo $m; ?>' id='CustomField<?php echo $m; ?>'
				<?php if($FinalValue[isMandatory] == 'Y') { ?>
					lang='custom'
					<?php } ?>
					 onFocus="validSpanDesp(this,'<?php echo $FieldDescription; ?>');" onBlur="validTextDesp(this,'<?php echo $FieldDescription; ?>','<?php echo $LabelName; ?>'); validSpanDesp(this,'<?php echo $FieldDescription; ?>');" 
				labelValue='<?php echo $LabelName; ?>' descValue='<?php echo $FieldDescription; ?>'><?php echo $FieldDescription; ?></textarea><input type="hidden" name='CustomFieldDescription<?php echo $m; ?>' id='CustomFieldDescription<?php echo $m; ?>' value='<?php echo $FieldDescription; ?>' /><span id='CustomField<?php echo $m; ?>span' class='errortxt errorwidth'></span></div><br/><br clear="all"/>
			<?php }
			elseif($FinalValue[FieldType] == 4) {
				$FinalValue[FieldName] = str_replace(" ","",$FinalValue[FieldName]); ?>
				<!--<div style='width:900px; height:80px;' class='borderedge bordercolor' >-->
					<div class='pfdivlt smalltxt fleft tlright'><?php echo _e($LabelName); 
					if($FinalValue[isMandatory] == 'Y') { ?><font class="clr3">*</font> <?php } ?>
				&nbsp;<img align='absmiddle' title='<?php echo $FinalValue[FieldToolTip]; ?>' src='<?php echo $SITE_URL; ?>/images/information.png'  alt=''></div>
				<div class="pfdivrtaddl smalltxt fleft"><div>
				<?php foreach($FieldValue as $FieldKey=>$FieldVal) { 
					$RadioName							=	"CustomField".$m."[]"; ?>
					<span class="checkboxpad"><input title='<?php echo $FinalValue[FieldToolTip]; ?>' class='RadioStyle' type='radio' name='<?php echo $RadioName; ?>' id='CustomField<?php echo $m; ?>' labelValue='<?php echo $LabelName; ?>'

					<?php $offerNeed = stristr(strtolower($LabelName),"ad type"); if($offerNeed) { 
						$path = SITE_URL."/content/"; ?>
						onclick="getPremiumAds(this,'<?php echo $idCategory; ?>','<?php echo $path; ?>');" 
						<?php } ?>

					<?php if($FinalValue[isMandatory] == 'Y') { ?>
						onblur="CustomRadio(this,'<?php echo $LabelName; ?>');" lang='custom'
					<?php } ?>
					value='<?php echo $FieldVal; ?>'>					
					<?php echo ucwords($FieldVal); ?></span>
				<?php } ?>				
				</div><span id='CustomField<?php echo $m; ?>span' class='errortxt errorwidth'></span></div><br/><br clear="all"/>
			<?php }
			elseif($FinalValue[FieldType] == 5) { ?>
				<!--<div style='width:900px; height:80px;' class='borderedge bordercolor' >-->
					<div class='pfdivlt smalltxt fleft tlright'><?php echo _e($LabelName); 
					if($FinalValue[isMandatory] == 'Y') { ?><font class="clr3">*</font> <?php } ?>
				&nbsp;<img align='absmiddle' title='<?php echo $FinalValue[FieldToolTip]; ?>' src='<?php echo $SITE_URL; ?>/images/information.png'  alt=''></div>
				<div class="pfdivrtaddl smalltxt fleft"><div>
				<?php foreach($FieldValue as $FieldKey=>$FieldVal) { 
					$CheckName							=	"CustomField".$m."[]"; ?>
					<span class="checkboxpad"><input title='<?php echo $FinalValue[FieldToolTip]; ?>' type='checkbox' class='RadioStyle' name='<?php echo $CheckName; ?>' id='CustomField<?php echo $m; ?>' labelValue='<?php echo $LabelName; ?>'
					<?php if($FinalValue[isMandatory] == 'Y') { ?>
						onblur="CustomCheck(this,'<?php echo $LabelName; ?>');" lang='custom'
					<?php }  ?>
					value='<?php echo $FieldVal; ?>'>
					<?php echo ucwords($FieldVal); ?></span>
				<?php } ?>			
				<span id='CustomField<?php echo $m; ?>span' class='errortxt errorwidth'></span></div></div><br/><br clear="all"/>
			<?php }
			echo "<input type='hidden' name='CustomRows' id='CustomRows' value='$CustomRows' />";
			echo "<input type='hidden' name='CustomFieldId$m' id='CustomFieldId$m' value='$FinalValue[idField]' />";
			$m++;		
		}

}
?>
	<!-- END OF CUSTOM FIELDS DYNAMICALLY-->

