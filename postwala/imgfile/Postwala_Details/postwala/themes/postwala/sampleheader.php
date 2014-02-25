<?php $queryMap="SELECT idMap,idField FROM ".TABLE_PREFIX."categories_field_mapping WHERE idCategory='$idCategory' and Active='Y'"; 
		$resultMap = mysql_query($queryMap);
		$i		=	0;
		while($rowMap = mysql_fetch_array($resultMap)) {
			$idField =	$rowMap[idField];
			$queryField="SELECT idField,FieldName,FieldActualName,FieldValues FROM ".TABLE_PREFIX."custom_fields WHERE idField='$idField' and Active='Y'"; 
			$resultField = mysql_query($queryField);
			while($rowField = mysql_fetch_array($resultField)) {
				$idFieldVal =	$rowField[idField];
				$FieldActualName =	$rowField[FieldActualName]; 
				$FieldName		 =	$rowMap[FieldName]; 
				if($i != 4) {
					if($rowField[FieldValues] != '') {
						$FieldValues		=	explode(',',$rowField[FieldValues]);
						if($idFieldVal != '') {
							$t = 2;
							if($ParentId=='' && $catPresent !=5) { $catPresent = 5; echo "<br/>"; }
							?>
							<?php if($i == 1 || $i == 4) { ?>
										<span>
							<?php } ?>
							<span class='displayblock <?php if($i == 1 || $i == 4) {  ?>padtop<?php } ?>' >
								<?php echo ucwords(strtolower($FieldActualName)); ?>&nbsp;:&nbsp;<br/>
								<input type="hidden" id="idField<?php echo $i; ?>" name="idField<?php echo $i; ?>" value="1" />
								<select name='customfield<?php echo $i; ?>' id='customfield<?php echo $i; ?>' class='borderedge widthstyle heightstyle'>		
									<option value=''>- Select -</option>
									<?php $k = 0; foreach($FieldValues as $AdminFieldValue) { ?>
									<option value='<?php echo friendly_url($AdminFieldValue); ?>'><?php echo $AdminFieldValue; ?></option>
									<?php $k++; } $i++; ?>
								</select>
							</span>
							<?php if($i == 1) { ?>
								<span class="displayblock">
									<span>
										<button type="submit" id="submit" style="border: 0; background: transparent; cursor: pointer; cursor:hand;">
											<img src="<?php echo SITE_URL; ?>/themes/<?php echo THEME; ?>/images/search-button.jpg"/>
										</button>
									</span>
								</span>
							</span><br/>
							<?php }
						} 
				   }
				}
				
			} ?>
<?php } ?>





