<?php
#============================================================================================================
# Author 		: J P Senthil Kumar
# Start Date	: 18 May 2011
# End Date		: 19 May 2011
# Project		: Custom Fields
# Description	: This field is used to add custom fields
#============================================================================================================

require_once('access.php');
require_once('header.php');
require_once('../lib/commonArray.inc');
?>
<script type="text/javascript">
	function newCustomField(){
		d												=	document.customfield;
		d.cusid.value									=	"";
		d.cusname.value									=	"";
		d.cusactual.value								=	"";
		d.cusdesc.value									=	"";
		d.custool.value									=	"";
		d.cussize.value									=	"";
		d.charlen.value									=	"";
		d.custype.value									=	"0";
		d.cusvalues.value								=	"";
		d.action.value									=	"new";
		d.submitCat.value								=	"<?php _e("New Custom Field");?>";
		document.getElementById("form-tab").innerHTML	=	"<?php _e("Add Custom Field");?>";
		show("formCat");
		location.href									=	"#formCat";
	}	
	function editCustomField(cid, cusftype){
		d												=	document.customfield;
		d.cusid.value									=	cid;
		d.cusname.value									=	document.getElementById('name-'+cid).innerHTML; //cusname
		d.cusactual.value								=	document.getElementById('actualName-'+cid).innerHTML; //custom actual name
		d.cusdesc.value									=	document.getElementById('desc-'+cid).innerHTML;//cdesc;
		d.custool.value									=	document.getElementById('tool-'+cid).innerHTML;//custool;
		d.cussize.value									=	document.getElementById('size-'+cid).innerHTML;//custool;
		d.charlen.value									=	document.getElementById('charlen-'+cid).innerHTML;//custool;
		d.custype.value									=	cusftype;
		if(d.custype.value == 2 || d.custype.value == 4 || d.custype.value == 5)
		document.getElementById("typeHide").style.display=	"block";
		else document.getElementById("typeHide").style.display=	"none";	
		d.cusvalues.value								=	document.getElementById('cusval-'+cid).innerHTML;//cusvalue;
		var cusstatus									=	document.getElementById('cusactive-'+cid).innerHTML;
		if(cusstatus == 'Y')
		d.cusactive[0].checked							=	true;
		else
		d.cusactive[1].checked							=	true;
		d.action.value									=	"edit";
		d.submitCat.value								=	"<?php _e("Edit Custom Field");?>";
		document.getElementById("form-tab").innerHTML	=	"<?php _e("Edit Custom Field");?>";
		show("formCat");
		location.href									=	"#formCat";
	}	
	function deleteCustomField(cusid){
		if (confirm('<?php _e("Are You Sure You Want to Delete Custom Field");?> "'+document.getElementById('name-'+cusid).innerHTML+'"?'))
		window.location = "customfields.php?action=delete&cusid=" + cusid;
	}
	function hideOthers(typeVal) {
		d												=	document.customfield;
		var typeValue									=	typeVal.value;
		if(typeValue == 2 || typeValue == 4 || typeValue == 5)
		document.getElementById("typeHide").style.display=	"block";
		else document.getElementById("typeHide").style.display=	"none";
	}
</script>
<h2><?php _e("Custom Fields"); ?></h2>
<?php
function catSlug($name,$id=""){ //try to prevent duplicated categories
	$ocdb=phpMyDB::GetInstance();	

	if (is_numeric($id)) $query="SELECT FieldName FROM ".TABLE_PREFIX."custom_fields where (FieldName='$name') and (idField <> $id) limit 1"; 
	else $query="SELECT FieldName FROM ".TABLE_PREFIX."custom_fields where (FieldName='$name') limit 1";
	$res=$ocdb->getValue($query,"none");

	if ($res==false) return $name;
	else return false;
}

