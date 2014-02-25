<?php
function generateMenuSF($selectedCategory)
{
        //if (isset($category)) 
        //{
	//    $currentcategory = getcategoryName($category);
	//    $categoryparent = getcategoryParent($category);
        //} 
        //else $categoryparent = 0;
        
        $mainMenuContent = "";
        
        $ocdb=phpMyDB::GetInstance();
		
		$query = "select t1.idCategory, t1.name as mainCat, t1.friendlyName as mainCatFriendlyName, t1.idCategoryParent,
		t2.idCategory, t2.name as subCat, t2.friendlyName as subCatFriendlyName, t2.idCategoryParent  

		FROM ".TABLE_PREFIX."categories t1  LEFT OUTER JOIN  ".TABLE_PREFIX."categories t2

		ON  t1.idCategory = t2.idCategoryParent
                where t1.idCategoryParent=0

		order by t1.order, t2.order " ;

        $result=$ocdb->getRows($query);
        
        $currentMainCat = "";
	$prevMainCat = "";
	
        $currentSubCat = "";
	$prevSubCat = "";
		
	$mainMenuContent .= "\n<ul class=\"sf-menu sf-horizontal sf-js-enabled sf-shadow\" id=\"sf-top-navigation\"> \n" ; 
        $mainMenuContent .= "<li> <a href=\"" .SITE_URL. "\">" . T_("Home") .  "&nbsp;<img src=\"" .SITE_URL. "/images/menus/home_icon.gif\" width=\"10\" height=\"10\" ></a></li>";
	
        foreach ($result as $category_row ) {
            $currentMainCat = $category_row["mainCatFriendlyName"] ;
            $currentSubCat = $category_row["subCatFriendlyName"];
            
	    if($currentMainCat != $prevMainCat)
	    {
	        if($prevMainCat != "")
		{
		    if ($prevCity != "")
     		         $mainMenuContent .= "      </ul> \n    "  ;
		    
		    $mainMenuContent .= "      </li> \n"  ;
		}    
		    
		$mainMenuContent .= "    <li><a href=\"".SITE_URL.catURL($currentMainCat)."\">".$category_row["mainCat"];  
		
		$hrefpath = str_replace("/?category=","",catURL($currentMainCat));

		$hrefpathmobile = substr($hrefpath,0,6);  //mobile

		if($category_row["mainCat"] == "Real Estate") { 
			$MENU_IMAGE = "&nbsp;<img src=\"" .SITE_URL. "/images/menus/iconofficebuilding.gif\" width=\"12\" height=\"10\" >";
		}
		elseif($category_row["mainCat"] == "Electronics") { 
			$MENU_IMAGE = "&nbsp;<img src=\"" .SITE_URL. "/images/menus/electronics-icon-small-icon-1.gif\" width=\"12\" height=\"10\" >";
		}
		elseif($category_row["mainCat"] == "Kitchen Appliances") { 
			$MENU_IMAGE = "&nbsp;<img src=\"" .SITE_URL. "/images/menus/kitchenicon.gif\" width=\"12\" height=\"10\" >";
		}
		elseif($category_row["mainCat"] == "Jobs") { 
			$MENU_IMAGE = "&nbsp;<img src=\"" .SITE_URL. "/images/menus/jobsicon.gif\" width=\"12\" height=\"10\" >";
		}
		elseif($category_row["mainCat"] == "Home Appliances") { 
			$MENU_IMAGE = "&nbsp;<img src=\"" .SITE_URL. "/images/menus/iconhome.gif\" width=\"12\" height=\"10\" >";
		}
		elseif($category_row["mainCat"] == "Automobiles") { 
			$MENU_IMAGE = "&nbsp;<img src=\"" .SITE_URL. "/images/menus/redsedan.gif\" width=\"12\" height=\"10\" >";
		}
		elseif($hrefpathmobile == "mobile") { 
			$MENU_IMAGE = "&nbsp;<img src=\"" .SITE_URL. "/images/menus/mobileindex.jpg\" width=\"12\" height=\"10\" >";
		}
		elseif($category_row["mainCat"] == "Computers") { 
			$MENU_IMAGE = "&nbsp;<img src=\"" .SITE_URL. "/images/menus/compimages.jpg\" width=\"12\" height=\"10\" >";
		}
		elseif($category_row["mainCat"] == "Services") { 
			$MENU_IMAGE = "&nbsp;<img src=\"" .SITE_URL. "/images/menus/servicesimages.jpg\" width=\"12\" height=\"10\" >";
		}
		elseif($category_row["mainCat"] == "Others") { 
			$MENU_IMAGE = "&nbsp;<img src=\"" .SITE_URL. "/images/menus/globeimages.jpg\" width=\"12\" height=\"10\" >";
		}


		if($currentSubCat != "")
		{
		    //$mainMenuContent .= "<span class=\"sf-sub-indicator\"> &raquo; </span>" ;
		    $mainMenuContent .= "".$MENU_IMAGE. "</a> \n  <ul> \n"  ;
		}
		else
		    $mainMenuContent .= "</a> \n "  ;
		
		        
		$prevMainCat = $currentMainCat;
	    }
	    
	    if($currentSubCat  != "")
                $mainMenuContent .= "         <li><a href=\"".SITE_URL.catURL($currentSubCat)."\">".$category_row["subCat"]."</a></li> \n";            
        
	    $prevCity = $currentSubCat;
	} 
    $mainMenuContent .= "   </ul>\n</li>\n</ul> \n  " ;
	
    echo $mainMenuContent;


}

