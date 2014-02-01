  </div>
  
 
    
    
    <div class="clear"></div>

<div style="padding-bottom:8px;">
		<?php $query="SELECT idLocation, name, 
                (SELECT name
		FROM classifieds_locations
		WHERE idLocation = C.idLocationParent )
            FROM classifieds_locations C
	    WHERE idLocationParent != 0
	    ORDER BY name";

	$ocdb=phpMyDB::GetInstance();
	$result =$ocdb->query($query);//1 value needs to be the ID, second the Name, 3rd is the group
	//echo $sql; ?>
	<?php $i = 1; while ($row=mysql_fetch_assoc($result)){
		$first=mysql_field_name($result, 0);
		$second=mysql_field_name($result, 1);
		$third= mysql_field_name($result,2); ?>
		<span style="display:inline-block; <?php if($i==3) { ?>padding-bottom:8px; <?php } ?> <?php if($i==1) { ?>float:left; padding-left:8px; <?php } ?>padding-right:50px; width:100px; "><a href="<?php echo SITE_URL; ?>?location=<?php echo $row[$first]; ?>" ><?php echo $row[$second]; ?></a></span>


	<?php if($i == 6) { echo "<br>"; $i=1; } else { $i++; } 
	} ?>
	</div> 
	 <div class="clear"></div>

<div class="grid_12" id="footer">
    <ul class="pages">
	        <li><a href="<?php echo SITE_URL;?>/content/site-map.php"><?php _e("Sitemap");?></a></li>
		<li><a href="<?php echo SITE_URL;?>/content/privacy.php"><?php _e("Privacy Policy");?></a></li>
		<li><a href="<?php echo SITE_URL;?>/content/listing-policy.php"><?php _e("Listing Policy");?></a></li>
		<li><a href="<?php echo SITE_URL;?>/content/terms.php"><?php _e("Terms of Use");?></a></li>
	    <li><a href="<?php echo SITE_URL."/".contactURL();?>"><?php _e("Contact");?></a></li>
	</ul>
    <p>
    <?php echo date("Y"). "&nbsp;Copyright". COPY_RIGHT; ?> 
    </p>
  </div>
 

	
<!--<div class="HeaderAdSpace" style = "margin-left: auto; margin-right: auto; width: 728px;" >
		<object type="application/x-shockwave-flash" 
		   data="<?php echo SITE_URL; ?>/images/flash/pmbanner.swf" width="728" height="90" align="middle" 
		          wmode="transparent"><param name="movie" value="<?php echo SITE_URL; ?>/images/flash/pmbanner.swf">
		          <param name="wmode" value="transparent"></object>			
</div> -->

  </div>
</div>