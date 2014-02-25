<LINK REL="SHORTCUT ICON"
       HREF="<?php echo SITE_URL; ?>/images/pw-ico.ico">
<script type="text/javascript" src="<?php echo SITE_URL; ?>/themes/<?php echo THEME; ?>/js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL; ?>/themes/<?php echo THEME; ?>/jsclass.js"></script>

<script type="text/javascript" src="<?php echo SITE_URL; ?>/themes/<?php echo THEME; ?>/js/superfish.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL; ?>/themes/<?php echo THEME; ?>/css/superfish.css" />
<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL; ?>/themes/<?php echo THEME; ?>/css/defaultmenu.css" />
<script src="<?php echo SITE_URL; ?>/themes/<?php echo THEME; ?>/js/dropmenu.js" type="text/javascript"></script>
<?php
//getting the title in two parts/colors
$pos=strpos(SITE_NAME," ");
$firstH=substr(SITE_NAME,0,$pos);//first part of the site name in green
$secondH=substr(SITE_NAME,$pos);//second part of the name un blue

?>

<?php
        if(ENABLE_FACEBOOK_LIKE_PAGE)
		include(SITE_ROOT.'/facebook.tpl.php');

?>

<div class="container_12" id="wrap" OnClick="hide('locationMenu')" >
  <div class="grid_12" id="header">
     <div id="logo">
    	<div class="LogoSpace">
		<a href="<?php echo SITE_URL?>?location=<?php echo $location; ?>&home=1"><img src="<?php echo SITE_URL?>/images/postwala-gif.gif" border=0 alt="" class="Logo"/></a>
	</div>
     </div>
     <?php echo sb_location_menu("<div style=\"float: left; \">", "</div>"); ?>
	
	<div style="float:right;">
	<!-- Begin TranslateThis Button -->
	<div id="translate-this" ><a href="http://translateth.is/" class="translate-this-button">Translate</a></div>

	<script type="text/javascript" src="http://www.google.com/jsapi"></script>
	<script type="text/javascript" src="http://x.translateth.is/translate-this.js"></script>
	<script type="text/javascript">
	TranslateThis({
	  cookie : false
	});
	</script>

	<!-- End TranslateThis Button -->
	</div>
	<br/><br/><br/>
     <div style="padding-top:5px; padding-left:55px;  float:left;">
        <?php echo getGoogleAds(7); ?>
     </div>
	 <div style="padding-top:10px; padding-left:10px; float:right;">
                 <div style="float: right; font: 1.1em/1.4 tahoma,verdana,arial,sans-serif;">
                 <?php echo myaccount("","");?>
				 </div>
				<br/><br/><br/>
                
				
				 
				 <div style="float:right;">
                      <?php echo '<a title="'.T_("Publish a new Ad").'" href="'.SITE_URL.newURL().'">';?>
                        <img src="<?php echo SITE_URL; ?>/images/New Publish ad-11.gif" border=0 widht="120" height="40" alt="Publish a new Ad" >
                      </a>
                 </div>
     </div>
 </div>

  <!-- <div class="menupad"><div class="grid_12">
    <?php generateMenuSF($selectedCategory);?>
  </div>
  </div> -->
  <div class="clear"></div>
	<div class="searchpad"><div class="borderedge searchbox"><?php echo advancedSearchForm();?></div></div>
	 <span id='titlespan' class='errorpad errortxt errorwidth'></span>
    <div class="clear"></div>
    <?php if(cG("nofilter") != '1') { ?>
	<?php if(isset($categoryName)&&isset($categoryDescription)){ ?> 
  <div id="content">
     <div class="grid_12">
      <div class=" breadcrumb">          
			
			    <?php //$ParentName = $ocdb->getValue(
				
				$ParentNameRes = mysql_query("select name,friendlyName from ".TABLE_PREFIX."categories where idCategoryParent = 0 ORDER BY name");
				
				$ParentId = $ocdb->getValue("SELECT (select idCategory from ".TABLE_PREFIX."categories where idCategory=C.idCategoryParent) ParentName FROM ".TABLE_PREFIX."categories C where idCategory = '$idCategory'");

				$ParentFriendRes = mysql_query("SELECT C.friendlyName as FN, (select R.name from ".TABLE_PREFIX."categories R where R.idCategory=C.idCategoryParent) ParentName, (select G.friendlyName from ".TABLE_PREFIX."categories G where G.idCategory=C.idCategoryParent) FriendlyName FROM ".TABLE_PREFIX."categories C where idCategory = '$idCategory'");

				$rowParentFriend = mysql_fetch_array($ParentFriendRes);

				$ValParentName	 = $rowParentFriend['ParentName'];
				$ValFriendlyName = $rowParentFriend['FriendlyName'];

				if(is_null($ValParentName)) {
					$ValParentName = $categoryName;
				}
				if(is_null($ValFriendlyName)) {
					$ValFriendlyName = $rowParentFriend['FN'];
				}
				
				if($ParentId == '') {
					$SubId = $ocdb->getValue("select idCategory from ".TABLE_PREFIX."categories where name = '$categoryName'");
					
					$ParentName = $categoryName;
					$SubCategoryName = '';

					
					$ChildNameRes = mysql_query("select name,friendlyName from ".TABLE_PREFIX."categories where idCategoryParent = '$SubId' ORDER BY name");

					$SearchChildNameRes = mysql_query("select idCategory,name,friendlyName from ".TABLE_PREFIX."categories where idCategoryParent = '$SubId' ORDER BY name");
				}
				else {				
					$ChildNameRes = mysql_query("select name,friendlyName from ".TABLE_PREFIX."categories where idCategoryParent = '$ParentId' ORDER BY name");

					$SearchChildNameRes = mysql_query("select idCategory,name,friendlyName from ".TABLE_PREFIX."categories where idCategoryParent = '$ParentId' ORDER BY name");

					$SubCategoryName = $categoryName;

				}
								
				?>							
				<div ><div style="padding-bottom:3px;"><span style="float:left; width:265px;">You're viewing the ads available in <a href='<?php echo SITE_URL; ?>'>Postwala</a>: &nbsp;</span>
				<span style="padding-bottom:9px;">
				<ul id="navbar">
					<li style="padding-left:0em; width:80px;"><a href="<?php echo SITE_URL; ?>/?category=all&location=<?php echo $location; ?>">All Categories</a>
						<ul style="width:10em;">
							<?php while($rowParent = mysql_fetch_array($ParentNameRes)) { ?>
							<li style="background-color:#FFFFFF;">
								<a class ='catlinkstyle' href='<?php echo SITE_URL; ?>/?category=<?php echo $rowParent[friendlyName]; ?>&location=<?php echo $location; ?>'><?php echo $rowParent[name]; ?></a>
							</li>
							<?php } ?>
						</ul>
					</li>
					<?php if($categoryName != 'all') { ?>
					<li style="padding-left:0em; width:18px;"> &nbsp; &raquo; &nbsp;</li>
					<li style="padding-left:0em;"><a href="<?php echo SITE_URL; ?>/?category=<?php echo $ValFriendlyName; ?>&location=<?php echo $location; ?>"><?php echo $ValParentName; ?></a>
						<ul style="width:19em;">
							<?php while($rowChild = mysql_fetch_array($ChildNameRes)) { ?>
							<li style="background-color:#FFFFFF;">
								<a href='<?php echo SITE_URL; ?>/?category=<?php echo $rowChild[friendlyName]; ?>&location=<?php echo $location; ?>'><?php echo $rowChild[name]; ?></a>
							</li>
							<?php } ?>
						</ul>
					</li>
					<?php } ?>
					<!-- ... and so on ... -->
				</ul>
				</span>

				<span style="padding-left:0em; width:18px;"><?php if($ParentId != '') { ?> &nbsp; &raquo; <?php } ?></span>
					&nbsp;<?php echo $SubCategoryName; ?>
						</div><span class="viewpostad"><a title="<?php _e("Post Ad in");?> <?php echo $categoryName;?>" href="<?php echo SITE_URL.newURL();?>"><?php _e("Post Ad in");?> <?php echo ucwords($categoryName);?></a></span>
			<?php 
	            //echo strftime("%A %e %B %Y"); 
				
				?>


			   
			<div style="position:absolute; top:15px; left: 760px; font-size:14px; float:right;"><b><?php _e("Filter");?></b>:
		    <?php generatePostType($currentCategory,$type); ?>
		    </div>
			 </div>
		</div>
    </div>
	<div class="clear"></div>
	<div id="loadcustom" style="padding-left:60px; padding-top:20px;">
	<?php if(!isset($_POST[publishdate]) && cP("publishdate") =='') { ?>
	<?php echo "helloerwer"; ?>
	<form name="customsearch" method="post" action="" onsubmit="return customsearchfunc(this)">
	<?php $query="SELECT idLocation, name, 
                (SELECT name
		FROM classifieds_locations
		WHERE idLocation = C.idLocationParent )
            FROM classifieds_locations C
	    WHERE idLocationParent != 0
	    ORDER BY idLocationParent, idLocation"; ?>
		
		<span style="padding-top:10px;">
			<span class='displayblock'><?php _e("Location"); ?>&nbsp;:&nbsp;<br/><?php	$style = 1; $categoryval = ''; echo sqlOptionGroupSearch($query,"advancedlocation",$location,$categoryval,$style); ?><br/>		
			</span>
		
			<span class='displayblock'><?php _e("Published Date"); ?>&nbsp;:&nbsp;<br/><select name='publishdate' id='publishdate' onBlur="publishSelect(this); validateAdText(this);" class='borderedge widthstyle heightstyle'><option value=''>- Select -</option>
			<option value='ltone' <?php if(cP("publishdate") == 'ltone') { ?> selected <?php } ?>>Less than 1 month</option>
			<option value='ltthree' <?php if(cP("publishdate") == 'ltthree') { ?> selected <?php } ?>>Less than 3 months</option>
			<option value='ltsix' <?php if(cP("publishdate") == 'ltsix') { ?> selected <?php } ?>>Less than 6 months</option>
			<option value='gtsix' <?php if(cP("publishdate") == 'gtsix') { ?> selected <?php } ?>>Greater than 6 months</option>
			</select><br/>		
			</span>
		
		<?php
		if($ParentId != '') { ?> 
			<span id="hidecategory">
				<span class='displayblock'>
					<?php _e("Category"); ?>&nbsp;:&nbsp;<br/>
					<select name='categoryIf' id='categoryIf' onBlur="categorySelect(this); validateAdText(this);" onchange="loadcustomfields(this.value,'<?php echo SITE_URL; ?>/themes/<?php echo THEME; ?>/','<?php echo $SubId; ?>')" class='borderedge widthstyle heightstyle'>
						<option value=''>- Select -</option>
						<?php while($rowChildSearch = mysql_fetch_array($SearchChildNameRes)) { ?>
						<option value='<?php echo $rowChildSearch[idCategory]; ?>'><?php echo $rowChildSearch[name]; ?></option>	
						<?php } ?>
					</select><br/><input type="hidden" id="nocustom" name="nocustom" value="1" /><input type="hidden" id="totalcustomfields" name="totalcustomfields" value="500" />				
				</span>
				<span>
					<button type="submit" id="submit" style="border: 0; background: transparent">
						<img src="<?php echo SITE_URL; ?>/themes/<?php echo THEME; ?>/images/search-button.jpg" />
					</button>
				</span>				
			</span>		
		<?php }
		else { ?>
			<span class='displayblock'>
				<input type="hidden" id="categoryIf" name="categoryIf" value="" />
			</span>
		<?php } ?>		
		<span id="hidecatspan">
		<?php if($ParentId == '') { ?> 
			</span>
		<?php } ?>
		</span>



		<?php $queryMap="SELECT idField,FieldName,FieldActualName,FieldValues FROM ".TABLE_PREFIX."custom_fields				WHERE idField in	(SELECT idField FROM ".TABLE_PREFIX."categories_field_mapping WHERE							idCategory='$idCategory' and Active='Y') and Active='Y'";
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
						if($ParentId=='' && $catPresent !=5) { $catPresent = 5; echo "<br/>"; } ?>
						<?php if($i == 1 || $i == 4) { ?>
								<span>
						<?php } ?>
						<span class='displayblock <?php if($i == 1 || $i == 4) {  ?>padtop<?php } ?>' >
						<?php echo ucwords(strtolower($FieldActualName)); ?>&nbsp;:&nbsp;<br/>
						<input type="hidden" id="idField<?php echo $i; ?>" name="idField<?php echo $i; ?>" value="1" />
							
							<select name='customfield<?php echo $i; ?>' id='customfield<?php echo $i; ?>' class='borderedge widthstyle heightstyle'>		
								<option value=''>- Select -</option>
								<?php $k = 0; foreach($FieldValues as $AdminFieldValue) { ?>
								<option value='<?php echo friendly_url($AdminFieldValue); ?>' 
								

								><?php echo $AdminFieldValue; ?></option>
								<?php $k++; } $i++; ?>
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
		} ?><br/><br/>
<span id='commonsearchspan'></span>
</form>
<?php } ?>
</div>
	<?php 
		} //this is to display when category and category description comes from the URL 		
	 } //this is for no filter  
	?>
    <div class="clear"></div>
	       <div class="grid_8" id="content_main">