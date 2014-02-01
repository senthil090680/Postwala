<?php
////////////////////////////////////////////////////////////
//Retrieve data from DB, application controller
////////////////////////////////////////////////////////////

//starting variables for the app
	$app_time=microtime(true);//start time for the app
	$client_ip=$_SERVER['REMOTE_ADDR'];//ip of the client
	$cache = wrapperCache::GetInstance(CACHE_TYPE,CACHE_EXPIRE,CACHE_DATA_FILE);
	$ocdb = phpMyDB::GetInstance(DB_USER,DB_PASS,DB_NAME,DB_HOST,DB_CHARSET);//,DB_CHARSET,DB_TIMEZONE,'persistent'
	if (DEBUG) $ocdb->setDebug(true);//db debug
	if (CACHE_ACTIVE) $ocdb->setCache(true);//cache for DB
	hackerDefense();// begin hacker defense
//end
	
//get possible parameters for the app	
        
  	if (cG("category")!="" && cP("categoryIf")=="") $currentCategory=cG("category");
  	if (cG("category")=="" && cP("categoryIf")!="") $currentCategory=cP("categoryIf");
	if (cG("category")!="" && cP("categoryIf")!="") $currentCategory=cP("categoryIf");

	 if (is_numeric($currentCategory)) { 
		$currentCategoryFriendlyName = "select name,friendlyName from ".TABLE_PREFIX."categories where idCategory = '$currentCategory' ORDER BY name";
		$currentCategoryFriendlyNameRes = mysql_query($currentCategoryFriendlyName);

		 $rowFriendlyName = mysql_fetch_array($currentCategoryFriendlyNameRes);
		 
		 $currentCategory=$rowFriendlyName[friendlyName];
		 //if it is numeric we need to find the friendly name
	 }
	if (cG("title")!="") $currentTitle=cG("title");
        if (is_numeric(cG("item"))) $idItem=cG("item");
  	if (is_numeric(cG("page"))) $page = intval(cG("page"));//obtaining the page on get
  	
  	if (cG("type")!="") {
  	    $type=cG("type");
  	    if (!is_numeric($type)) $type=getTypeNum($type);//if its not numeric we need to find the numeric value
  	}
    
    if (cG("location")!="" || cG("citylocation")!="" || cP("advancedlocation")!="") {
  	    if(cG("location") == '' && cP("advancedlocation") == '') $location=cG("citylocation");
		if(cG("location") == '' && cP("citylocation") == '') $location=cP("advancedlocation");
		if(cG("citylocation") == '' && cP("advancedlocation") == '') $location=cG("location");
		if(cG("location") != '' && cP("advancedlocation") != '') $location=cP("advancedlocation");
 	    if (!is_numeric($location)) $location=getLocationNum($location);//if its not numeric we need to find the numeric value
  	}
  	
  	if (cG("contact")!="") {
  	    $contact=cG("contact");
  	}
//end get

//retrieve data for the category
    if (isset($currentCategory)) {	
	    $query="select idCategoryParent,description,name,idCategory from ".TABLE_PREFIX."categories where friendlyName='$currentCategory' Limit 1";
	    $result=$ocdb->query($query);
	    if (mysql_num_rows($result)){
		    $row=mysql_fetch_assoc($result);
		    $categoryParent=$row["idCategoryParent"] ;
			$categoryName=$row["name"];
			$categoryDescription=$row["description"];
			$idCategory=$row["idCategory"];
		    if ($categoryParent!=0){ //have parent is a subcategory we need the name for the parent category
			    $query="select friendlyName,name,description from ".TABLE_PREFIX."categories where idCategory=$categoryParent Limit 1";
			    $result=$ocdb->query($query);
			    if (mysql_num_rows($result)){
				    $row=mysql_fetch_assoc($result);
				    $selectedCategory=$row["friendlyName"];//category that needs to be selected in the menu
				    $selectedCategoryName=$row["name"];//name of the seleted category
				    //if the description is empty for a submenu we display the description of the parent
				    if ($categoryDescription=="")$categoryDescription=$row["description"];	
			    }
		    }
		    else{//doesnt have parent is a category
			    $selectedCategory=$currentCategory;//selected category is the same one
			    $idCategoryParent=$idCategory;//here we take the category ID
		    }
	    }
	    else unset($currentCategory);//nothing returned for that category
    }
