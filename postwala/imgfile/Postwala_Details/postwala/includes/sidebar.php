<?php
////////////////////////////////////////////////////////////
//Sidebar generator
////////////////////////////////////////////////////////////

function getSideBar($beg,$end){//generates the sidebar reading from the config.php
	$widgets=explode(",",SIDEBAR);
	foreach ($widgets as $widget){
        $widget="sb_".$widget;
	    echo $widget($beg,$end);
	}
}

//////////////////////////////////////////////////////
//Side bar functions. ALL OF THEM MUST START ON sb_FUNCTION_NAME, to add them in the config file just write FUNCTION_NAME,
/////////////////////////////////////////////////////

function sb_new($beg,$end){//add new
	return $beg.'<b><a title="'.T_("Publish a new Ad").'" href="'.SITE_URL.newURL().'">'.T_("Publish a new Ad").'</a></b>'.$end;
}
/////////////////////////////////////////////////////

function sb_ad300x250($beg,$end){

return getGoogleAds(8); 

}
////////////////////////////////////////////////////////////
function sb_search($beg,$end){//search form
	global $categoryName,$idCategory,$currentCategory,$type,$location;
		if (cG("s")=="") $ws=T_("Search")."...";
		else $ws=cG("s");
		$search= "<form name=\"word_search\">
			<p><input name=\"s\" style=\"color: #6633FF; width:180px; border: thin solid #369AE8; -moz-border-radius:10px;

-webkit-border-radius:10px; \"  id=\"s\" maxlength=\"25\" size=\"15\" title=\"".T_("Search")."\"
				onblur=\"this.value=(this.value=='') ? '$ws' : this.value;\" 
				onfocus=\"this.value=(this.value=='$ws') ? '' : this.value;\" 
				value=\"$ws\" type=\"text\" />
				<img src=\"".SITE_URL."/images/search-btn.jpg\" border=\"1\" class=\"searchBtn\" width=\"40\" height=\"30\" onclick=\"return search_func('".SITE_URL."')\" /><br/>";
		
		if(isset($categoryName)) $search.='<input type="hidden" name="category" value="'.$currentCategory.'" />';
        if(isset($location)) $search.='<input type="hidden" name="location" value="'.getLocationFriendlyName($location).'" />';
		
		$search.=advancedSearchURL().'</p>';
		
		$search.='</form>';
		
		      		
	return $beg.$search.$end;
}
////////////////////////////////////////////////////////////
function sb_locations($beg,$end){//locations list (state or city)
    if (LOCATION){
        global $location,$currentCategory,$selectedCategory;
           
        if (isset($location)) {
            $currentlocation = getLocationName($location);
            $locationparent = getLocationParent($location);
        } else $locationparent = 0;
        
        $locationcontent = "<h4>".T_("Location")."</h4>";
        
        if ($locationparent != 0) $locationcontent .= "<h4><a href=\"".SITE_URL.catURL($currentCategory,$selectedCategory,getLocationFriendlyName($locationparent))."\">".getLocationName($locationparent)."</a> / $currentlocation</h4>";
        elseif (isset($location)) {
          $locationroot = LOCATION_ROOT;
          if ($locationroot == "") $locationroot = T_("Home");
          $locationcontent .= "<h4><a href=\"".SITE_URL.catURL($currentCategory,$selectedCategory,$_unused_)."\">$locationroot</a> / $currentlocation</h4>";
        }
        
        if (!isset($location) || $location=='' ) $location = 0;
        $ocdb=phpMyDB::GetInstance();
        $query = "select idLocation, name, friendlyName from ".TABLE_PREFIX."locations where idLocationParent=$location order by name";
        $result=$ocdb->getRows($query);
        
        $i = 0;
    	$q = count($result);
    	$z = round($q/2);
    
        foreach ($result as $location_row ) {
            if ($i==0 or $i==$z) $locationcontent .= "<div class=\"columns\"><ul>";
            
            $locationcontent .= "<li><a href=\"".SITE_URL.catURL($currentCategory,$selectedCategory,$location_row["friendlyName"])."\">".$location_row["name"]."</a></li>";
            
            if ($i==($z-1) or $i==($q-1)) $locationcontent .= "</ul></div>";
    
            $i++;
        }
        
        $locationcontent .= "<div class=\"clear\" />";
        
        return $beg.$locationcontent.$end;
    }
}
////////////////////////////////////////////////////////////
function sb_location_menu($beg,$end){//locations list (state or city)
        global $location,$currentCategory,$selectedCategory;
           
        if (isset($location)) 
	{
            $currentlocation = getLocationName($location);
            $locationparent = getLocationParent($location);
        } 
	else $locationparent = 0;
        
        $locationcontent = "";
        
        if ($locationparent != 0) 
	{
		$locationcontent .= "<div class=\"select_location_sub\"><a href=\"".SITE_URL.catURL($currentCategory,$selectedCategory,getLocationFriendlyName($locationparent))."\">".getLocationName($locationparent)."</a> &raquo; $currentlocation</div>";
        }
	elseif (isset($location)) 
	{
          $locationroot = LOCATION_ROOT;
          if ($locationroot == "") $locationroot = T_("Home");
          $locationcontent .= "<div class=\"select_location_sub\"><a href=\"".SITE_URL.catURL($currentCategory,$selectedCategory,$_unused_)."\">$locationroot</a> &raquo; $currentlocation</div>";
        }
	
		
        
	
        $ocdb=phpMyDB::GetInstance();
	
	$query = "select t1.idLocation, t1.name as state, t1.friendlyName as stateFriendlyName, t1.idLocationParent,
		t2.idLocation, t2.name as city, t2.friendlyName as cityFriendlyName, t2.idLocationParent  

		FROM ".TABLE_PREFIX."locations t1  LEFT OUTER JOIN  ".TABLE_PREFIX."locations t2

		ON  t1.idLocation = t2.idLocationParent
                where t1.idLocationParent=0

		order by t1.name, t2.name " ;


        $result=$ocdb->getRows($query);
        
        $currentState = "";
	$prevState = "";
	
        $currentCity = "";
	$prevCity = "";
	
	$locationcontent .= " <div style=\"padding-left: 20px;\">" ;
	$locationcontent .= "\n<ul class=\"sf-menu sf-horizontal sf-js-enabled sf-shadow\" id=\"sf-top-navigation2\"> \n" ; 
        $locationcontent .= "    <li> <a href=\"#\"  style=\"background-color: #FF6600;\" >" ; 
	if($locationparent != 0  || isset($location) )
		$locationcontent .= "Change Location" ;
	else  	
	        $locationcontent .= "Select Location" ;

	$locationcontent .=  "</a><ul>";            
		
		if(ENABLE_ALLINDIA) {
			$locationcontent .= "<li><a href=\"".SITE_URL."\">All India</a></li>";
		}
        foreach ($result as $location_row ) {
            $currentState = $location_row["state"] ;
            $currentCity = $location_row["city"];
            
	    if($currentState != $prevState)
	    {
	        if($prevState != "")
		{
		    if ($prevCity != "")
     		         $locationcontent .= "      </ul> \n    "  ;
		    
		    $locationcontent .= "      </li> \n"  ;
		}    
		    
		$locationcontent .= "    <li><a href=\"".SITE_URL.catURL($currentCategory,$selectedCategory,$location_row["stateFriendlyName"])."\">".$location_row["state"];            
                
		if($currentCity != "")
		{
		    $locationcontent .= "<span class=\"sf-sub-indicator\"> &raquo; </span>" ;
		    $locationcontent .= "</a> \n  <ul> \n"  ;
		}
		else
		    $locationcontent .= "</a> \n "  ;
		
		        
		$prevState = $currentState;
	    }
	    
	    if($currentCity  != "")
                $locationcontent .= "         <li><a href=\"".SITE_URL.catURL($currentCategory,$selectedCategory,$location_row["cityFriendlyName"])."\">".$location_row["city"]."</a></li> \n";            
        
	    $prevCity = $currentCity;
	} 
        $locationcontent .= "  </ul>\n</li> </ul>\n</li>\n</ul> \n  </div>" ;
	
        return $beg.$locationcontent.$end;    
}
////////////////////////////////////////////////////////////
function sb_infolinks($beg,$end){//site stats info and tools linsk rss map..
	global $idCategory,$currentCategory,$type,$location;
    $info.= '<b>'.T_("Total Ads").':</b> '.totalAds($idCategory).SEPARATOR
		.' <b>'.T_("Views").':</b> '.totalViews($idCategory).SEPARATOR
		.' <b><a href="'.rssURL().'?category='.$currentCategory.'&amp;type='.$type.'&amp;location='.$location.'">RSS</a></b>';
		 if (MAP_KEY!="") $info.=SEPARATOR.'<b><a href="'.SITE_URL.'/'.mapURL().'?category='.$currentCategory.'&amp;type='.$type.'">'.T_("Map").'</a></b>';
   return $beg.$info.$end;
}
////////////////////////////////////////////////////////////
function sb_donate($beg,$end){//donation
	return $beg.'<h4>'.T_("Recommended").'</h4><br />Please donate to help developing this software. No matter how much, even small amounts are very welcome.
<a href="http://j.mp/ocdonate" target="_blank">
<img src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" border="0" alt="" />
</a> Thanks. <br /><br /> To erase this please go to: Admin->Settings->Look and Feel->Widget Sidebar->donate.'.$end;
}
////////////////////////////////////////////////////////////
function sb_advertisement($beg,$end){//advertisement
	return $beg.ADVERT_SIDEBAR.$end;
}
////////////////////////////////////////////////////////////
/*function sb_popular($beg,$end){//popular items
	if (COUNT_POSTS){
		global $categoryName,$idCategory;
		$ret="<br>".$beg."<h4>".T_("Latest Ads")." $categoryName:</h4>";
		$ret.=generatePopularItems(7,5,$idCategory);
		//$ret.="*".T_("Last Week").$end;
		$ret.=$end;
		return $ret;
	}
}*/
////////////////////////////////////////////////////////////
function sb_popular($beg,$end){//latest items
	if (COUNT_POSTS){
		//global $location;
		
		/*if($location != '') {
			if(is_numeric($location)) {
				$locationName		=	getLocationName($location);
				$locationId			=	$location;
				$locationDisplay	=	"in ".$locationName;
			}
			else if(!is_numeric($location)) {
				$locationName		=	$location;
				$locationId			=	getLocationNum($location);
				$locationDisplay	=	"in ".$locationName;
			}
		}
		else $locationDisplay = '';*/

		$ret="<br><br>".$beg."<h4>".T_("Latest Ads")." $locationDisplay:</h4>";
		//$ret.=generateLatestItems($locationId); // use this if location is chosen
		$ret.=generateLatestItems($locationId,5); 
		//$ret.="*".T_("Last Week").$end;
		$ret.=$end;
		return $ret;
	}
}
////////////////////////////////////////////////////////////
function sb_item_tools($beg,$end){//utils for admin
	global $idItem,$itemPassword;
	if(isset($idItem)&&isset($_SESSION['admin'])){
		echo $beg;?>
		<h4><?php _e("Classifieds tools");?>:</h4>
		<ul>
			<li><a href="<?php echo itemManageURL();?>?post=<?php echo $idItem;?>&amp;pwd=<?php echo $itemPassword;?>&amp;action=edit">
				<?php _e("Edit");?></a>
			</li>
			<li><a onClick="return confirm('<?php _e("Deactivate");?>?');" 
				href="<?php echo itemManageURL();?>?post=<?php echo $idItem;?>&amp;pwd=<?php echo $itemPassword;?>&amp;action=deactivate">
				<?php _e("Deactivate");?></a>
			</li>
			<li>	<a onClick="return confirm('<?php _e("Spam");?>?');"
					href="<?php echo itemManageURL();?>?post=<?php echo $idItem;?>&amp;pwd=<?php echo $itemPassword;?>&amp;action=spam">
						<?php _e("Spam");?></a>
			</li>
			<li><a onClick="return confirm('<?php _e("Delete");?>?');"
				href="<?php echo itemManageURL();?>?post=<?php echo $idItem;?>&amp;pwd=<?php echo $itemPassword;?>&amp;action=delete">
				<?php _e("Delete");?></a>
			</li>
			<li><a href="<?php echo SITE_URL;?>/admin/logout.php"><?php _e("Logout");?></a>
			</li>
		</ul>
	<?php 
		echo $end;
	}
}
////////////////////////////////////////////////////////////
function sb_links($beg,$end){//links sitemap
		echo $beg;
		
	?>
		<h4><?php _e("Menu");?>:</h4>
		<ul>
		    <?php if(FRIENDLY_URL) {?>
			    <li><a href="<?php echo SITE_URL."/".u(T_("Advanced Search"));?>.htm"><?php _e("Advanced Search");?></a></li>
			    <li><a href="<?php echo SITE_URL."/".u(T_("Sitemap"));?>.htm"><?php _e("Sitemap");?></a></li>   
			    <li><a href="<?php echo SITE_URL."/".u(T_("Privacy Policy"));?>.htm"><?php _e("Privacy Policy");?></a></li>
			    <li><a href="http://www.pricemask.com/termsofuse.htm"><?php _e("Terms of Use");?></a></li>
			    
		    <?php }else { ?>
		        <li><a href="<?php echo SITE_URL;?>/content/search.php"><?php _e("Advanced Search");?></a></li>
		        <li><a href="<?php echo SITE_URL;?>/content/site-map.php"><?php _e("Sitemap");?></a></li>
			    <li><a href="<?php echo SITE_URL;?>/content/privacy.php"><?php _e("Privacy Policy");?></a></li>
		    <?php } ?>
		    <li><a href="<?php echo SITE_URL."/".contactURL();?>"><?php _e("Contact");?></a></li>
		</ul>
	<?php 
	echo $end;
}

