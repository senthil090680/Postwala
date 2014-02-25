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

								if(strtolower($customVal) == strtolower($AdminFieldValue)) { ?> selected <?php } 
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