//end data category
	
//data for the item
	if (isset($idItem)) {
		$query="select * from ".TABLE_PREFIX."posts where idPost=$idItem and isConfirmed=1 Limit 1";
		$result=$ocdb->query($query);
		if (mysql_num_rows($result)){
			$row=mysql_fetch_assoc($result);
			$itemTitle=$row["title"] ;
			$itemDescription=mediaPostDesc($row['description']);
			$itemDate=$row["insertDate"];
			$itemName=$row["name"];
                        $itemLocation=$row["idLocation"];
                        $location=$itemLocation;
			$itemPlace=$row["place"];
			$itemPrice=$row["price"];
			$itemPhone=$row["phone"];
			$itemEmail=$row["email"];
			$itemType=$row["type"];
			$itemPassword=$row["password"];
			$itemAvailable=$row["isAvailable"];
			
			if ( EXPIRE_POST!=0 && $itemAvailable==1 ){
				$expireItemDate = convert_datetime($itemDate) + (EXPIRE_POST * 24 * 60 * 60);//expire unix time date
				if (time() > $expireItemDate){//the post is expired	
					$itemAvailable=0;
					$ocdb->update(TABLE_PREFIX.'posts','isAvailable=0','idPost='.$idItem);
					if (CACHE_DEL_ON_POST) deleteCache();//delete cache
					if (OCAKU) {
						$ocaku=new ocaku();
						$ocaku->deactivatePost(array('KEY'=>OCAKU_KEY,'idPostInClass'=>$idItem));
					}
				}
			}
			
			if ($row["hasImages"]==1) $itemImages=getPostImages($idItem,setDate($itemDate));//getting the images

			//postsHits add new hit row TODO decide if we allow once per IP....
			if (COUNT_POSTS) {
				$ocdb->insert(TABLE_PREFIX."postshits (idPost,ip)","$idItem,'$client_ip'");
				$itemViews=$ocdb->getValue("SELECT count(idPost) FROM ".TABLE_PREFIX."postshits where idPost=$idItem","none");
			}
		}
		else unset($idItem);//nothing returned for that item
	}
	//sitemap
    elseif(strpos($_SERVER["SCRIPT_NAME"], "site-map.php")>0){
	    $query="SELECT name,friendlyName,description,
	            (select friendlyName from ".TABLE_PREFIX."categories where idCategory=c.idCategoryParent limit 1) parent
	            FROM ".TABLE_PREFIX."categories c order by idCategoryParent,`order`";
	    $resultSitemap=$ocdb->getRows($query);
	    $query="SELECT * FROM ".TABLE_PREFIX."locations C order by idLocationParent,idLocation";
	    $resultSitemapLoc=$ocdb->getRows($query);
    }
    //RSS Feed
    elseif(strpos($_SERVER["SCRIPT_NAME"], "feed-rss.php")>0){
            if (isset($type) && $type!='') $filter.= " and p.type=".$type;

            if (isset($currentCategory)){//theres a category set
				if ($categoryParent!=0) $filter.= " and c.idCategory=$idCategory ";//parent category
				else $filter.= " and ( c.idCategory=$idCategory  or c.idCategoryParent=$idCategoryParent) ";//sub category
		    }
		    
		    if (LOCATION && isset($location)){//there's a location set
		            if ($location!=0) {
		            	$filter.= " and p.idLocation=$location ";//location
		            	//retrieving siblings!!!
		            	$query="select l.idLocation from ".TABLE_PREFIX."locations l where l.idLocationParent=".$location;
			        	$resultL=$ocdb->getRows($query);
		            	foreach($resultL as $l) $locationfilter.=$l['idLocation'].',';
		            	if (strlen($locationfilter)>0) $filter.= ' or p.idLocation in ('.substr($locationfilter,0,-1).')';
		            }
				}

            if (strlen(cG("s"))>=MIN_SEARCH_CHAR) $filter.= " and (p.title like '%".cG("s")."%' or  p.description like '%".cG("s")."%') ";

	        $query="SELECT p.idPost,p.title,p.description,p.insertDate,p.place,
					            c.Name category,c.idCategoryParent,p.type,p.price, c.friendlyName,p.hasImages,p.idLocation,
					            (select friendlyName from ".TABLE_PREFIX."categories where idCategory=c.idCategoryParent limit 1) parent
					            FROM ".TABLE_PREFIX."posts p
						            inner join ".TABLE_PREFIX."categories c
				            on c.idCategory=p.idCategory
			            where p.isAvailable=1 and p.isConfirmed=1 $filter
			            order by p.insertDate Desc LIMIT ".RSS_ITEMS;
	        $resultRSS=$ocdb->getRows($query);
    
    }
	//search for home index.php
	elseif( (( strpos($_SERVER["SCRIPT_NAME"], "index.php")>0 && !strpos($_SERVER["SCRIPT_NAME"], "admin")>0 ) || ( strpos($_SERVER["SCRIPT_NAME"], "listing.php")>0) ) && (cP("publishdate") == '') ){
		
		if (isset($type)) $filter.= " and p.type=".$type;
		
		if (isset($currentCategory)){//there's a category set
            if ($categoryParent!=0) $filter.= " and c.idCategory=$idCategory ";//parent category
            else $filter.= " and ( c.idCategory=$idCategory  or c.idCategoryParent=$idCategoryParent) ";//sub category
		}
        
		if (LOCATION && isset($location)){//there's a location set
            if ($location!=0) {            	
            	//retrieving siblings!!!
            	$query="select l.idLocation from ".TABLE_PREFIX."locations l where l.idLocationParent=".$location;
	        	$resultL=$ocdb->getRows($query);
            	foreach($resultL as $l) $locationfilter.=$l['idLocation'].',';
            	if (strlen($locationfilter)>0) $filter.= ' and ( p.idLocation='.$location.' or p.idLocation in ('.substr($locationfilter,0,-1).') )';
            	else $filter.= " and p.idLocation=$location ";//location
            }
		}
        
        if (isset($contact)){//there's an account set
            if ($contact!="0"){
                $account = Account::createById($contact);
                if ($account->exists){
                    $account_email=$account->email;
                    $filter.= " and p.email='$account_email' ";//email
                } else $filter.= " and p.email='' ";//email
            }
		}

		// normal search
		if (strlen(cG("s"))>=MIN_SEARCH_CHAR){
			if (strpos(cG("s"), ",")>0){//search for location
				$search=explode(",",cG("s"));
				$filter.= " and (p.title like '%$search[0]%' or  p.description like '%$search[0]%' or p.place like '%$search[1]%') ";
			}
			else $filter.= " and (p.title like '%".cG("s")."%' or  p.description like '%".cG("s")."%') ";
		}
		//end search
		
		//advanced search
		$advs=false;
		if (strlen(cG("title"))>=MIN_SEARCH_CHAR){
			//$filter.= " and p.title like '%".cG("title")."%' ";
			$filter.= " and (p.title REGEXP '[[:<:]]" . cG("title") . "[[:>:]]'";
			//$filter.= " and p.description like '%".cG("title")."%' ";
			$filter.= " or p.description REGEXP '[[:<:]]" . cG("title") . "[[:>:]]')";
			$advs=true;
		}
		if (strlen(cG("desc"))>=MIN_SEARCH_CHAR){
			$filter.= " and p.description like '%".cG("desc")."%' ";
			$advs=true;
		}
		if (is_numeric(cG("price"))){
			$filter.= " and p.price<=".cG("price")." ";
			$advs=true;
		}
		if (is_numeric(cG("active"))){
			$filter.= " and p.isAvailable=".cG("active")." ";
			$advs=true;
		}
		if (is_numeric(cG("reviewed"))){
			$filter.= " and p.isConfirmed=".cG("reviewed")." ";
			$advs=true;
		}
		if (strlen(cG("place"))>=MIN_SEARCH_CHAR){
			$filter.= " and p.place like '%".cG("place")."%' ";
			$advs=true;
		}
		if (cG("sort")!=""){
			if (cG("sort")=="price-desc") $order= "p.price Desc";
			elseif (cG("sort")=="price-asc") $order= "p.price Asc";
			$advs=true;
		}
		else $order="p.insertDate Desc";
		//end adv search
		
			//pagination
			$query="SELECT count(p.idPost) total	FROM ".TABLE_PREFIX."posts p
								inner join ".TABLE_PREFIX."categories c
						on c.idCategory=p.idCategory
					where 1 = 1 ";
					
					if(!isset($_SESSION['admin']))
					    $query .= " AND p.isAvailable=1 and p.isConfirmed=1 "  ;
					
					$query .= " $filter ";
					
					
			$total_records = $ocdb->getValue($query);//query to get total of records
			
			//echo $query;
			//echo "Count:". $total_records; 				
			
			$records_per_page = ITEMS_PER_PAGE;//how many records per page displayed
			$total_pages = ceil($total_records / $records_per_page);//total pages to display
			if ($page < 1 || $page > $total_pages) $page = 1;//controlling that the page have a valid value
			$offset = ($page - 1) * $records_per_page;//position where we need to display
			$limit = " LIMIT $offset, $records_per_page";//sql to attach IMPORTANT
			//end pagination
		
			$query="SELECT p.idPost,p.title,p.Edited,p.description,p.insertDate,l.name as Location_Name, 
							c.Name category,c.friendlyName fcategory,c.idCategoryParent,p.type,p.price,p.password,p.hasImages,
							(select friendlyName from classifieds_categories where idCategory=c.idCategoryParent limit 1) parent, 
							p.isAvailable ,p.isConfirmed, IFNULL(adbatch.ImageName, '') as ImageName 
							FROM classifieds_posts p
								inner join classifieds_categories c
						on c.idCategory=p.idCategory 
								inner join classifieds_locations l
						on p.idLocation=l.idLocation

						left outer join classifieds_premium_posts  pp1 
						on p.idPost = pp1.idPost 
						AND CURDATE() between pp1.StartDate and pp1.EndDate 
						AND Paid = 'Y'
						
						left outer join classifieds_premium_adtypes adtype
						on pp1.idAdType = adtype.idAdType  
						AND adtype.Active = 'Y'

						left outer join classifieds_premium_adbatches adbatch
						on adbatch.idBatch = pp1.idBatch  
						AND adtype.Active = 'Y'

						

					where 1=1 " ;
					

					if (cG("category")!="") {
						$Categoryid=cG("category");
						if($Categoryid == 'all') {
							$filter = '';
							$currentCategory='all';
						}
					}
					if(!isset($_SESSION['admin']))
					    $query .= " AND p.isAvailable=1 and p.isConfirmed=1 "  ;
					
					$query .= " $filter order by adbatch.idBatch Desc ,  $order $limit";
					
			//echo $query;
			$resultSearch=$ocdb->getRows($query);
			//echo "Count:". count($resultSearch);
			if($currentCategory == 'all') {
				$categoryName='all';
				$categoryDescription='all';
			}
	}
	elseif(cP("publishdate")) {
		$publishDate				=			cP("publishdate");
		$curDate					=			date("Y-m-d")." 23:59:59";
		if($publishDate == 'ltone'){
			$lessThanOne		=	mktime(date("H"),date("i"),date("s"),date('m'),date('d')-30,date('Y'));
			$lessThanOne		=	date("Y-m-d H:i:s",$lessThanOne);
			$filter				.=	"AND ((cpd.PostUpdatedDate >= '$lessThanOne' and cpd.PostUpdatedDate <= '$curDate') OR (p.insertDate >= '$lessThanOne' and p.insertDate <= '$curDate'))";
		}
		elseif($publishDate == 'ltthree'){			
			$lessThanThree		=	mktime(date("H"),date("i"),date("s"),date('m'),date('d')-90,date('Y'));
			$lessThanThree		=	date("Y-m-d H:i:s",$lessThanThree);
			$filter				.=	"AND ((cpd.PostUpdatedDate >= '$lessThanThree' and cpd.PostUpdatedDate <= '$curDate') OR (p.insertDate >= '$lessThanThree' and p.insertDate <= '$curDate'))";
		}
		elseif($publishDate == 'ltsix'){			
			$lessThanSix		=	mktime(date("H"),date("i"),date("s"),date('m'),date('d')-180,date('Y'));
			$lessThanSix		=	date("Y-m-d H:i:s",$lessThanSix);
			$filter				.=	"AND ((cpd.PostUpdatedDate >= '$lessThanSix' and cpd.PostUpdatedDate <= '$curDate') OR (p.insertDate >= '$lessThanSix' and p.insertDate <= '$curDate'))";
		}
		elseif($publishDate == 'gtsix'){			
			$greaterThanSix		=	mktime(date("H"),date("i"),date("s"),date('m'),date('d')-180,date('Y'));
			$greaterThanSix		=	date("Y-m-d H:i:s",$greaterThanSix);
			$filter				.=	"AND ((cpd.PostUpdatedDate <= '$greaterThanSix') OR (p.insertDate <= '$greaterThanSix'))";
		}

		$totalcustom			=	cP("totalcustom");
		
		
		for($i = 0; $i <= $totalcustom; $i++) {			
			$idField			=	"idField".$i;	
			$customfieldId		=	cP($idField);
			$customFieldId[]	=	$customfieldId;

			$customfield		=	"customfield".$i;	
			$customfieldVal		=	cP($customfield);
			
			$customfieldValArray[]	=	$customfieldVal;
			$customfieldVal		=	str_replace("-"," ",$customfieldVal);
			$customfieldVal		=	str_replace("offer","(Offer)",$customfieldVal);
			$customfieldVal		=	ucfirst(str_replace("need","(Need)",$customfieldVal));
			$customfieldValArrayQuery[]	=	$customfieldVal;
		}
		$customFieldId			=	implode("','",$customFieldId);
		$customFieldValue		=	implode("','",$customfieldValArrayQuery);
		$customFields			=	implode(',',$customfieldValArray);
			
		$customfilter			.=	" and (cpd.idField in ('".$customFieldId."') and cpd.FieldValue in ('".$customFieldValue."'))";
	
		// THIS IS CORRELATED SUBQUERY EXAMPLE, BUT NOT IMPLEMENTED HERE
		//echo $customSearchQuery=	"select * from ".TABLE_PREFIX."posts p LEFT JOIN ".TABLE_PREFIX."posts_ad_data cpd on p.idPost=cpd.idPost where 1=1 $filter and exists (select * from ".TABLE_PREFIX."posts_ad_data where 1=1 $customfilter)";

		$customSearchQuery		=	"select * from ".TABLE_PREFIX."posts p LEFT JOIN ".TABLE_PREFIX."posts_ad_data cpd on p.idPost=cpd.idPost where 1=1 $filter $customfilter GROUP BY p.idPost ";

		if (isset($type)) $filter.= " and p.type=".$type;
		
		if (isset($currentCategory)){//there's a category set
			$filter.= " and c.idCategory=$idCategory "; //category in condition
		}
        
		if (LOCATION && isset($location)){//there's a location set
            if ($location!=0) {            	
            	//retrieving siblings!!!
            	$query="select l.idLocation from ".TABLE_PREFIX."locations l where l.idLocationParent=".$location;
	        	$resultL=$ocdb->getRows($query);
            	foreach($resultL as $l) $locationfilter.=$l['idLocation'].',';
            	if (strlen($locationfilter)>0) $filter.= ' and ( p.idLocation='.$location.' or p.idLocation in ('.substr($locationfilter,0,-1).') )';
            	else $filter.= " and p.idLocation=$location ";//location
            }
		}
        
        if (isset($contact)){//there's an account set
            if ($contact!="0"){
                $account = Account::createById($contact);
                if ($account->exists){
                    $account_email=$account->email;
                    $filter.= " and p.email='$account_email' ";//email
                } else $filter.= " and p.email='' ";//email
            }
		}

		// normal search
		if (strlen(cG("s"))>=MIN_SEARCH_CHAR){
			if (strpos(cG("s"), ",")>0){//search for location
				$search=explode(",",cG("s"));
				$filter.= " and (p.title like '%$search[0]%' or  p.description like '%$search[0]%' or p.place like '%$search[1]%') ";
			}
			else $filter.= " and (p.title like '%".cG("s")."%' or  p.description like '%".cG("s")."%') ";
		}
		//end search
		
		//advanced search
		$advs=false;
		if (strlen(cG("title"))>=MIN_SEARCH_CHAR){
			//$filter.= " and p.title like '%".cG("title")."%' ";
			$filter.= " and (p.title REGEXP '[[:<:]]" . cG("title") . "[[:>:]]'";
			//$filter.= " and p.description like '%".cG("title")."%' ";
			$filter.= " or p.description REGEXP '[[:<:]]" . cG("title") . "[[:>:]]')";
			$advs=true;
		}
		if (strlen(cG("desc"))>=MIN_SEARCH_CHAR){
			$filter.= " and p.description like '%".cG("desc")."%' ";
			$advs=true;
		}
		if (is_numeric(cG("price"))){
			$filter.= " and p.price<=".cG("price")." ";
			$advs=true;
		}
		if (is_numeric(cG("active"))){
			$filter.= " and p.isAvailable=".cG("active")." ";
			$advs=true;
		}
		if (is_numeric(cG("reviewed"))){
			$filter.= " and p.isConfirmed=".cG("reviewed")." ";
			$advs=true;
		}
		if (strlen(cG("place"))>=MIN_SEARCH_CHAR){
			$filter.= " and p.place like '%".cG("place")."%' ";
			$advs=true;
		}
		if (cG("sort")!=""){
			if (cG("sort")=="price-desc") $order= "p.price Desc";
			elseif (cG("sort")=="price-asc") $order= "p.price Asc";
			$advs=true;
		}
		else $order="p.insertDate Desc";
		//end adv search
		
			//pagination
			$query="SELECT count(p.idPost) total	FROM ".TABLE_PREFIX."posts_ad_data cpd
								RIGHT JOIN ".TABLE_PREFIX."posts p 
						on cpd.idPost=p.idPost inner join ".TABLE_PREFIX."categories c
						on c.idCategory=p.idCategory
					where 1 = 1 ";
					
					if(!isset($_SESSION['admin']))
					    $query .= " AND p.isAvailable=1 and p.isConfirmed=1 "  ;
					
					$query .= " $filter GROUP BY p.idPost";
					
					
			$total_records = $ocdb->getValue($query);//query to get total of records
			
			//echo $query;
			//echo "Count:". $total_records; 				
			
			$records_per_page = ITEMS_PER_PAGE;//how many records per page displayed
			$total_pages = ceil($total_records / $records_per_page);//total pages to display
			if ($page < 1 || $page > $total_pages) $page = 1;//controlling that the page have a valid value
			$offset = ($page - 1) * $records_per_page;//position where we need to display
			$limit = " LIMIT $offset, $records_per_page";//sql to attach IMPORTANT
			//end pagination
		
			$query="SELECT p.idPost,p.title,p.Edited,p.description,p.insertDate,l.name as Location_Name, 
							c.Name category,c.friendlyName fcategory,c.idCategoryParent,p.type,p.price,p.password,p.hasImages,
							(select friendlyName from classifieds_categories where idCategory=c.idCategoryParent limit 1) parent, 
							p.isAvailable ,p.isConfirmed, IFNULL(adbatch.ImageName, '') as ImageName 
							FROM ".TABLE_PREFIX."posts_ad_data cpd RIGHT JOIN classifieds_posts p
								on p.idPost=cpd.idPost inner join classifieds_categories c
						on c.idCategory=p.idCategory 
								inner join classifieds_locations l
						on p.idLocation=l.idLocation

						left outer join classifieds_premium_posts  pp1 
						on p.idPost = pp1.idPost 
						AND CURDATE() between pp1.StartDate and pp1.EndDate 
						AND Paid = 'Y'
						
						left outer join classifieds_premium_adtypes adtype
						on pp1.idAdType = adtype.idAdType  
						AND adtype.Active = 'Y'

						left outer join classifieds_premium_adbatches adbatch
						on adbatch.idBatch = pp1.idBatch  
						AND adtype.Active = 'Y'

						

					where 1=1 " ;
					

					if (cG("category")!="") {
						$Categoryid=cG("category");
						if($Categoryid == 'all') {
							$filter = '';
							$currentCategory='all';
						}
					}
					if(!isset($_SESSION['admin']))
					    $query .= " AND p.isAvailable=1 and p.isConfirmed=1 "  ;
					
					$query .= " $filter $customfilter GROUP BY p.idPost order by adbatch.idBatch Desc ,  $order $limit";
					
			//echo $query;
			$resultSearch=$ocdb->getRows($query);
			//echo "Count:". count($resultSearch);
			if($currentCategory == 'all') {
				$categoryName='all';
				$categoryDescription='all';
			}







	}
?>