<?php
require_once('access.php');
require_once('header.php');
$RandomStr = md5(microtime());
$token = substr($RandomStr,0,5);
?>
<script type="text/javascript">
	function validateUpload(obj) {
		if(obj.cparent.value == 0) {
			alert("Please select a category");
			obj.cparent.focus();
			return false;
		}
		if(obj.smallImage.value == '' && obj.mediumImage.value == '' && obj.thumbImage.value == '') {
			alert("Please choose at lease one image");
			obj.smallImage.focus();
			return false;
		}
		var smallFlag = 0;
		var mediumFlag = 0;
		var thumbFlag = 0;

		if (obj.smallImage.value != "")
		{
			var extensions = new Array("jpg","jpeg","gif","png","bmp");	
			var image_file = obj.smallImage.value;			
			var image_length = obj.smallImage.value.length;			
			var pos = image_file.lastIndexOf('.') + 1;			
			var ext = image_file.substring(pos, image_length);			
			var final_ext = ext.toLowerCase();			
			for (i = 0; i < extensions.length; i++)
			{
				if(extensions[i] == final_ext)
				{
					smallFlag = 1;
				}
			}
			if(smallFlag == 0) {
				alert("You must upload an image file with one of the following extensions: "+ extensions.join(', ') +".");
				obj.smallImage.focus();
				return false;
			}
		}
		if (obj.mediumImage.value != "")
		{
			var extensions = new Array("jpg","jpeg","gif","png","bmp");		
			var image_file = obj.mediumImage.value;			
			var image_length = obj.mediumImage.value.length;			
			var pos = image_file.lastIndexOf('.') + 1;			
			var ext = image_file.substring(pos, image_length);			
			var final_ext = ext.toLowerCase();			
			for (i = 0; i < extensions.length; i++)
			{
				if(extensions[i] == final_ext)
				{
					mediumFlag = 1;
				}
			}
			if(mediumFlag == 0) {
				alert("You must upload an image file with one of the following extensions: "+ extensions.join(', ') +".");
				obj.mediumImage.focus();
				return false;
			}
		 }
		if (obj.thumbImage.value != "")
		{
			var extensions = new Array("jpg","jpeg","gif","png","bmp");			
			var image_file = obj.thumbImage.value;			
			var image_length = obj.thumbImage.value.length;			
			var pos = image_file.lastIndexOf('.') + 1;			
			var ext = image_file.substring(pos, image_length);			
			var final_ext = ext.toLowerCase();			
			for (i = 0; i < extensions.length; i++)
			{
				if(extensions[i] == final_ext) {
					thumbFlag = 1;
				}
			}
			if(thumbFlag == 0) {
				alert("You must upload an image file with one of the following extensions: "+ extensions.join(', ') +".");
				obj.thumbImage.focus();
				return false;
			}
		}
	}
	
	function newImage(){
		d = document.image;
		d.iid.value = "";
		d.cparent.value = "";
		d.smallImageSrc.src = "";
		d.mediumImageSrc.src = "";
		d.thumbImageSrc.src = "";
		d.action.value ="new";
		d.submitImage.value ="<?php _e("New Image");?>";
		document.getElementById("form-tab").innerHTML ="<?php _e("New Image");?>";
		show("formImage");
		location.href = "#formImage";
	}	
	function editImage(iid, iparent){
		d = document.image;
		d.iid.value = iid;
		d.cparent.value = iparent;
		d.smallImageSrc.src = document.getElementById('small-'+iid).innerHTML;//cdesc;
		d.mediumImageSrc.src = document.getElementById('medium-'+iid).innerHTML;//cdesc;
		d.thumbImageSrc.src = document.getElementById('thumb-'+iid).innerHTML;//cdesc;
		d.action.value ="edit";
		d.submitImage.value ="<?php _e("Edit Image");?>";
		document.getElementById("form-tab").innerHTML ="<?php _e("Edit Image");?>";
		show("formImage");
		location.href = "#formImage";
	}	
	function deleteImage(imageId){
		if(confirm('<?php _e("Delete ?"); ?>'))
		window.location = "imageupload.php?action=delete&iid=" + imageId;
	}
