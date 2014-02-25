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
	function newSeo(){
		d = document.seo;
		d.sid.value = "";
		d.seoName.value = "";
		d.seoTitle.value  = "";
		d.seoKey.value = "";
		d.seoDesc.value = "";
		d.action.value ="new";
		d.submitAd.value ="<?php _e("New Seo");?>";
		document.getElementById("form-tab").innerHTML ="<?php _e("New Seo");?>";
		show("formSeo");
		location.href = "#formSeo";
	}	
	function editSeo(sid){
		d						=	document.seo;
		d.sid.value				=	sid;
		d.seoName.value			=	document.getElementById('seoname-'+sid).innerHTML;//seo name;
		d.seoFriend.value		=	document.getElementById('seofriend-'+sid).innerHTML;//seo title;
		d.seoTitle.value		=	document.getElementById('seotitle-'+sid).innerHTML;//seo title;
		d.seoKey.value			=	document.getElementById('seokey-'+sid).innerHTML; //seo keyword;
		d.seoDesc.value			=	document.getElementById('seodesc-'+sid).innerHTML; //seo description;
		d.action.value			=	"edit";
		d.submitAd.value		=	"<?php _e("Edit Seo");?>";
		document.getElementById("form-tab").innerHTML ="<?php _e("Edit Seo");?>";
		show("formSeo");
		location.href			=	"#formSeo";
	}	
	function deleteSeo(banId){
		if(confirm('<?php _e("Delete ?"); ?>'))
		window.location = "searchengine.php?action=delete&sid=" + banId;
	}
</script>
<h2><?php _e("SEO"); ?></h2>
<?php
function catSlug($seoName,$id=""){ //try to prevent duplicated categories
	$ocdb=phpMyDB::GetInstance();

	if (is_numeric($id)) $query="SELECT seoName FROM ".TABLE_PREFIX."seo where (seoName='$seoName') and (idSeo <> $id) limit 1";
	else $query="SELECT seoName FROM ".TABLE_PREFIX."seo where (seoName = '$seoName') limit 1";
	$res=$ocdb->getValue($query,"none");

	if ($res==false) return $seoName; 
	else return false; 
}

//actions
if (cP("action")!=""||cG("action")!=""){
	$action=cG("action");
	if ($action=="")$action=cP("action");
	
	if ($action=="new"){
		$nameSlug=catSlug(cP("seoName"));
		if ($nameSlug!=false){  //no exists insert

			$ocdb->insert(TABLE_PREFIX."seo (seoName,seoFriendlyName,seoTitle,seoKeyword,seoDescription,insertedDate)",
			"'".cP("seoName")."','".cP("seoFriend")."','".cP("seoTitle")."','".cP("seoKey")."','".cP("seoDesc")."',NOW()");
		}
		else _e("Seo already exists");
	}
	elseif ($action=="delete"){		
		$ocdb->delete(TABLE_PREFIX."seo","idSeo=".cG("sid"));
		//echo "Deleted";
	}
	elseif ($action=="edit"){
		$nameSlug=catSlug(cP("seoName"),cP("sid"));
		if ($nameSlug!=false){  //no exists update

			$query="update ".TABLE_PREFIX."seo set seoName='".cP("seoName")."',seoFriendlyName='".cP("seoFriend")."',seoTitle='".cP("seoTitle")."',seoKeyword='".cP("seoKey")."',seoDescription='".cP("seoDesc")."',updatedDate=NOW() 
					where idSeo=".cP("sid");
			$ocdb->query($query);
		}
		else _e("Seo already exists");
		//echo "Edit: $query";
	}
	elseif ($action=="filter" && is_numeric(cG("sid"))){
		$filter = ' and idSeo='.cG("sid");
	}
}
?>
<p class="desc"><?php _e("Manage your Seos");?>
<?php 
		echo '<form  action="searchengine.php" method="get" >';
				$query="SELECT idSeo,seoName FROM ".TABLE_PREFIX."seo order by seoName";
				sqlOptionGroup($query,"sid",cG("sid"));
		echo'<input type="hidden" name="action" value="filter" />
			<input type="submit" class="button-submit" value="'.T_('Filter').'" /></form>';	
	?></p>
