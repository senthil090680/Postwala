<?php
require_once('access.php');
require_once('header.php');
$RandomStr = md5(microtime());
$token = substr($RandomStr,0,5);
?>
<script type="text/javascript">
	function validateFinalLink(obj) {
		if(obj.cparent.value == 0) {
			alert("Please select a category");
			obj.cparent.focus();
			return false;
		}
		if(obj.finalLink.value == '') {
			alert("Please enter the link");
			obj.finalLink.focus();
			return false;
		}		
	}
	
	function newFinalLink(){
		d = document.finalLink;
		d.flid.value			=	"";
		d.cparent.value			=	"";
		d.finalLink.value		=	"";
		d.action.value			=	"new";
		d.submitFinalLink.value =	"<?php _e("New Final Link");?>";
		document.getElementById("form-tab").innerHTML ="<?php _e("New Final Link");?>";
		show("formFinalLink");
		location.href = "#formFinalLink";
	}	
	function editFinalLink(flid, iparent){
		d = document.finalLink;
		d.flid.value = flid;
		d.cparent.value = iparent;
		d.finalLink.value = document.getElementById('finallink-'+flid).innerHTML;//cdesc;
		d.action.value ="edit";
		d.submitFinalLink.value ="<?php _e("Edit Final Link");?>";
		document.getElementById("form-tab").innerHTML ="<?php _e("Edit Final Link");?>";
		show("formFinalLink");
		location.href = "#formFinalLink";
	}	
	function deleteFinalLink(finalLinkId){
		if(confirm('<?php _e("Delete ?"); ?>'))
		window.location = "menufinallink.php?action=delete&flid=" + finalLinkId;
	}
</script>
<h2><?php _e("Final Link Mapping"); ?></h2>
<?php
function catSlug($ParentId,$id=""){ //try to prevent duplicated categories
	$ocdb=phpMyDB::GetInstance();
		

	if (is_numeric($id)) 
		$query="SELECT idCategory FROM ".TABLE_PREFIX."finallink_menu where (idCategory='$ParentId') and (idMenuFinalLink <> '$id') limit 1"; 
	else  
	{ $query="SELECT idCategory FROM ".TABLE_PREFIX."finallink_menu where (idCategory='$ParentId') limit 1"; }
	$res=$ocdb->getValue($query,"none");

	if ($res==false) return $ParentId; 
	else return false; 
}

//actions
if (cP("action")!=""||cG("action")!=""){
	$action=cG("action");
	if ($action=="")$action=cP("action");	
	if ($action=="new"){
		$finalLinkSlug=catSlug(cP("cparent"));
		if ($finalLinkSlug!=false){  //no exists insert			
			$ocdb->insert(TABLE_PREFIX."finallink_menu (idCategory,FinalLink,updatedTime)",
			"'".cP("cparent")."','".cP("finalLink")."',NOW()");
		}
		else { 			
			$query="update ".TABLE_PREFIX."finallink_menu set FinalLink='".cP("finalLink")."',UpdatedTime=Now() where idCategory=".cP("cparent");
			$ocdb->query($query);
		}

	}
	elseif ($action=="delete"){		
		$ocdb->delete(TABLE_PREFIX."finallink_menu","idMenuFinalLink=".cG("flid"));
		//echo "Deleted";
	}
	elseif ($action=="edit"){

		$finalLinkSlug=catSlug(cP("cparent"),cP("flid"));
		if ($finalLinkSlug!=false){  //no exists insert			
			$ocdb->insert(TABLE_PREFIX."finallink_menu (idCategory,FinalLink,updatedTime)",
			"'".cP("cparent")."','".cP("finalLink")."',NOW()");
		}
		else {
			$query="update ".TABLE_PREFIX."finallink_menu set FinalLink='".cP("finalLink")."',UpdatedTime=Now() where idCategory=".cP("cparent");
			$ocdb->query($query);
		}
	}
	elseif ($action=="filter" && is_numeric(cG("flid"))){
		$filter = ' and idCategory='.cG("flid");
	}
}
?>
<p class="desc"><?php //_e("Manage your images");?>
<?php 
		echo '<form  action="menufinallink.php" method="get" >';
				$query="SELECT idCategory,name FROM ".TABLE_PREFIX."categories C where idCategoryParent=0  order by  `order`";
				sqlOptionGroup($query,"flid",cG("flid"));
		echo'<input type="hidden" name="action" value="filter" />
			<input type="submit" class="button-submit" value="'.T_('Filter').'" /></form>';	
	?></p>
<div id='formFinalLink' style="display:none;">
	<div id="form-tab" class="form-tab"></div>
	<div class="clear"></div>
	<form name="finalLink" action="menufinallink.php" method="post" onsubmit="return validateFinalLink(this);">
		<fieldset>			
			<p>
            	<label><?php _e("Parent");?></label>
                <?php sqlOption("select idCategory,name from ".TABLE_PREFIX."categories where idCategoryParent=0","cparent","");?>
        	</p>
			<p>
            	<label><?php _e("Final Link");?></label>
                <input type="text" name="finalLink" id="finalLink" style="width:350px;" value=""/>
        	</p>
			<input id="submitFinalLink" type="submit" value="" class=" button-submit" />
			<input type="submit" value="<?php _e("Cancel");?>" class="button-cancel" onclick="hide('formFinalLink');return false;" />
			<input type="hidden" name="flid" value="" />
			<input type="hidden" name="action" value="" />
		</fieldset>
	</form>
</div>
<div class="add_link"><a href="" onclick="newFinalLink();return false;"><?php _e("New Final Link");?></a></div>
<table>
	<tr class="thead">
		<td><?php _e("Id");?></td>
		<td><?php _e("Parent");?></td>
		<td><?php _e("Final Link");?></td>
		<td>&nbsp;</td>
	</tr>
	<?php 
		$result = $ocdb->query("SELECT *,(select name from ".TABLE_PREFIX."categories  where idCategory=C.idCategory) ParentName
								FROM ".TABLE_PREFIX."finallink_menu C where 6=6 ".$filter." order by idMenuFinalLink");
		$row_count = 0;
		while ($row = mysql_fetch_array($result)){
			$idMenuFinalLink=	$row["idMenuFinalLink"] ;
			$ParentName		=	$row["ParentName"];
			$FinalLink		=	$row["finalLink"];
			$idCategory		=	$row["idCategory"];

			$row_count++;
			if ($row_count%2 == 0) $row_class = 'class="odd"';
			else $row_class = 'class="even"';

	?>
	<tr <?php echo $row_class;?>>      
		<td><?php echo $idMenuFinalLink;?></td>
		<td><?php echo $ParentName;?></td>
		<td><?php echo $FinalLink;?></td>
		<td class="action">
			<a href="" onclick="editFinalLink('<?php echo $idMenuFinalLink; ?>','<?php echo $idCategory;?>');return false;" class="edit"><?php _e("Edit");?></a> 
			| <a href="" onclick="deleteFinalLink('<?php echo $idMenuFinalLink;?>');return false;" class="delete"><?php _e("Delete");?></a>
		<div style="display:none;" id="parentname-<?php echo $idMenuFinalLink; ?>"><?php echo $ParentName;?></div>
		<div style="display:none;" id="finallink-<?php echo $idMenuFinalLink; ?>"><?php echo $FinalLink;?></div>		
		</td>
	</tr>
	<?php } ?>
</table>
<?php
require_once('footer.php');
?>