</script>
<h2><?php _e("Image Upload"); ?></h2>
<?php
function catSlug($ParentId,$id=""){ //try to prevent duplicated categories
	$ocdb=phpMyDB::GetInstance();
		

	if (is_numeric($id)) 
		$query="SELECT idCategory FROM ".TABLE_PREFIX."menu_image where (idCategory='$ParentId') and (idImage <> '$id') limit 1"; 
	else  
	{ $query="SELECT idCategory FROM ".TABLE_PREFIX."menu_image where (idCategory='$ParentId') limit 1"; }
	$res=$ocdb->getValue($query,"none");

	if ($res==false) return $ParentId; 
	else return false; 
}

//actions
if (cP("action")!=""||cG("action")!=""){
	$action=cG("action");
	if ($action=="")$action=cP("action");
	
	$imageQuery = mysql_query("select smallImage,mediumImage,thumbImage from ".TABLE_PREFIX."menu_image where idCategory='".cP("cparent")."'");

	$imageRow					=	mysql_fetch_array($imageQuery);
	$oldSmallimage				=	$imageRow['smallImage'];
	$oldMediumimage				=	$imageRow['mediumImage'];
	$oldThumbimage				=	$imageRow['thumbImage'];

	if ($action=="new"){
		$imageSlug=catSlug(cP("cparent"));
		if ($imageSlug!=false){  //no exists insert
						
			$smallImage			=	$token.$_FILES['smallImage']['name'];
			$mediumImage		=	$token.$_FILES['mediumImage']['name'];
			$thumbImage			=	$token.$_FILES['thumbImage']['name'];

			$smallPath			=	"../images/menus/smallimage/";
			$smallTmp			=	$_FILES['smallImage']['tmp_name'];
			$filepath			=	$smallPath.$smallImage;
			move_uploaded_file($smallTmp,$filepath);
			
			$mediumPath			=	"../images/menus/mediumimage/";
			$mediumTmp			=	$_FILES['mediumImage']['tmp_name'];
			$filepath			=	$mediumPath.$mediumImage;
			move_uploaded_file($mediumTmp,$filepath);

			$thumbPath			=	"../images/menus/thumbimage/";
			$thumbTmp			=	$_FILES['thumbImage']['tmp_name'];
			$filepath			=	$thumbPath.$thumbImage;
			move_uploaded_file($thumbTmp,$filepath);

			if($_FILES['smallImage']['name'] == '') {
				$smallImage		=	'';
			}
			if($_FILES['mediumImage']['name'] == '') {
				$mediumImage	=	'';
			}
			if($_FILES['thumbImage']['name'] == '') {
				$thumbImage		=	'';
			}

			$ocdb->insert(TABLE_PREFIX."menu_image (idCategory,smallImage,mediumImage,thumbImage,updatedTime)",
			"'".cP("cparent")."','$smallImage','$mediumImage','$thumbImage',NOW()");
		}
		else { 

			$smallImage			=	$token.$_FILES['smallImage']['name'];
			$mediumImage		=	$token.$_FILES['mediumImage']['name'];
			$thumbImage			=	$token.$_FILES['thumbImage']['name'];
			$smallPath			=	"../images/menus/smallimage/";
			$smallTmp			=	$_FILES['smallImage']['tmp_name'];
			$smallFullPath		=	$smallPath.$smallImage;
			
			$mediumPath			=	"../images/menus/mediumimage/";
			$mediumTmp			=	$_FILES['mediumImage']['tmp_name'];
			$mediumFullPath		=	$mediumPath.$mediumImage;
			
			$thumbPath			=	"../images/menus/thumbimage/";
			$thumbTmp			=	$_FILES['thumbImage']['tmp_name'];
			$thumbFullPath		=	$thumbPath.$thumbImage;

			if($_FILES['smallImage']['name'] != '' && $_FILES['mediumImage']['name'] != '' && $_FILES['thumbImage']['name'] != '') {
				unlink($smallPath.$oldSmallimage);
				unlink($mediumPath.$oldMediumimage);
				unlink($thumbPath.$oldThumbimage);				
				$updateVal			=	"smallImage='$smallImage',mediumImage='$mediumImage',thumbImage='$thumbImage',updatedTime=NOW()";
			}
			else if($_FILES['smallImage']['name'] != '' && $_FILES['mediumImage']['name'] != '' && $_FILES['thumbImage']['name'] == '') {
				unlink($smallPath.$oldSmallimage);
				unlink($mediumPath.$oldMediumimage);
				$updateVal			=	"smallImage='$smallImage',mediumImage='$mediumImage',updatedTime=NOW()";
			}
			else if($_FILES['smallImage']['name'] != '' && $_FILES['mediumImage']['name'] == '' && $_FILES['thumbImage']['name'] != '') {
				unlink($smallPath.$oldSmallimage);
				unlink($thumbPath.$oldThumbimage);
				$updateVal			=	"smallImage='$smallImage',thumbImage='$thumbImage',updatedTime=NOW()";
			}
			else if($_FILES['smallImage']['name'] == '' && $_FILES['mediumImage']['name'] != '' && $_FILES['thumbImage']['name'] != '') {
				unlink($mediumPath.$oldMediumimage);
				unlink($thumbPath.$oldThumbimage);
				$updateVal			=	"mediumImage='$mediumImage',thumbImage='$thumbImage',updatedTime=NOW()";
			}
			else if($_FILES['smallImage']['name'] != '' && $_FILES['mediumImage']['name'] == '' && $_FILES['thumbImage']['name'] == '') {
				unlink($smallPath.$oldSmallimage);
				$updateVal			=	"smallImage='$smallImage',updatedTime=NOW()";
			}
			else if($_FILES['smallImage']['name'] == '' && $_FILES['mediumImage']['name'] != '' && $_FILES['thumbImage']['name'] == '') {
				unlink($mediumPath.$oldMediumimage);
				$updateVal			= "mediumImage='$mediumImage',updatedTime=NOW()";
			}
			else if($_FILES['smallImage']['name'] == '' && $_FILES['mediumImage']['name'] == '' && $_FILES['thumbImage']['name'] != '') {
				unlink($thumbPath.$oldThumbimage);
				$updateVal			= "thumbImage='$thumbImage',updatedTime=NOW()";
			}

			move_uploaded_file($smallTmp,$smallFullPath);				
			move_uploaded_file($mediumTmp,$mediumFullPath);
			move_uploaded_file($thumbTmp,$thumbFullPath);

			$query="update ".TABLE_PREFIX."menu_image set $updateVal where idCategory=".cP("cparent");
			$ocdb->query($query);
		}

	}
	elseif ($action=="delete"){		
		$ocdb->delete(TABLE_PREFIX."menu_image","idImage=".cG("iid"));
		//echo "Deleted";
	}
	elseif ($action=="edit"){

		$imageSlug=catSlug(cP("cparent"),cP("iid"));
		if ($imageSlug!=false){  //no exists insert
			
			$smallImage			=	$token.$_FILES['smallImage']['name'];
			$mediumImage		=	$token.$_FILES['mediumImage']['name'];
			$thumbImage			=	$token.$_FILES['thumbImage']['name'];

			$smallPath			=	"../images/menus/smallimage/";
			$smallTmp			=	$_FILES['smallImage']['tmp_name'];
			$filepath			=	$smallPath.$smallImage;
			move_uploaded_file($smallTmp,$filepath);
			
			$mediumPath			=	"../images/menus/mediumimage/";
			$mediumTmp			=	$_FILES['mediumImage']['tmp_name'];
			$filepath			=	$mediumPath.$mediumImage;
			move_uploaded_file($mediumTmp,$filepath);

			$thumbPath			=	"../images/menus/thumbimage/";
			$thumbTmp			=	$_FILES['thumbImage']['tmp_name'];
			$filepath			=	$thumbPath.$thumbImage;
			move_uploaded_file($thumbTmp,$filepath);

			if($_FILES['smallImage']['name'] == '') {
				$smallImage		=	'';
			}
			if($_FILES['mediumImage']['name'] == '') {
				$mediumImage	=	'';
			}
			if($_FILES['thumbImage']['name'] == '') {
				$thumbImage		=	'';
			}

			$ocdb->insert(TABLE_PREFIX."menu_image (idCategory,smallImage,mediumImage,thumbImage,updatedTime)",
			"'".cP("cparent")."','$smallImage','$mediumImage','$thumbImage',NOW()");
		}
		else {
			$smallImage			=	$token.$_FILES['smallImage']['name'];
			$mediumImage		=	$token.$_FILES['mediumImage']['name'];
			$thumbImage			=	$token.$_FILES['thumbImage']['name'];
			$smallPath			=	"../images/menus/smallimage/";
			$smallTmp			=	$_FILES['smallImage']['tmp_name'];
			$smallFullPath		=	$smallPath.$smallImage;
			
			$mediumPath			=	"../images/menus/mediumimage/";
			$mediumTmp			=	$_FILES['mediumImage']['tmp_name'];
			$mediumFullPath		=	$mediumPath.$mediumImage;
			
			$thumbPath			=	"../images/menus/thumbimage/";
			$thumbTmp			=	$_FILES['thumbImage']['tmp_name'];
			$thumbFullPath		=	$thumbPath.$thumbImage;

			if($_FILES['smallImage']['name'] != '' && $_FILES['mediumImage']['name'] != '' && $_FILES['thumbImage']['name'] != '') {
				unlink($smallPath.$oldSmallimage);
				unlink($mediumPath.$oldMediumimage);
				unlink($thumbPath.$oldThumbimage);				
				$updateVal			=	"smallImage='$smallImage',mediumImage='$mediumImage',thumbImage='$thumbImage',updatedTime=NOW()";
			}
			else if($_FILES['smallImage']['name'] != '' && $_FILES['mediumImage']['name'] != '' && $_FILES['thumbImage']['name'] == '') {
				unlink($smallPath.$oldSmallimage);
				unlink($mediumPath.$oldMediumimage);
				$updateVal			=	"smallImage='$smallImage',mediumImage='$mediumImage',updatedTime=NOW()";
			}
			else if($_FILES['smallImage']['name'] != '' && $_FILES['mediumImage']['name'] == '' && $_FILES['thumbImage']['name'] != '') {
				unlink($smallPath.$oldSmallimage);
				unlink($thumbPath.$oldThumbimage);
				$updateVal			=	"smallImage='$smallImage',thumbImage='$thumbImage',updatedTime=NOW()";
			}
			else if($_FILES['smallImage']['name'] == '' && $_FILES['mediumImage']['name'] != '' && $_FILES['thumbImage']['name'] != '') {
				unlink($mediumPath.$oldMediumimage);
				unlink($thumbPath.$oldThumbimage);
				$updateVal			=	"mediumImage='$mediumImage',thumbImage='$thumbImage',updatedTime=NOW()";
			}
			else if($_FILES['smallImage']['name'] != '' && $_FILES['mediumImage']['name'] == '' && $_FILES['thumbImage']['name'] == '') {
				unlink($smallPath.$oldSmallimage);
				$updateVal			=	"smallImage='$smallImage',updatedTime=NOW()";
			}
			else if($_FILES['smallImage']['name'] == '' && $_FILES['mediumImage']['name'] != '' && $_FILES['thumbImage']['name'] == '') {
				unlink($mediumPath.$oldMediumimage);
				$updateVal			= "mediumImage='$mediumImage',updatedTime=NOW()";
			}
			else if($_FILES['smallImage']['name'] == '' && $_FILES['mediumImage']['name'] == '' && $_FILES['thumbImage']['name'] != '') {
				unlink($thumbPath.$oldThumbimage);
				$updateVal			= "thumbImage='$thumbImage',updatedTime=NOW()";
			}

			move_uploaded_file($smallTmp,$smallFullPath);				
			move_uploaded_file($mediumTmp,$mediumFullPath);
			move_uploaded_file($thumbTmp,$thumbFullPath);

			$query="update ".TABLE_PREFIX."menu_image set $updateVal where idCategory=".cP("cparent");
			$ocdb->query($query);
		}
	}
	elseif ($action=="filter" && is_numeric(cG("iid"))){
		$filter = ' and idCategory='.cG("iid");
	}
}
?>
<p class="desc"><?php //_e("Manage your images");?>
<?php 
		echo '<form  action="imageupload.php" method="get" >';
				$query="SELECT idCategory,name FROM ".TABLE_PREFIX."categories C where idCategoryParent=0  order by  `order`";
				sqlOptionGroup($query,"iid",cG("iid"));
		echo'<input type="hidden" name="action" value="filter" />
			<input type="submit" class="button-submit" value="'.T_('Filter').'" /></form>';	
	?></p>
