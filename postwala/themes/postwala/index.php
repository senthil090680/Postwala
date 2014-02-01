<?php
$featuredAdAvailable = 0;
$featuredAdSeperator = 0;
/*if ($advs){//advanced search form
	echo '<div>';
	    advancedSearchForm();
	echo '</div>';
}*/
if ((isset($idItem)||isset($currentCategory)||isset($type)||cG("s")!=""||isset($location) || isset($currentTitle)) && cG("home")!=1) {
?>
<div id="listings" style="padding-top:20px; width: 940px;">
	<div >
<?php
	if (isset($currentTitle) && ($currentTitle != 'all')) echo "<div style='float:left;'><b style='font-size:18px;'>".ucwords($currentTitle)."</b></div>";
	if (isset($location)) $locationtitle = " - ".getLocationName($location);
	if (isset($categoryName) && ($categoryName != 'all')) echo "<div style='float:left;'><b style='font-size:18px;'>".$categoryName.$locationtitle."</b></div>";
	else if (isset($categoryName) && ($categoryName == 'all')) echo "<div style='float:left;'><b style='font-size:18px;'>All Ads in Postwala".$locationtitle."</b></div>"; ?>
	

	<?php if(stristr($ValParentName,"home")) {
			$anchorValue	=	'Home Appliances';
			$anchorUrl		=	PRICEMASK_HIT_HOME_URL;
		}
		else { $anchorValue	=	'Mobiles & Electronics';
			   $anchorUrl	=	PRICEMASK_HIT_MOBILE_URL;} ?>

	<?php if(ENABLE_PRICEMASK_LINK) { ?>
	<div style="float:right;"><span style="display:inline;"><img class="handimage" src="<?php echo SITE_URL; ?>/themes/<?php echo THEME; ?>/images/handpointer.gif" height="20px" /></span><span class="handpointer"><a target="_blank" href="<?php echo $anchorUrl; ?>"><b style="color:white;"><blink>Check the latest price of <?php echo $anchorValue; ?> here</blink></b></a></span>
	</div>	
	<?php } ?>
	
	</div>
	
	<div class="clear"></div>

	<?php if ($resultSearch){ ?>
	<div class="googleads post1">
              <div class="padbot10"><?php echo getGoogleAds(1); ?></div>
  	</div>
		<?php foreach ( $resultSearch as $row ){
		        $adBatch = $row['ImageName'];
				$Location_Name = $row['Location_Name'];
			$idPost=$row['idPost'];
			$postType=$row['type'];
			$postTypeName=getTypeName($postType);
			$postTitle=$row['title'];
			$postPrice=$row['price'];
			$postDesc = mb_substr(strip_tags(html_entity_decode($row['description'], ENT_QUOTES, CHARSET)), 0, 200, CHARSET)."...";
			$category=$row['category'];//real category name
			$fcategory=$row['fcategory'];//frienfly name category
			$idCategoryParent=$row['idCategoryParent'];
			$fCategoryParent=$row['parent'];
			$postImage=$row['image'];
			$postPassword=$row['password'];
			$insertDate=setDate($row['insertDate']);
			$postUrl=itemURL($idPost,$fcategory,$postTypeName,$postTitle,$fCategoryParent); 			
			if ($row["hasImages"]==1){
				$postImage=getPostImages($idPost,$insertDate,true,true);
			}
			else $postImage=getPostImages(true,true,true,true);//there's no image
			?>

			<?  if($adBatch =='' && $featuredAdAvailable == 1 && $featuredAdSeperator == 1 ) {   $featuredAdSeperator = 0; ?>
			        <div class="adsense-bottom-bar">&nbsp;</div>
			<? } ?>
			<?  if($adBatch !=='' ) { $featuredAdAvailable =1;  $featuredAdSeperator = 1; ?>
			<div id="tag" class="right-tag"><img src="<?php
			                                                echo SITE_URL;
			                                                echo  "/images/premium-ads/". $adBatch ;

								  ?>" width="70" height="70"></div>

			<? }   ?>

			<div class="post">
			    <?php if (MAX_IMG_NUM>0){?>
						<img  title="<?php echo FREE_CLASSIFIEDS_INDIA . $Location_Name." ". $postTitle." ".$postTypeName." ".$category;?>"  alt="<?php echo FREE_CLASSIFIEDS_INDIA . $postTitle." ".$postTypeName." ".$category;?>" width="100px" height="74px" src="<?php echo $postImage;?>" class="post-img"
				                <?php if($adBatch !=='' ) {  echo "style=\"margin: 0px -30px 0px 0px;\" ";    }  ?>
						/>
				<?php }?>
				
				

			    <table width="70%">
					<tr class="valigntd">
						<td class="valigntd">
							<table><tr class="valigntd"><td class="valigntd">
							<a title="<?php echo FREE_CLASSIFIEDS_INDIA . $Location_Name." ". $postTitle." ".$postTypeName." ".$category;?>" href="<?php echo SITE_URL.$postUrl;?>"  rel="bookmark" >
									 <h2 style="width:500px; padding-right:170px;"><span class="headlocation"><?php echo $Location_Name;?>:</span>
							 <?php echo $postTitle;?> </h2>
							</a></td></tr></table>
						</td>
						<td><div id="<?php echo $idPost; ?><?php echo "_1"; ?>"><table>
								<tr>
									<td>
										<a href="javascript:divopen('<?php echo $idPost; ?>')"><img src="<?php echo SITE_URL; ?>/themes/<?php echo THEME; ?>/images/reply-button.jpg" /></a><input type="hidden" value="" name="hidereply" id="hidereply" />
									</td>
								</tr>
							</table>
							</div>
						</td>
					</tr>
				</table>
					
				
			     <div class="post-detail">
	                         <p><?php if ($postPrice!=0) echo '<span class="post-price">'.getPrice($postPrice).'</span> — ';?><span class="post-cat"><?php echo '<a href="'.SITE_URL.catURL($fcategory,$fCategoryParent).'" title="'.$category.' '.$fCategoryParent.'">'.$category.'</a>';?></span> — <span class="post-date"><?php echo $insertDate;?></span></p>
	                     </div>

				
	          <p class="post-desc"><?php echo $postDesc;?></p>				
			 <div id="<?php echo $idPost; ?>" style="display:none;">
					
					<div class="popdivstyle">
					<form name="reply" action="" id="reply" onsubmit="">
						<div class="replytable">
							<div class="namerow">
								<span class="replyname">Name&nbsp;*</span>
								<span class="replynacol">:</span>
								<span class="replynatxt"><input name="<?php echo $idPost; ?>replyname" id="<?php echo $idPost; ?>replyname" maxlength="50" style="height:10px;" type="text"/></span>
								<span class="replymail">Email&nbsp;*</span>
								<span class="replymacol">:</span>
								<span class="replymatxt"><input name="<?php echo $idPost; ?>replyemail" id="<?php echo $idPost; ?>replyemail" style="height:10px;" type="text"/></span>
								<span class="replyclose"><a href="javascript:divclose('<?php echo $idPost; ?>')"><img src="<?php echo SITE_URL; ?>/themes/<?php echo THEME; ?>/images/cross-button.jpg" /></a></span>
							</div>
							<div class="msgrow">
								<span class="replymsg">Message&nbsp;*</span>
								<span class="replymecol">:</span>
								<span class="replymetxt"><input maxlength="250" name="<?php echo $idPost; ?>replymsg" id="<?php echo $idPost; ?>replymsg" style="width:600px; height:10px;" type="text"/></span>								
							</div>
							<div class="mobrow">								
								<span class="replymob">Mobile</span>
								<span class="replymocol">:</span>
								<span class="replymotxt"><input name="<?php echo $idPost; ?>replymob" id="<?php echo $idPost; ?>replymob" style="width:150px; height:10px;" maxlength="10" type="text"/></span>
								<span class="replysub"><a href="#" onclick="javascript:return replysubmit('<?php echo $idPost; ?>','<?php echo SITE_URL; ?>/themes/<?php echo THEME; ?>/')"><img src="<?php echo SITE_URL; ?>/themes/<?php echo THEME; ?>/images/submit-button2.jpg" /></a>
								</span>
							</div>										
						</div>
					</form>
					</div>		
				</div>
			<div id="<?php echo $idPost; ?>errormsg" style="display:none; color:red;"></div>
			  <?php if(isset($_SESSION['admin'])){?>
					<br />
					<a href="<?php echo SITE_URL;?>/manage/?post=<?php echo $idPost;?>&amp;pwd=<?php echo $postPassword;?>&amp;action=edit">
							<?php _e("Edit");?></a><?php echo SEPARATOR;?>
					<a onClick="return confirm('<?php _e("Deactivate");?>?');"
						href="<?php echo SITE_URL;?>/manage/?post=<?php echo $idPost;?>&amp;pwd=<?php echo $postPassword;?>&amp;action=deactivate">
							<?php _e("Deactivate");?></a><?php echo SEPARATOR;?>
					<a onClick="return confirm('<?php _e("Spam");?>?');"
						href="<?php echo SITE_URL;?>/manage/?post=<?php echo $idPost;?>&amp;pwd=<?php echo $postPassword;?>&amp;action=spam">
							<?php _e("Spam");?></a><?php echo SEPARATOR;?>
					<a onClick="return confirm('<?php _e("Delete");?>?');"
						href="<?php echo SITE_URL;?>/manage/?post=<?php echo $idPost;?>&amp;pwd=<?php echo $postPassword;?>&amp;action=delete">
							<?php _e("Delete");?></a>
				<?php }?>
	          <div class="clear"></div>
	        </div>
			
			<?php 		} ?>
		<div class="googleads">
              <div class="padbot10"><?php echo getGoogleAds(4); ?></div>
			  <div class="padbot10"><?php echo getGoogleAds(5); ?></div>
			  <div class="padbot10"><?php echo getGoogleAds(6); ?></div>
  		</div>
	<?php }//end if check there's results
else
{

   echo "<p>No Ads available in this section</p>";
   echo sb_new ("<p>","</p>");

}
?>
</div>


	<div class="pagination">
	 <div class="wp-pagenavi">
	<?php //page numbers echo $_SERVER["REQUEST_URI"];
		if ($total_pages>1){

			//if is a search
			if (strlen(cG("s"))>=MIN_SEARCH_CHAR) $search="&s=".cG("s");

			$pag_title=$html_title." page ";

			//getting the url
			if(strlen(cG("s"))>=MIN_SEARCH_CHAR){//home with search
				$pag_url='?s='.cG("s").'&category='.$currentCategory.'&page=';
			}
			elseif ($advs){//advanced search
				$pag_url="?category=$currentCategory&type=".cG("type")."&title=".cG("title")."&desc=".cG("desc")."&price=".cG("price")."&place=".cG("place")."&sort=".cG("sort")."&page=";
			}
			elseif (isset($type)){ //only set type in the home
				$pag_url=typeURL($type,$currentCategory).'&page=';
			}
			elseif (isset($currentCategory)){//category
				$pag_url=catURL($currentCategory,$selectedCategory,$location);//only category
				if(!FRIENDLY_URL) $pag_url.='&page=';
			}
			elseif (isset($location)){//category
				$pag_url=catURL($currentCategory,$selectedCategory,$location);
				if(!FRIENDLY_URL) $pag_url.='&page=';
			}
			else {
			    $pag_url="/";//home
			    if(!FRIENDLY_URL) $pag_url.='?page=';
			}
			//////////////////////////////////

			if ($page>1){
				echo "<a title='$pag_title' href='".SITE_URL.$pag_url."1'>&lt;&lt;</a>";//First
				echo "<a title='".T_("Previous")." $pag_title".($page-1)."' href='".SITE_URL.$pag_url.($page-1)."'>&lt;</a>";//previous
			}
			//pages loop
			for ($i = $page; $i <= $total_pages && $i<=($page+DISPLAY_PAGES); $i++) {//for ($i = 1; $i <= $total_pages; $i++) {
		        if ($i == $page) echo "<span class='current'>$i</span>";//not printing link current page
		        else echo "<a class='page' title='$pag_title$i' href='".SITE_URL."$pag_url$i'>$i</a>";//print the link
		    }

		    if ($page<$total_pages){
		    	echo "<a href='".SITE_URL.$pag_url.($page+1)."' title='".T_("Next")." $pag_title".($page+1)."' >&gt;</a>";//next
		    	echo  "<a title='$pag_title$total_pages' href='".SITE_URL."$pag_url$total_pages'>&gt;&gt;</a>";//End
		    }
		}
	?>
	</div>
	</div>

<?php
}//if not home

else {//home page carousel and categories ?>
    <div>
		<div style="float:left;">
			  <div id="frontpage_cats">
				<?php echo getCategoriesList();?>
				<div class="clear"></div>
			  </div>
		</div>
		<div class="sidead">
			<div class="grid_4" id="sidebar">
				  <ul id="sidebar_widgeted">
						<?php getSideBar("<li class='widget widget_recent_entries'><div class='whitebox'>","</div></li>");?>
				  </ul>
			</div>
		</div>
	</div>
	 <div class="clear"></div>

     <?php
     }
?>