function generateMenuJS($selectedCategory){//tabbed top menu, param, the selected category
	$ocdb=phpMyDB::GetInstance();
	
	$style='default_page_item';//for the selected item
	$nstyle='page_item page-item';//normal style
	
	//home
	 if (!isset($selectedCategory)) $astyle=$style;
	 else $astyle=$nstyle;
	 echo '<li id="nav0" class="'.$astyle.'"><a onmouseover="ShowTab(0);" href="'.SITE_URL.'">'.T_("Home").'</a></li>';
	
	
	$query="SELECT name,friendlyName,idCategory from ".TABLE_PREFIX."categories where idCategoryParent=0 order by `order`";
	$result=$ocdb->getRows($query);
	
	foreach ($result as $category ) {
		$name=$category["name"];
		$fcategory=$category["friendlyName"];
		$idCategory=$category["idCategory"];
		if ($name!=""&&$fcategory!=""){
			$url=catURL($fcategory);	
			if ($selectedCategory==$fcategory) $astyle=$style;//selected category
			else $astyle=$nstyle;
			$Menu.="<li id=\"nav$idCategory\" class='".$astyle."'><a  onmouseover=\"ShowTab($idCategory);\" title=\"$name\" href=\"".SITE_URL."$url\">$name</a></li>";
			
		}
	} 
	echo $Menu;//home menu
}

function generateSubMenuJS($idCategoryParent,$categoryParent,$currentCategory){//generates thes submenu for a category
	
	$ocdb=phpMyDB::GetInstance();
	
	echo '<div class="sub" id="sub0"';
	if (isset($currentCategory)) echo ' style="display:none;" ';
	echo ">";
	generatePopularCategoriesJS();
	echo '</div>';
	
	if ($categoryParent!=0) $subCategory=$categoryParent; //if it's a subcategory
 	else { //its a category
 		if (!$idCategoryParent) $idCategoryParent=0;//if doesnt exist the category
 		$subCategory=$idCategoryParent;
 	}	
	$query="SELECT idCategory,name,friendlyName,
	   					(select name from ".TABLE_PREFIX."categories where idCategory=C.idCategoryParent limit 1) parent, 
	   					idCategoryParent
	   					FROM ".TABLE_PREFIX."categories C 
	   			where idCategoryParent!=0 
	   			order by idCategoryParent,`order`";
	$result=$ocdb->getRows($query);
	
	$parent="";
	foreach ($result as $row ) {	
			$name=$row['name'];
			$fcategory=$row['friendlyName'];
			$CategoryParent=$row['idCategoryParent'];
			
			if ($parent!=$row['parent']&&$row['parent']!=""){
				if ($parent!='') $subMenu.='</div>';
				$subMenu.="<div class=\"sub\" id=\"sub$CategoryParent\""; 
				if ($CategoryParent!=$subCategory) $subMenu.=' style="display:none;" ';
				$subMenu.=">";	
				$parent=$row['parent'];
			}
			
			if ($fcategory!=""){
				$url=catURL($fcategory,friendly_url($parent));	
				//$subMenu.=SEPARATOR;
				if ($currentCategory==$fcategory) $subMenu.=  "<b>";//for the selectd item
				$subMenu.="<a $astyle title=\"$name\" href=\"".SITE_URL."$url\">$name</a>";
				if ($currentCategory==$fcategory) $subMenu.=  "</b>";
			}
		}
 
	if ($subMenu!="") $subMenu.="</div>";
	echo $subMenu;
}

