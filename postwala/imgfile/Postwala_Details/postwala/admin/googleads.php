<?php
require_once('access.php');
require_once('header.php');
?>
<script type="text/javascript">
	function validateAdv(obj) {
		if(obj.banName.value == 0) {
			alert("Please enter the banner name");
			obj.banName.focus();
			return false;
		}
		if(obj.banSource.value == 0) {
			alert("Please enter the banner source");
			obj.banSource.focus();
			return false;
		}
		if(obj.banCode.value == 0) {
			alert("Please enter the banner code");
			obj.banCode.focus();
			return false;
		}		
	}	
	function newAd(){
		d = document.adv;
		d.bid.value = "";
		d.banName.value = "";
		d.banSource.value  = "";
		d.banCode.value = "";
		d.action.value ="new";
		d.submitAd.value ="<?php _e("New Ad");?>";
		document.getElementById("form-tab").innerHTML ="<?php _e("New Ad");?>";
		show("formAd");
		location.href = "#formAd";
	}	
	function editAd(bid){
		d						=	document.adv;
		d.bid.value				=	bid;
		d.banName.value			=	document.getElementById('banname-'+bid).innerHTML;//banner name;
		d.banSource.value		=	document.getElementById('bansource-'+bid).innerHTML;//banner source;
		d.banCode.value			=	document.getElementById('bancode-'+bid).innerHTML; //banner code;
		//d.banCode.value			=	document.getElementById('bancode-'+bid).innerHTML;//banner code;
		//tinyMCE.get('banCode').setContent(d.banCode.value);
		d.action.value			=	"edit";
		d.submitAd.value		=	"<?php _e("Edit Ad");?>";
		document.getElementById("form-tab").innerHTML ="<?php _e("Edit Ad");?>";
		show("formAd");
		location.href			=	"#formAd";
	}	
	function deleteAd(banId){
		if(confirm('<?php _e("Delete ?"); ?>'))
		window.location = "googleads.php?action=delete&bid=" + banId;
	}
</script>
<h2><?php _e("Google Adv"); ?></h2>
<?php
function catSlug($banName,$id=""){ //try to prevent duplicated categories
	$ocdb=phpMyDB::GetInstance();

	if (is_numeric($id)) $query="SELECT bannerName FROM ".TABLE_PREFIX."banners where (bannerName='$banName') and (bannerID <> $id) limit 1";
	else $query="SELECT bannerName FROM ".TABLE_PREFIX."banners where (bannerName = '$banName') limit 1";
	$res=$ocdb->getValue($query,"none");

	if ($res==false) return $banName; 
	else return false; 
}

//actions
if (cP("action")!=""||cG("action")!=""){
	$action=cG("action");
	if ($action=="")$action=cP("action");
	
	if ($action=="new"){
		$nameSlug=catSlug(cP("banName"));
		if ($nameSlug!=false){  //no exists insert
			$banCode =	$_POST["banCode"];
			$banCode =	str_replace("<","bancode",$banCode);
			$banCode =	str_replace(">","rightone",$banCode);
			$ocdb->insert(TABLE_PREFIX."banners (bannerName,bannerSource,bannerCode,createdTs)",
			"'".cP("banName")."','".cP("banSource")."','".$banCode."',NOW()");
		}
		else _e("Banner already exists");
	}
	elseif ($action=="delete"){		
		$ocdb->delete(TABLE_PREFIX."banners","bannerID=".cG("bid"));
		//echo "Deleted";
	}
	elseif ($action=="edit"){
		$nameSlug=catSlug(cP("banName"),cP("bid"));
		if ($nameSlug!=false){  //no exists update
			$banCode =	$_POST["banCode"];
			$banCode =	str_replace("<","bancode",$banCode);
			$banCode =	str_replace(">","rightone",$banCode);	
			$query="update ".TABLE_PREFIX."banners set bannerName='".cP("banName")."',bannerSource='".cP("banSource")."',bannerCode='".$banCode."',createdTs=NOW() 
					where bannerID=".cP("bid");
			$ocdb->query($query);
		}
		else _e("Banner already exists");
		//echo "Edit: $query";
	}
	elseif ($action=="filter" && is_numeric(cG("bid"))){
		$filter = ' and idCategory='.cG("bid");
	}
}
?>
<p class="desc"><?php _e("Manage your Ads");?>
<?php 
		echo '<form  action="googleads.php" method="get" >';
				$query="SELECT bannerID,bannerName FROM ".TABLE_PREFIX."banners order by bannerName";
				sqlOptionGroup($query,"bid",cG("bid"));
		echo'<input type="hidden" name="action" value="filter" />
			<input type="submit" class="button-submit" value="'.T_('Filter').'" /></form>';	
	?></p>
<div id='formAd' style="display:none;">
	<div id="form-tab" class="form-tab"></div>
	<div class="clear"></div>
	<form name="adv" action="googleads.php" method="post" onsubmit="return validateAdv	(this);">
		<fieldset>			
			<p>
            	<label><?php _e("Banner Name");?></label>
                <input type="text" name="banName" id="banName" value=""/>
        	</p>
			<p>
            	<label><?php _e("Banner Source");?></label>
                <input type="text" name="banSource" id="banSource" value="" />
        	</p>
			<p>
            	<label><?php _e("Banner Code");?></label>
				<textarea name="banCode" id="banCode"></textarea>
        	</p>
			<input id="submitAd" type="submit" value="" class=" button-submit" />
			<input type="submit" value="<?php _e("Cancel");?>" class="button-cancel" onclick="hide('formAd');return false;" />
			<input type="hidden" name="bid" value="" />
			<input type="hidden" name="action" value="" />
		</fieldset>
	</form>
</div>
<div class="add_link"><a href="" onclick="newAd();return false;"><?php _e("New Ad");?></a></div>
<table>
	<tr class="thead">
		<td><?php _e("Id");?></td>
		<td><?php _e("Banner Name");?></td>
		<td><?php _e("Banner Source");?></td>
		<td><?php _e("Banner Code");?></td>
		<td>&nbsp;</td>
	</tr>
	<?php 
		$result = $ocdb->query("SELECT bannerName,bannerSource,bannerCode,bannerID from ".TABLE_PREFIX."banners  where 6=6 ".$filter." order by bannerID");
		$row_count = 0;
		while ($row = mysql_fetch_array($result)){
			$idBan			=	$row["bannerID"] ;
			$banName		=	$row["bannerName"];
			$banSource		=	$row["bannerSource"];
			$banCode		=	$row["bannerCode"];

			$row_count++;
			if ($row_count%2 == 0) $row_class = 'class="odd"';
			else $row_class = 'class="even"';

	?>
	<tr <?php echo $row_class;?>>      
		<td><?php echo $idBan;?></td>
		<td><?php echo $banName;?></td>
		<td><?php echo $banSource;?></td>
		<td><?php echo $banCode;?></td>
		<td class="action">
			<a href="" onclick="editAd('<?php echo $idBan; ?>','<?php echo $idCategory;?>');return false;" class="edit"><?php _e("Edit");?></a> 
			| <a href="" onclick="deleteAd('<?php echo $idBan;?>');return false;" class="delete"><?php _e("Delete");?></a>
		<div style="display:none;" id="banname-<?php echo $idBan; ?>"><?php echo $banName;?></div>
		<div style="display:none;" id="bansource-<?php echo $idBan; ?>"><?php echo $banSource;?></div>
		<div style="display:none;" id="bancode-<?php echo $idBan; ?>"><?php echo $banCode;?></div>
		</td>
	</tr>
	<?php } ?>
</table>
<?php
require_once('footer.php');
?>