//actions
if (cP("action")!=""||cG("action")!=""){
	$action=cG("action");
	if ($action=="")$action=cP("action");
	
	if ($action=="new"){
		$nameSlug=catSlug(cP("cusname"));
		if ($nameSlug!=false){  //no exists insert

		$cusactive = cP("cusactive");

		if ($cusactive == 'Y') {
			$ch1 = 'checked';
		}
		else {
			$cusactive = 'N';
			$ch1 = 'unchecked';
		}
		
		$custype = cP("custype");
		$cusvalues = cP("cusvalues");
		if($custype == 1 || $custype == 3 || $custype == 6)
			$cusvalues = '';
		
			$ocdb->insert(TABLE_PREFIX."custom_fields (FieldName,FieldActualName,FieldDescription,FieldTooltip,FieldSize,FieldLength,FieldType,FieldValues,Active,UpdatedTime)",
			"'".cP("cusname")."','".cP("cusactual")."','".cP("cusdesc")."','".cP("custool")."','".cP("cussize")."','".cP("charlen")."','".cP("custype")."','".$cusvalues."','".$cusactive."',NOW()");
		}
		else { _e("Custom field already exists"); echo "<br/><br/><br/><br/>"; }

	}
	elseif ($action=="delete"){
		$ocdb->delete(TABLE_PREFIX."custom_fields","idField=".cG("cusid"));
		//echo "Deleted";
	}
	elseif ($action=="edit"){
		$nameSlug=catSlug(cP("cusname"),cP("cusid"));
		
		$cusactive = cP("cusactive");
		
		if($cusactive != '' ) {
			if ($cusactive == 'Y') {
				$ch1 = 'checked';
			}
			else {
				$cusactive = 'N';
				$ch1 = 'unchecked';
			}
		}

		$custype = cP("custype");
		$cusvalues = cP("cusvalues");


		if($custype == 1 || $custype == 3 || $custype == 6)
			echo $cusvalues = '';

		if ($nameSlug!=false){  //no exists update
			$query="update ".TABLE_PREFIX."custom_fields set FieldName='".cP("cusname")."',FieldActualName='".cP("cusactual")."',FieldDescription='".cP("cusdesc")."'
					,FieldToolTip='".cP("custool")."',FieldSize='".cP("cussize")."',FieldLength='".cP("charlen")."',FieldType='".cP("custype")."',FieldValues='".$cusvalues."',Active='".$cusactive."',UpdatedTime=Now() where idField=".cP("cusid");
			$ocdb->query($query);
		}
		 
		else { _e("Custom field already exists"); echo "<br/><br/><br/><br/>"; }
		//echo "Edit: $query";
	}
	elseif ($action=="filter" && is_numeric(cG("cusid"))){
		$filter = ' and idField ='.cG("cusid");
	}
}
?>
<p class="desc"><?php _e("Manage your website custom fields");?>
<?php 
		echo '<form action="customfields.php" method="get" >';
				$query="SELECT idField,FieldName FROM ".TABLE_PREFIX."custom_fields order by FieldName";
				sqlOptionGroup($query,"cusid",cG("cusid"));
		echo'<input type="hidden" name="action" value="filter" />
			<input type="submit" title="Click to filter custom field" class="button-submit" value="'.T_('Filter').'" /></form>';	
	?></p>
<div id='formCat' style="display:none;">
	<div id="form-tab" class="form-tab"></div>
	<div class="clear"></div>
	<form name="customfield" action="customfields.php" method="post" onsubmit="return validateTextArea() && checkForm(this);">
		<fieldset>
			<p>
				<label><?php _e("Custom Field Name");?></label>
				<input title="To add the custom name" name="cusname" type="text" class="text-long" lang="false" onblur="validateText(this);" xml:lang="false" />
			</p>
			<p>
				<label><?php _e("Custom Screen Name");?></label>
				<input title="To add the custom screen name" name="cusactual" type="text" class="text-long" lang="false" onblur="validateText(this);" xml:lang="false" />
			</p>
			<p>
            	<label><?php _e("Field Description");?></label>
				<textarea title="To add the custom description" rows="1" cols="1" name="cusdesc"></textarea>
			</p>
			<p>
            	<label><?php _e("Field Tooltip");?></label>
                <textarea title="To add the custom tooltip" rows="1" cols="1" name="custool"></textarea>
        	</p>
			<p>
				<label><?php _e("Custom Field Size");?></label>
				<input title="To add the custom field size" maxlength="3" onkeypress='return isNumberKey(event);' name="cussize" type="text" class="text-long" lang="false" onblur="validateNumber(this);" xml:lang="false" />
			</p>  
			<p>
				<label><?php _e("Character Length");?></label>
				<input title="To add the character length of the field" maxlength="3" onkeypress='return isNumberKey(event);' name="charlen" type="text" class="text-long" lang="false" onblur="validateNumber(this);" xml:lang="false" />
			</p>  
			<p>
            	<label><?php _e("Field Type");?></label>
                <select title="To add the custom type" name="custype" id="custype" onchange="hideOthers(this); return validateTextArea();">
				<option value="0">Select</option>
				<?php asort($customTypeArray); foreach($customTypeArray as $cusKey=>$cusValue) { ?>
				<option value="<?php echo $cusKey; ?>"><?php echo $cusValue; ?></option>
				<?php } ?>
				</select>
        	</p>
			<p id="typeHide" style="display:none;">
            	<label><?php _e("Field Values");?></label>
                <textarea title="To add the custom values" rows="1" cols="1" name="cusvalues" id="cusvalues" ></textarea>
        	</p>
			<p>
            	<label><?php _e("Active Status");?></label>
                <input title="Active Status" name="cusactive" id="cusactive" type="radio" value="Y" <?php if(isset($ch1)) print $ch1; else print "unchecked"; ?>/>Yes
				<input title="Inactive Status" name="cusactive" id="cusactive" type="radio" value="N" <?php if(isset($ch1)) print $ch1; else print "checked"; ?>/>No										
        	</p>
			<input id="submitCat" title="Click to add custom field" type="submit" value="" class=" button-submit" />
			<input type="submit" title="Click to cancel custom field" value="<?php _e("Cancel");?>" class="button-cancel" onclick="hide('formCat');return false;" />
			<input type="hidden" name="cusid" value="" />
			<input type="hidden" name="action" value="" />
		</fieldset>
	</form>
