<?php
#============================================================================================================
# Author 		: J P Senthil Kumar
# Start Date	: 19 May 2011
# End Date		: 19 May 2011
# Project		: Mapping Custom Fields to Categories
# Description	: This file is used to map the custom fields with the categories
#============================================================================================================

require_once('access.php');
require_once('header.php');
?>
<script type="text/javascript">
	function newMapField(){
		d												=	document.mapfield;
		d.mapid.value									=	"";
		d.mapcat.value									=	"";
		d.mapcus.value									=	"";
		d.mapmand.value									=	"";
		d.maporder.value								=	"";
		d.mapactive.value								=	"";
		d.action.value									=	"new";
		d.submitMap.value								=	"<?php _e("New Mapping");?>";
		document.getElementById("form-tab").innerHTML	=	"<?php _e("Map Fields");?>";
		show("formMap");
		location.href									=	"#formMap";
	}	
	function editMapField(cid){
		d												=	document.mapfield;
		d.mapid.value									=	cid;
		d.mapcat.value									=	document.getElementById('mapcat-'+cid).innerHTML; //category
		d.mapcus.value									=	document.getElementById('mapcus-'+cid).innerHTML;//custom field;
		var mapMandatory								=	document.getElementById('mapmand-'+cid).innerHTML;//mandatory;
		if(mapMandatory == 'Y')
		d.mapmand[0].checked							=	true;  // If it is Y, checked
		else
		d.mapmand[1].checked							=	true; // If it is N, unchecked

		d.maporder.value								=	document.getElementById('maporder-'+cid).innerHTML;//field order;
		var mapActive									=										document.getElementById('mapactive-'+cid).innerHTML;//active status;
		if(mapActive == 'Y')
		d.mapactive[0].checked							=	true;  // If it is Y, checked
		else
		d.mapactive[1].checked							=	true;  // If it is N, unchecked

		d.action.value									=	"edit";
		d.submitMap.value								=	"<?php _e("Edit Mapping");?>";
		document.getElementById("form-tab").innerHTML	=	"<?php _e("Map Fields");?>";
		show("formMap");
		location.href									=	"#formMap";
	}	
	function deleteMapField(mapid){
		if (confirm('<?php _e("Are You Sure You Want to Delete The Combination of Category");?> "'+document.getElementById('mapcatcomb-'+mapid).innerHTML+'" and Custom Field "'+document.getElementById('mapcuscomb-'+mapid).innerHTML+'" ?'))
		window.location = "mapcustocat.php?action=delete&mapid=" + mapid;
	}
</script>
<h2><?php _e("Mapping of Category to Custom Fields"); ?></h2>
<?php
function catSlug($cat,$cus,$id=""){ //try to prevent duplicated categories
	$ocdb	=	phpMyDB::GetInstance();

	if (is_numeric($id)) $query="SELECT idMap FROM ".TABLE_PREFIX."categories_field_mapping where (idCategory='$cat') and (idField='$cus') and (idMap <> $id) limit 1"; 
	else
		$query	=	"SELECT idMap FROM ".TABLE_PREFIX."categories_field_mapping where (idCategory='$cat') and (idField='$cus') limit 1";
	$res		=	$ocdb->getValue($query,"none");
	if ($res==false) return $cus;
	else return false;	
}

//actions
if (cP("action")!=""||cG("action")!=""){
	$action=cG("action");
	if ($action=="")$action=cP("action");
	
	if ($action=="new"){
		$nameSlug=catSlug(cP("mapcat"),cP("mapcus"));
		if ($nameSlug!=false){  //no exists insert
			$ocdb->insert(TABLE_PREFIX."categories_field_mapping (idCategory,idField,isMandatory,FieldOrder,Active,UpdatedTime)",
			"'".cP("mapcat")."','".cP("mapcus")."','".cP("mapmand")."','".cP("maporder")."','".cP("mapactive")."',NOW()");
		} else  { ?>
		<span style="color:#FF0000;"><?php _e("This mapping already exists"); ?></span>
		<?php }
	}
	elseif ($action=="delete"){
		$ocdb->delete(TABLE_PREFIX."categories_field_mapping","idMap=".cG("mapid"));
		//echo "Deleted";
	}
	elseif ($action=="edit"){
		$nameSlug=catSlug(cP("mapcat"),cP("mapcus"),cP("mapid"));
		
		$mapmand = cP("mapmand");
		
		if($mapmand != '' ) {
			if ($mapmand == 'Y') {
				$ch1 = 'checked';
			}
			else {
				$mapmand = 'N';
				$ch1 = 'unchecked';
			}
		}
		$mapactive = cP("mapactive");
		
		if($mapactive != '' ) {
			if ($mapactive == 'Y') {
				$ch2 = 'checked';
			}
			else {
				$mapactive = 'N';
				$ch2 = 'unchecked';
			}
		}
		if ($nameSlug!=false){  //no exists update
			$query="update ".TABLE_PREFIX."categories_field_mapping set idCategory='".cP("mapcat")."',idField='".cP("mapcus")."'
					,isMandatory='".$mapmand."',FieldOrder='".cP("maporder")."',Active='".$mapactive."',UpdatedTime=Now() where idMap=".cP("mapid");
			$ocdb->query($query);
		} else   { ?>
		<span style="color:#FF0000;"><?php _e("This mapping already exists"); ?></span> 
		<?php  }//echo "Edit: $query";
	}	

}
?>