////////////////////////////////////////////////////////////
function sb_comments($beg,$end){//disqus comments
	if (DISQUS!=""){
		return $beg .'<script type="text/javascript" src="http://disqus.com/forums/'.DISQUS.'/combination_widget.js?num_items=5&hide_mods=0&color=blue&default_tab=recent&excerpt_length=200"></script>'.$end;
	}
}

////////////////////////////////////////////////////////////
function sb_translator($beg,$end){//google translate
    $lang = LANGUAGE;
	return $beg.'<div id="google_translate_element"></div><script type="text/javascript">
	function googleTranslateElementInit() {
	new google.translate.TranslateElement({pageLanguage: \''.$lang.'\'}, \'google_translate_element\');
	}</script><script src="http://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit" type="text/javascript"></script>'.$end;
}

///////////////////////////////////////////////////////////
function sb_theme($beg,$end){//theme selector
	if (THEME_SELECTOR){
	    echo $beg;?>
	    <b onclick="openClose('theme_sel');" style="cursor:pointer;"><?php echo THEME;?></b>
	    <div id="theme_sel" style="display:none;"><ul>
		<?php
		$themes = scandir(SITE_ROOT."/themes");
		foreach ($themes as $theme) {
			if($theme!="" && $theme!=THEME && $theme!="." && $theme!=".." && $theme!="wordcloud.css"){
				echo '<li><a href="'.SITE_URL.'/?theme='.$theme.'">'.$theme.'</a></li>';
			}
		}
	    echo "</ul></div>" . $end;
	}
}
////////////////////////////////////////////////////////////
function sb_categories_cloud($beg,$end){// popular categories
	global $categoryName;
	if(!isset($categoryName)){ 
		echo $beg."<h4>".T_("Categories")."</h4><br />";
		generateTagPopularCategories();
		echo $end;
	}
}
////////////////////////////////////////////////////////////
function sb_account($beg,$end){
	if (LOGON_TO_POST){
		$account = Account::createBySession();
		if ($account->exists){
			$ret='<h4>'.T_("Welcome").' '.$account->name.'</h4>';
		    $ret.= '<ul><li><a href="'.accountURL().'">'.T_("My Account").'</a></li>';
		    $ret.= '<li><a href="'.accountSettingsURL().'">'.T_("Settings").'</a></li>';
		    $ret.= '<li><a href="'.accountLogoutURL().'">'.T_("Logout").'</a></li></ul>';
		   
		}
		else{
		    $ret='<h4>'.T_("Account").'</h4>'; 
		    $ret.= '<a href="'.accountLoginURL().'">'.T_("Login").'</a>&nbsp;|&nbsp;<a href="'.accountRegisterURL().'">'.T_("Register").'</a> ';
		}		
		return $beg.$ret.$end;
	}
	
}

////////////////////////////////////////////////////////////
function accountLinks($beg,$end){
	if (LOGON_TO_POST){
		$account = Account::createBySession();
		if ($account->exists){
			$ret='<h4>'.T_("Welcome").' '.$account->name.'</h4>';
		    $ret.= '<ul><li><a href="'.accountURL().'">'.T_("My Account").'</a></li>';
		    $ret.= '<li><a href="'.accountSettingsURL().'">'.T_("Settings").'</a></li>';
		    $ret.= '<li><a href="'.accountLogoutURL().'">'.T_("Logout").'</a></li></ul>';
		   
		}
		else{
		    $ret='<h4>'.T_("Account").'</h4>';
		    $ret.= '<a href="'.accountLoginURL().'">'.T_("Login").'</a>|<a href="'.accountRegisterURL().'">'.T_("Register").'</a>';
		}		
		return $beg.$ret.$end;
	}
	
}
////////////////////////////////////////////////////////////
function myaccount($beg,$end){
	if (LOGON_TO_POST){
		$account = Account::createBySession();
		if ($account->exists){
			$ret=T_("Hello").' '.$account->name.' | ';
		    $ret.= '<a href="'.accountURL().'">'.T_("My Account").'</a> | ';
		    $ret.= '<a href="'.accountSettingsURL().'">'.T_("Settings").'</a> | ';
		    $ret.= '<a href="'.accountLogoutURL().'">'.T_("Logout").'</a>';
		   

		}
		else{
		    $ret= '<a href="'.accountLoginURL()."?nofilter=1".'">'.T_("Login").'</a> | <a href="'.accountRegisterURL()."?nofilter=1".'">'.T_("Register").'</a>';
		}		
		return $beg.$ret.$end;
	}
	
}
////////////////////////////////////////////////////////////
function sb_rss($beg,$end){
	$ret = '<h4>'.RSS_SIDEBAR_NAME.'</h4>';
	$ret.= '<ul>'.rssReader(RSS_SIDEBAR_URL,RSS_SIDEBAR_COUNT,CACHE_ACTIVE,'<li>','</li>').'</ul>';
	return $beg.$ret.$end;
}


?>