<div id='formSeo' style="display:none;">
	<div id="form-tab" class="form-tab"></div>
	<div class="clear"></div>
	<form name="seo" action="searchengine.php" method="post" onsubmit="return validateAdv	(this);">
		<fieldset>			
			<p>
            	<label><?php _e("Seo Name");?></label>
                <input type="text" name="seoName" id="seoName" value=""/>
        	</p>
			<p>
            	<label><?php _e("Seo Friendly Name");?></label>
				<textarea name="seoFriend" id="seoFriend"></textarea>
        	</p>
			<p>
            	<label><?php _e("Seo Title");?></label>
				<textarea name="seoTitle" id="seoTitle"></textarea>
        	</p>
			<p>
            	<label><?php _e("Seo Keyword");?></label>
				<textarea name="seoKey" id="seoKey"></textarea>
        	</p>
			<p>
            	<label><?php _e("Seo Description");?></label>
				<textarea name="seoDesc" id="seoDesc"></textarea>
        	</p>
			<input id="submitAd" type="submit" value="" class=" button-submit" />
			<input type="submit" value="<?php _e("Cancel");?>" class="button-cancel" onclick="hide('formSeo');return false;" />
			<input type="hidden" name="sid" value="" />
			<input type="hidden" name="action" value="" />
		</fieldset>
	</form>
</div>
<div class="add_link"><a href="" onclick="newSeo();return false;"><?php _e("New Seo");?></a></div>
<table>
	<tr class="thead">
		<td><?php _e("Id");?></td>
		<td><?php _e("Seo Name");?></td>
		<td><?php _e("Seo Title");?></td>
		<td><?php _e("Seo Keyword");?></td>
		<td><?php _e("Seo Description");?></td>
		<td>&nbsp;</td>
	</tr>
	<?php 
		$result = $ocdb->query("SELECT seoName,seoFriendlyName,seoTitle,seoKeyword,seoDescription,idSeo from ".TABLE_PREFIX."seo where 6=6 ".$filter." order by idSeo");
		$row_count = 0;
		while ($row = mysql_fetch_array($result)){
			$idSeo			=	$row["idSeo"] ;
			$seoName		=	$row["seoName"];
			$seoFriend		=	$row["seoFriendlyName"];
			$seoTitle		=	$row["seoTitle"];
			$seoKey			=	$row["seoKeyword"];
			$seoDesc		=	$row["seoDescription"];

			$row_count++;
			if ($row_count%2 == 0) $row_class = 'class="odd"';
			else $row_class = 'class="even"';

	?>
	<tr <?php echo $row_class;?>>      
		<td><?php echo $idSeo;?></td>
		<td><?php echo $seoName;?></td>
		<td><?php echo $seoTitle;?></td>
		<td><?php echo $seoKey;?></td>
		<td><?php echo $seoDesc;?></td>
		<td class="action">
			<a href="" onclick="editSeo('<?php echo $idSeo; ?>');return false;" class="edit"><?php _e("Edit");?></a> 
			| <a href="" onclick="deleteSeo('<?php echo $idSeo;?>');return false;" class="delete"><?php _e("Delete");?></a>
		<div style="display:none;" id="seoname-<?php echo $idSeo; ?>"><?php echo $seoName;?></div>
		<div style="display:none;" id="seofriend-<?php echo $idSeo; ?>"><?php echo $seoFriend;?></div>
		<div style="display:none;" id="seotitle-<?php echo $idSeo; ?>"><?php echo $seoTitle;?></div>
		<div style="display:none;" id="seokey-<?php echo $idSeo; ?>"><?php echo $seoKey;?></div>
		<div style="display:none;" id="seodesc-<?php echo $idSeo; ?>"><?php echo $seoDesc;?></div>
		</td>
	</tr>
	<?php } ?>
</table>
<?php
require_once('footer.php');
?>