<div id='formMap' style="display:none;">
	<div id="form-tab" class="form-tab"></div>
	<div class="clear"></div>
	<form name="mapfield" action="mapcustocat.php" method="post" onsubmit="return checkForm(this);">
		<fieldset>
			<p>
				<label><?php _e("Category Name");?></label>
				<?php sqlOptionTwoValues("SELECT idCategory,name,(select name from ".TABLE_PREFIX."categories where idCategory=C.idCategoryParent) ParentName FROM ".TABLE_PREFIX."categories C where 6=6 and idCategoryParent <> '0' ".$filter." order by ParentName,`order`","mapcat");?>
			</p>                          
			<p>
            	<label><?php _e("Custom Field Name");?></label>
				<?php sqlOption("select idField,FieldName from ".TABLE_PREFIX."custom_fields where 6=6 ".$filter." ORDER BY FieldName ASC","mapcus","");?>
			</p>			
			<p>
            	<label><?php _e("Mandatory");?></label>
                <input title="Mandatory" name="mapmand" type="radio" value="Y" <?php if(isset($ch1)) print $ch1;  else print "unchecked"; ?>/>Yes
				<input title="Not Mandatory" name="mapmand" type="radio" value="N" <?php if(isset($ch1)) print $ch1; else print "checked"; ?>/>No
        	</p>
			<p>
            	<label><?php _e("Field Order");?></label>
                <input  name="maporder" title="Order in which it should come in the category" type="text" class="text-small" lang="false"  onblur="validateNumber(this);" onkeypress="return isNumberKey(event);" value="" maxlength="5" xml:lang="false" />
        	</p>
			<p>
            	<label><?php _e("Active Status");?></label>
                <input title="Active Status" name="mapactive" id="mapactive" type="radio" value="Y" <?php if(isset($ch2)) print $ch2; else print "unchecked"; ?>/>Yes
				<input title="Inactive Status" name="mapactive" id="mapactive" type="radio" value="N" <?php if(isset($ch2)) print $ch2; else print "checked"; ?>/>No
        	</p>
			<input id="submitMap" title="Click to map custom field to categories" type="submit" value="" class=" button-submit" />
			<input type="submit" title="Click to cancel mapping" value="<?php _e("Cancel");?>" class="button-cancel" onclick="hide('formMap');return false;" />
			<input type="hidden" name="mapid" value="" />
			<input type="hidden" name="action" value="" />
		</fieldset>
	</form>
</div>
<div class="add_link"><a href="" onclick="newMapField();return false;"><?php _e("New Mapping");?></a></div>
<table>
	<tr class="thead">
		<td><?php _e("Category Name");?></td>
		<td><?php _e("Custom Field Name");?></td>
		<td><?php _e("Field Order");?></td>
		<td><?php _e("Active Status");?></td>
		<td>&nbsp;</td>
	</tr>
	<?php $result = $ocdb->query("SELECT idMap,idCategory,IdField,isMandatory,FieldOrder,Active from ".TABLE_PREFIX."categories_field_mapping  where 6=6 ".$filter." order by idMap");
		$row_count = 0;
		while($row = mysql_fetch_array($result)){
			$mapId		=	$row["idMap"] ;
			$mapCat		=	$row["idCategory"];
			$mapCus		=	$row["IdField"];
			$mapMand	=	$row["isMandatory"];
			$mapOrder	=	$row["FieldOrder"];
			$mapActive	=	$row["Active"];
			
			$mapCategoryName = sqlTwoValues("SELECT name,(select name from ".TABLE_PREFIX."categories where idCategory=C.idCategoryParent) ParentName from ".TABLE_PREFIX."categories C where idCategory='$mapCat' ORDER BY ParentName ASC"); //To get the Category Name and Parent Name
			
			$mapCustomName = $ocdb->getValue("SELECT FieldName from ".TABLE_PREFIX."custom_fields where idField='$mapCus'","none"); //To get the custom field name

			$row_count++;
			if ($row_count%2 == 0) $row_class = 'class="odd"';
			else $row_class = 'class="even"';

	?>
	<tr <?php echo $row_class;?>>      
		<td><?php echo $mapCategoryName[1] . "&nbsp;>&nbsp;". $mapCategoryName[0];?></td>
		<td><?php echo ucwords($mapCustomName); ?></td>
		<td><?php echo $mapOrder;?></td>
		<td><?php echo $mapActive;?></td>
		<td class="action">
			<a href="" onclick="editMapField('<?php echo $mapId; ?>');return false;" class="edit"><?php _e("Edit");?></a> 
			| <a href="" onclick="deleteMapField('<?php echo $mapId;?>');return false;" class="delete"><?php _e("Delete");?></a>
		<div style="display:none;" id="mapcat-<?php echo $mapId; ?>"><?php echo $mapCat;?></div>
		<div style="display:none;" id="mapcus-<?php echo $mapId; ?>"><?php echo $mapCus;?></div>
		<div style="display:none;" id="mapmand-<?php echo $mapId; ?>"><?php echo $mapMand;?></div>
		<div style="display:none;" id="maporder-<?php echo $mapId; ?>"><?php echo $mapOrder;?></div>
		<div style="display:none;" id="mapactive-<?php echo $mapId; ?>"><?php echo $mapActive; ?></div>
		<div style="display:none;" id="mapcatcomb-<?php echo $mapId; ?>"><?php echo $mapCategoryName[0];?></div>
		<div style="display:none;" id="mapcuscomb-<?php echo $mapId; ?>"><?php echo $mapCustomName;?></div>
		</td>
	</tr>
	<?php } ?>
</table>
<?php
require_once('footer.php');
?>