<div id='formImage' style="display:none;">
	<div id="form-tab" class="form-tab"></div>
	<div class="clear"></div>
	<form name="image" action="imageupload.php" method="post" enctype="multipart/form-data" onsubmit="return validateUpload(this);">
		<fieldset>			
			<p>
            	<label><?php _e("Parent");?></label>
                <?php sqlOption("select idCategory,name from ".TABLE_PREFIX."categories where idCategoryParent=0","cparent","");?>
        	</p>
			<p>
            	<label><?php _e("Small Image");?></label>
                <input type="file" name="smallImage" id="smallImage" /><img src="" width="30" height="30" name="smallImageSrc" id="smallImageSrc"/>
        	</p>
			<p>
            	<label><?php _e("Medium Image");?></label>
                <input type="file" name="mediumImage" id="mediumImage" /><img src="" width="30" height="30" name="mediumImageSrc" id="mediumImageSrc"/>
        	</p>
			<p>
            	<label><?php _e("Thumb Image");?></label>
                <input type="file" name="thumbImage" id="thumbImage" /><img src="" width="30" height="30" name="thumbImageSrc" id="thumbImageSrc"/>
        	</p>
			<input id="submitImage" type="submit" value="" class=" button-submit" />
			<input type="submit" value="<?php _e("Cancel");?>" class="button-cancel" onclick="hide('formImage');return false;" />
			<input type="hidden" name="iid" value="" />
			<input type="hidden" name="action" value="" />
		</fieldset>
	</form>
