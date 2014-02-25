<?php
////////////////////////////////////////////////////////////
function advancedSearchForm($admin=0){//used in the index.php when an search is done
	global $currentCategory,$currentTitle;
	if ($admin==1) $action=SITE_URL.'/admin/listing.php';
	else $action=SITE_URL;
	?>

	
	<form action="<?php echo $action;?>" method="get" onsubmit="return searchvalidate()"><table cellpadding="2" cellspacing="0">
	<tr>    
    <td class="searchlabel"><?php _e("I am Searching for");?>:</td><td class="searchtext"><input class="searchtextbox borderedge <?php if(isset($currentTitle)) { ?> darkcolor <?php } else { ?> lightcolor <?php } ?>" type="text" name="title" id="title" onFocus="validSpanDespSearch(this,'Eg: Mobiles, Real estate, computer course, old mp3');" onBlur="validTextDespSearch(this,'Eg: Mobiles, Real estate, computer course, old mp3','word to search'); validSpanDespSearch(this,'Eg: Mobiles, Real estate, computer course, old mp3');"  <?php if(isset($currentTitle)) { ?> value="<?php echo $currentTitle; ?>" <?php } else { ?> value="Eg: Mobiles, Real estate, computer course, old mp3" <?php } ?> labelValue='word to search' descValue='Eg: Mobiles, Real estate, computer course, old mp3' /></td>

	<?php if (LOCATION){?>
	<td><?php _e("City");?>:</td>
	<td class="searchbut">
    	<?php 
    	global $location;
		$query="SELECT idLocation,name,(select name from ".TABLE_PREFIX."locations where idLocation=C.idLocationParent) 
					FROM ".TABLE_PREFIX."locations C where idLocationParent != 0 order by idLocationParent,idLocation";
        echo sqlOptionGroupSearch($query,"citylocation",$location);
    	?>
	</td>
    <?php }?> 

	<?php if($admin==1) { ?>

	   <tr><td><?php _e("Active");?>:</td> <td>
			<select name="active">
				<option value="1" <?php if(cG("active")=="1" || cG("active")=="")  echo "selected=selected";?> ><?php _e("Yes");?> </option>
				<option value="0" <?php if(cG("active")=="0")  echo "selected=selected";?> ><?php _e("No");?> </option>
			</select>
		</td></tr>
	   <tr><td><?php _e("Reviewed");?>:</td> <td> 
	                <select name="reviewed">
				<option value="1" <?php if(cG("reviewed")=="1" || cG("reviewed")=="" )  echo "selected=selected";?> ><?php _e("Yes");?> </option>
				<option value="0" <?php if(cG("reviewed")=="0")  echo "selected=selected";?> ><?php _e("No");?> </option>
			</select>
		</td></tr>
	
	<?php } ?>
			
	<td>&nbsp;</td><td class="searchbutt"><input type="submit" class="butcolor" value="<?php _e("Search");?>" /></td></tr>
	<!--<tr><td colspan="4" style="padding-left:70px;"></td></tr>-->
	</table></form>
	<?php 
}



////////////////////////////////////////////////////////////
function advancedSearchFormAdmin($admin=0){//used in the search when an advanced search is done
	global $currentCategory;
	if ($admin==1) $action=SITE_URL.'/admin/listing.php';
	else $action=SITE_URL;
	?>	
	<form action="<?php echo $action;?>" method="get"><table cellpadding="2" cellspacing="0">
	<tr><td><?php _e("Category");?>:</td><td> 
	<?php 
	$query="SELECT friendlyName,name,(select name from ".TABLE_PREFIX."categories where idCategory=C.idCategoryParent) FROM ".TABLE_PREFIX."categories C order by idCategoryParent";
	sqlOptionGroup($query,"category",$currentCategory);
	?></td></tr>
	<tr><td><?php _e("Type");?>:</td><td>
		<select id="type" name="type">
			<option value="<?php echo TYPE_OFFER;?>"><?php _e("offer");?></option>
			<option value="<?php echo TYPE_NEED;?>"><?php _e("need");?></option>
		</select>
	</td></tr>
    <?php if (LOCATION){?>
	<tr><td><?php _e("Location");?>:</td><td>
    	<?php 
    	global $location;
		$query="SELECT idLocation,name,(select name from ".TABLE_PREFIX."locations where idLocation=C.idLocationParent) 
					FROM ".TABLE_PREFIX."locations C order by idLocationParent,idLocation";
        echo sqlOptionGroup($query,"location",$location);
    	?>
	</td></tr>
    <?php }?>  
    <tr><td><?php _e("Place");?>:</td><td><input type="text" name="place" value="<?php echo cG("place");?>" /></td></tr>
	<tr><td><?php _e("Title");?>:</td><td><input type="text" name="title" value="<?php echo cG("title");?>" /></td></tr>
	<tr><td><?php _e("Description");?>:</td><td><input type="text" name="desc" value="<?php echo cG("desc");?>" /></td></tr>
	<tr><td><?php _e("Price");?>:</td><td><input type="text" name="price" value="<?php echo cG("price");?>" /></td></tr>
	<tr><td><?php _e("Sort");?>:</td>
		<td>
			<select name="sort">
				<option></option>
				<option value="price-desc" <?php if(cG("sort")=="price-desc")  echo "selected=selected";?> ><?php _e("Price");?> - <?php _e("Desc");?></option>
				<option value="price-asc" <?php if(cG("sort")=="price-asc")  echo "selected=selected";?> ><?php _e("Price");?> - <?php _e("Asc");?></option>
			</select>
		</td></tr>
	
	<?php if($admin==1) { ?>

	   <tr><td><?php _e("Active");?>:</td> <td>
			<select name="active">
				<option value="1" <?php if(cG("active")=="1" || cG("active")=="")  echo "selected=selected";?> ><?php _e("Yes");?> </option>
				<option value="0" <?php if(cG("active")=="0")  echo "selected=selected";?> ><?php _e("No");?> </option>
			</select>
		</td></tr>
	   <tr><td><?php _e("Reviewed");?>:</td> <td> 
	                <select name="reviewed">
				<option value="1" <?php if(cG("reviewed")=="1" || cG("reviewed")=="" )  echo "selected=selected";?> ><?php _e("Yes");?> </option>
				<option value="0" <?php if(cG("reviewed")=="0")  echo "selected=selected";?> ><?php _e("No");?> </option>
			</select>
		</td></tr>
	
	<?php } ?>
			
	<tr><td>&nbsp;</td><td><input type="submit" class="but" value="<?php _e("Search");?>" /></td></tr>
	</table></form><?php 
}
////////////////////////////////////////////////////////////
?>