</div>
<div class="add_link"><a href="" onclick="newCustomField();return false;"><?php _e("New Custom Field");?></a></div>
<table>
	<tr class="thead">
		<td><?php _e("Custom Field Name");?></td>
		<td><?php _e("Custom Screen Name");?></td>
		<td><?php _e("Custom Type");?></td>
		<td><?php _e("Custom Values");?></td>
		<td><?php _e("Active Status");?></td>
		<td>&nbsp;</td>
	</tr>
	<?php 

	$result = $ocdb->query("SELECT * from ".TABLE_PREFIX."custom_fields  where 6=6 ".$filter." order by  FieldName");
		$row_count = 0;
		while($row = mysql_fetch_array($result)){
			$cusName	=	$row["FieldName"];
			$cusActual	=	$row["FieldActualName"];
			$cusDesc	=	$row["FieldDescription"];
			$cusTool	=	$row["FieldToolTip"];
			$cusSize	=	$row["FieldSize"];
			$charLen	=	$row["FieldLength"];
			$cusId		=	$row["IdField"];
			$cusType	=	$row["FieldType"];
			$cusValues	=	$row["FieldValues"];
			$cusActive	=	$row["Active"];
			$row_count++;
			if ($row_count%2 == 0) $row_class = 'class="odd"';
			else $row_class = 'class="even"';

	?>
	<tr <?php echo $row_class;?>>      
		<td><?php echo $cusName;?></td>
		<td><?php echo $cusActual;?></td>
		<td><?php echo $customTypeArray[$cusType];?></td>
		<td><?php echo $cusValues;?></td>
		<td><?php echo $cusActive;?></td>
		<td class="action">
			<a href="" onclick="editCustomField('<?php echo $cusId; ?>','<?php echo $cusType;?>');return false;" class="edit"><?php _e("Edit");?></a> 
			| <a href="" onclick="deleteCustomField('<?php echo $cusId;?>');return false;" class="delete"><?php _e("Delete");?></a>
		<div style="display:none;" id="name-<?php echo $cusId; ?>"><?php echo $cusName;?></div>
		<div style="display:none;" id="actualName-<?php echo $cusId; ?>"><?php echo $cusActual;?></div>
		<div style="display:none;" id="desc-<?php echo $cusId; ?>"><?php echo $cusDesc;?></div>
		<div style="display:none;" id="tool-<?php echo $cusId; ?>"><?php echo $cusTool;?></div>
		<div style="display:none;" id="size-<?php echo $cusId; ?>"><?php echo $cusSize; ?></div>
		<div style="display:none;" id="charlen-<?php echo $cusId; ?>"><?php echo $charLen; ?></div>
		<div style="display:none;" id="cusval-<?php echo $cusId; ?>"><?php echo $cusValues;?></div>
		<div style="display:none;" id="cusactive-<?php echo $cusId; ?>"><?php echo $cusActive; ?></div>

		</td>
	</tr>
	<?php } ?>
</table>
<?php
require_once('footer.php');
?>