</div>
<div class="add_link"><a href="" onclick="newImage();return false;"><?php _e("New Image");?></a></div>
<table>
	<tr class="thead">
		<td><?php _e("Id");?></td>
		<td><?php _e("Parent");?></td>
		<td><?php _e("Small Image");?></td>
		<td><?php _e("Medium Image");?></td>
		<td><?php _e("Thumb Image");?></td>
		<td>&nbsp;</td>
	</tr>
	<?php 
		$result = $ocdb->query("SELECT *,(select name from ".TABLE_PREFIX."categories  where idCategory=C.idCategory) ParentName
								FROM ".TABLE_PREFIX."menu_image C where 6=6 ".$filter." order by idImage");
		$row_count = 0;
		while ($row = mysql_fetch_array($result)){
			$idImage		=	$row["idImage"] ;
			$ParentName		=	$row["ParentName"];
			$smallImage		=	$row["smallImage"];
			$mediumImage	=	$row["mediumImage"];
			$thumbImage		=	$row["thumbImage"];
			$idCategory		=	$row["idCategory"];

			$row_count++;
			if ($row_count%2 == 0) $row_class = 'class="odd"';
			else $row_class = 'class="even"';

	?>
	<tr <?php echo $row_class;?>>      
		<td><?php echo $idImage;?></td>
		<td><?php echo $ParentName;?></td>
		<td><img src="../images/menus/smallimage/<?php echo $smallImage;?>" width="30" height="30" /></td>
		<td><img src="../images/menus/mediumimage/<?php echo $mediumImage;?>" width="30" height="30" /></td>
		<td><img src="../images/menus/thumbimage/<?php echo $thumbImage;?>" width="30" height="30" /></td>
		<td class="action">
			<a href="" onclick="editImage('<?php echo $idImage; ?>','<?php echo $idCategory;?>');return false;" class="edit"><?php _e("Edit");?></a> 
			| <a href="" onclick="deleteImage('<?php echo $idImage;?>');return false;" class="delete"><?php _e("Delete");?></a>
		<div style="display:none;" id="parentname-<?php echo $idImage; ?>"><?php echo $ParentName;?></div>
		<div style="display:none;" id="small-<?php echo $idImage; ?>">../images/menus/smallimage/<?php echo $smallImage;?></div>
		<div style="display:none;" id="medium-<?php echo $idImage; ?>">../images/menus/mediumimage/<?php echo $mediumImage;?></div>
		<div style="display:none;" id="thumb-<?php echo $idImage; ?>">../images/menus/thumbimage/<?php echo $thumbImage;?></div>
		</td>
	</tr>
	<?php } ?>
</table>
<?php
require_once('footer.php');
?>