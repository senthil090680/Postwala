<?php
require_once('../includes/header.php');


if (LOGON_TO_POST){
    $account = Account::createBySession();
    if ($account->exists){
        $name = $account->name;
        $email = $account->email;
    } 
    else redirect(accountLoginURL());
}

if (!isInSpamList($client_ip)){//no spammer
	require_once('../includes/classes/resize.php');
	if ($_POST){
	     $currentCategory=cP("category");
	     redirect(SITE_URL.newURL() . "&type=".cP("type"));	 
	}//if post
	
?>
<h3><?php _e("Select a Category to Publish a new Ad");?> </h3>
<form action="" method="post" onsubmit="return checkForm(this);" enctype="multipart/form-data">
		
		<?php _e("Category");?>:<br />
		<?php 
		$selectedCategory=cG("category");
		if (PARENT_POSTS){
			$query="SELECT friendlyName,name,(select name from ".TABLE_PREFIX."categories where idCategory=C.idCategoryParent) FROM ".TABLE_PREFIX."categories C order by idCategoryParent, `order`";
			sqlOptionGroup($query,"category",$selectedCategory);
		}
		else{
			$query="SELECT friendlyName,name,(select name from ".TABLE_PREFIX."categories where idCategory=C.idCategoryParent) 
				FROM ".TABLE_PREFIX."categories C where C.idCategoryParent!=0 order by idCategoryParent, `order`";
			sqlOptionGroup($query,"category",$selectedCategory);
		}
		?>
		<br />
	        <?php _e("Type");?>:<br />
		<select id="type" name="type" class='borderedge'>
			<option value="<?php echo TYPE_OFFER;?>"><?php _e("offer");?></option>
			<option value="<?php echo TYPE_NEED;?>"><?php _e("need");?></option>
		</select>
		<br />
	
	<input type="submit" id="submit" class="but" value="<?php _e("Go >>");?>" />
</form>
<?php
}
else {//is spammer
	alert(T_("NO Spam!"));
	jsRedirect(SITE_URL);
}

require_once('../includes/footer.php');
?>