<?php
	require_once('../../includes/functions.php');
	require_once('../../includes/common.php');
		
	$idLoadCategory				=	cG("idLoadCategory");
	$subId						=	cG("subId");
	$location					=	cG("selocation");
	$sedate						=	cG("sedate");
	$publishdate				=	cG("publish");
	$customfieldVal				=	cG("customArr");
	$customfieldValArray		=	explode(',',$customfieldVal);
	//print_r($customfieldValArray);
?>


	<form name="customsearch" method="post" action="" onsubmit="return customsearchfunc(this)">
	<?php $query="SELECT idLocation, name, 
                (SELECT name
		FROM classifieds_locations
		WHERE idLocation = C.idLocationParent )
            FROM classifieds_locations C
	    WHERE idLocationParent != 0
	    ORDER BY idLocationParent, idLocation"; ?>
		
		<span style="padding-top:10px;">
			<span class='displayblock'><?php _e("Location"); ?>&nbsp;:&nbsp;<br/><?php	$style = 1; $categoryval = ''; echo sqlOptionGroupSearch($query,"advancedlocation",$location,$categoryval,$style); ?><br/>		
			</span>
		
			<span class='displayblock'><?php _e("Published Date"); ?>&nbsp;:&nbsp;<br/><select name='publishdate' id='publishdate' onBlur="publishSelect(this); validateAdText(this);" class='borderedge widthstyle heightstyle'><option value=''>- Select -</option>
			<option value='ltone' <?php if($sedate == 'ltone') { ?> selected <?php } ?> >Less than 1 month</option>
			<option value='ltthree' <?php if($sedate == 'ltthree') { ?> selected <?php } ?>>Less than 3 months</option>
			<option value='ltsix' <?php if($sedate == 'ltsix') { ?> selected <?php } ?>>Less than 6 months</option>
			<option value='gtsix' <?php if($sedate == 'gtsix') { ?> selected <?php } ?>>Greater than 6 months</option>
			</select><br/>			
			</span>



<?php	$queryMap="SELECT idField,FieldName,FieldActualName,FieldValues FROM ".TABLE_PREFIX."custom_fields				WHERE idField in	(SELECT idField FROM ".TABLE_PREFIX."categories_field_mapping WHERE							idCategory='$idLoadCategory' and Active='Y') and Active='Y'";
		$resultMap = mysql_query($queryMap);
		$i		=	0;

		while($rowMap = mysql_fetch_array($resultMap)) {
			$idFieldVal			=	$rowMap[idField];
			$FieldActualName	=	$rowMap[FieldActualName]; 
			$FieldName			=	$rowMap[FieldName]; 

			if($i != 4) {
				if($rowMap[FieldValues] != '') {
					$FieldValues		=	explode(',',$rowMap[FieldValues]);
					//print_r($FieldValues);
					if($idFieldVal != '') {
						$t = 2;
						//if($ParentId=='' && $catPresent !=5) { $catPresent = 5; echo "<br/>"; } ?>
						<?php if($i == 1 || $i == 4) { ?>
								<span>
						<?php } ?>
						<span class='displayblock <?php if($i == 1 || $i == 4) {  ?>padtop<?php } ?>' >
						<?php echo ucwords(strtolower($FieldActualName)); ?>&nbsp;:&nbsp;<br/>
						<input type="hidden" id="idField<?php echo $i; ?>" name="idField<?php echo $i; ?>" value="<?php echo $idFieldVal; ?>" />
							
							<select name='customfield<?php echo $i; ?>' id='customfield<?php echo $i; ?>' class='borderedge widthstyle heightstyle'>		
								<option value=''>- Select -</option>
								<?php $k = 0; foreach($FieldValues as $AdminFieldValue) { ?>
								<option value='<?php echo friendly_url($AdminFieldValue); ?>' 
								<?php if($publishdate !='' ) { foreach($customfieldValArray as $customVal) {	

								if(strtolower($customVal) == friendly_url($AdminFieldValue)) { ?> selected <?php } 
						} } ?>

								><?php echo $AdminFieldValue; ?></option>
								<?php $k++; } $v = $i; $i++; ?>
							</select></span>
						<?php if($i == 1) { ?>
							<span class="displayblock">
								<span>
									<button type="submit" id="submit" style="border: 0; background: transparent; cursor: pointer; cursor:hand; ">
										<img src="<?php echo SITE_URL; ?>/themes/<?php echo THEME; ?>/images/search-button.jpg" />
									</button>
								</span>
							</span></span><br/>
						<?php }
					} 
				}
			}
		} ?>		
<?php if($t != 2) { 
	if($subId != ''){
		$SearchChildNameRes = mysql_query("select idCategory,name,friendlyName from ".TABLE_PREFIX."categories where idCategoryParent = '$subId' ORDER BY name"); 
	} else {
		//echo "select idCategoryParent from ".TABLE_PREFIX."categories where idCategory = '$idLoadCategory' and idCategoryParent != '0' ORDER BY name";
		$parentCat = mysql_query("select idCategoryParent from ".TABLE_PREFIX."categories where idCategory = '$idLoadCategory' ORDER BY name");
		$parentCatRow  = mysql_fetch_array($parentCat);
		$parentCatId	=	$parentCatRow[idCategoryParent];
		
			if($parentCatId == '0') {
				$SearchChildNameRes = mysql_query("select idCategory,name,friendlyName from ".TABLE_PREFIX."categories where idCategoryParent = '$idLoadCategory' ORDER BY name"); 
			} else {
				$SearchChildNameRes = mysql_query("select idCategory,name,friendlyName from ".TABLE_PREFIX."categories where idCategoryParent = '$parentCatId' ORDER BY name"); 
			}
		
		}
	?>
<span class='displayblock'>
	<?php _e("Category"); ?>&nbsp;:&nbsp;<br/>
	<select name='categoryIf' id='categoryIf' onBlur="categorySelect(this); validateAdText(this);" onchange="loadcustomfields(this.value,'<?php echo SITE_URL; ?>/themes/<?php echo THEME; ?>/','<?php echo $subId; ?>')" class='borderedge widthstyle heightstyle'>
		<option value=''>- Select -</option>
		<?php while($rowChildSearch = mysql_fetch_array($SearchChildNameRes)) { ?>
		<option value='<?php echo $rowChildSearch[idCategory]; ?>' <?php if($rowChildSearch[idCategory] == $idLoadCategory) { ?> selected <?php } ?>><?php echo $rowChildSearch[name]; ?></option>	
		<?php } ?>
	</select><br/><input type="hidden" id="nocustom" name="nocustom" value="" /><input type="hidden" id="totalcustomfields" name="totalcustomfields" value="500" />
</span>
<span>
	<button type="submit" id="submit" style="border: 0; background: transparent">
		<img src="<?php echo SITE_URL; ?>/themes/<?php echo THEME; ?>/images/search-button.jpg" />
	</button>
</span>
<span>
<?php } else { ?> <input type="hidden" id="totalcustomfields" name="totalcustomfields" value="<?php echo $i; ?>" /><input type="hidden" id="nocustom" name="nocustom" value="1" /><input type="hidden" id="totalcustom" name="totalcustom" value="<?php echo $v; ?>" /><input type="hidden" id="categoryIf" name="categoryIf" value="<?php echo $idLoadCategory; ?>" /> <?php } ?>
<br/><br/>
<span id='commonsearchspan'></span>
</form>