function generatePopularCategoriesJS(){//popular categories displayed in the menu
	$ocdb=phpMyDB::GetInstance();

	$query="select c.idCategory,c.friendlyName,c.name,count(c.idCategory) cont , (select friendlyName from ".TABLE_PREFIX."categories where idCategory=c.idCategoryParent limit 1) parent
						from ".TABLE_PREFIX."categories c
						inner join ".TABLE_PREFIX."posts p
					on p.idCategory=c.idCategory
			group by c.idCategory,c.friendlyName,c.name
			order by cont desc,c.name Limit 7";//where idCategoryParent!=0	
	$result=$ocdb->getRows($query);
	
	//$popularCategories="<b>".T_("Popular")."</b>";
	foreach ( $result as $category ) {
		$name=$category["name"];
		$fcategory=$category["friendlyName"];
		$cont=$category["cont"];
		$parent=$category["parent"];
	
		if ($name!=""){
			$url=catURL($fcategory,$parent);
			$popularCategories.="<a title=\"$name $cont\" href=\"".SITE_URL."$url\">$name</a>";
		}
	} 
	echo $popularCategories;
	
}

function getCategoriesList(){//for the home
    $ocdb=phpMyDB::GetInstance();
    $query="SELECT name,friendlyName,idCategory from ".TABLE_PREFIX."categories where idCategoryParent=0 order by `order`";
    $result=$ocdb->getRows($query);
    
    $i = 0;
	$q = count($result);
	$z = round($q/3);
	$k = ceil($q/3);

    foreach ($result as $category ) {
        $name=$category["name"];
        $fcategory=$category["friendlyName"];
        $idCat=$category["idCategory"];
        if ($name!=""&&$fcategory!=""){
            
            if ($i==0 or $i==$k) $list.= '<div class="cats_col1 cats_colums">';
		    elseif ($i==($z+$k)) $list.= '<div class="cats_col2 cats_colums">';

	        $url=catURL($fcategory);	
	       
	        $list.= '<ul><li class="cathead"><a title="'.$name.'" href="'.SITE_URL.$url.'">'.$name.'</a></li>';
	        
	        //get sub cats category
	            $query="SELECT idCategory,name,friendlyName
   					FROM ".TABLE_PREFIX."categories C 
       			where idCategoryParent!=0  and idCategoryParent=$idCat
       			order by idCategoryParent, `order`";
                $result2=$ocdb->getRows($query);

                $list.= "<div class=\"scroll\">" ;
                foreach ($result2 as $row ) {	
	                    $name2=ucfirst(strtolower($row['name']));
	                    $fcategory2=$row['friendlyName'];
	                    if ($fcategory!=""){
		                    $url=catURL($fcategory2,$fcategory);	              
		                    $list.= "<li><a title=\"$name2\" href=\"".SITE_URL."$url\">$name2</a></li>";
	                    }
                 }
                 
                 $list.= "</div>" ; 
	        //end get sub cats category
	        
	        $list.= '</ul>';
	        if ($i==($k-1) or $i==(($z+$k)-1) or $i==($q-1)) $list.='</div>';
		    $i++;
        }	//if name        
    } //for  
   return $list;
}

//#276FD4
?>