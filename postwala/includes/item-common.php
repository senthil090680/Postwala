<?php
#============================================================================================================
# Author 		: J P Senthil Kumar
# Start Date	: 20 May 2011
# End Date		: 24 May 2011
# Project		: Publish A New Ad
# Description	: This file is used to add a new advertisement with the custom fields coming dynamically
#============================================================================================================
////////////////////////////////////////////////////////////
function getPrice($amount){//returns the price for the item in the correct format
	return str_replace(array("AMOUNT","CURRENCY"),array($amount,CURRENCY),CURRENCY_FORMAT);
	//return $amount;
}
////////////////////////////////////////////////////////////
function isSpam($name,$email,$comment){//return if something is spam or not using akismet, and checking the spam list
	$ocdb=phpMyDB::GetInstance();
	$res=$ocdb->getValue("SELECT idPost FROM ".TABLE_PREFIX."posts p where isAvailable=2  and email='$email' LIMIT 1","none");//check spam tags
	if ($res==false){//nothing found
		if (AKISMET!=""){
			$akismet = new Akismet(SITE_URL ,AKISMET);//change this! or use defines with that name!
			$akismet->setCommentAuthor($name);
			$akismet->setCommentAuthorEmail($email);
			$akismet->setCommentContent($comment);
			return $akismet->isCommentSpam();
		}
		else return false;//we return is not spam since we do not have the api :(
	}
	else return true;//ohohoho SPAMMER!

}
////////////////////////////////////////////////////////////
function isInSpamList($ip){//return is was taged as spam (in /manage is where we tag)
	$ocdb=phpMyDB::GetInstance();
	$res=$ocdb->getValue("SELECT idPost FROM ".TABLE_PREFIX."posts p where isAvailable=2  and ip='$ip' LIMIT 1","none");

	if(!empty($res)) return true;//we had tagged him before as spammer
	elseif (empty($res) && SPAM_COUNTRY){
	    $geoip=geoIP();
	    $countries = explode(',',SPAM_COUNTRIES);
	    if ( in_array($geoip['country'],$countries) || $geoip['country']=='(unknown country?)')  return true;//ohohoho SPAMMER!
	    else return false;//nothing found
	}
	else return false;
}
////////////////////////////////////////////////////////////
function check_images_form(){//get values by reference to allow change them. Used in new item and manage item
    //image check
    $image_check=1;
    if (MAX_IMG_NUM>0){	//image upload active if there's more than 1
		$types=split(",",IMG_TYPES);//creating array with the allowed types print_r ($types);

		for ($i=1;$i<=MAX_IMG_NUM && is_numeric($image_check);$i++){//loop for all the elements in the form

		    if (file_exists($_FILES["pic$i"]['tmp_name'])){//only for uploaded files

			    $imageInfo = getimagesize($_FILES["pic$i"]["tmp_name"]);
			    $file_mime = strtolower(substr(strrchr($imageInfo["mime"], "/"), 1 ));//image mime
			    $file_ext  = strtolower(substr(strrchr($_FILES["pic$i"]["name"], "."), 1 ));//image extension

			    if ($_FILES["pic$i"]['size'] > MAX_IMG_SIZE) {//control the size
				     $image_check=T_("Picture")." $i ".T_("Upload pictures max file size")." ".(MAX_IMG_SIZE/1000000)."Mb";
			    }
			    elseif (!in_array($file_mime,$types) || !in_array($file_ext,$types)){//the size is right checking type and extension
				     $image_check=T_("Picture")." $i no ".T_("format")." ".IMG_TYPES;
			    }//end else

			    $image_check++;

			}//end if existing file
		}//end loop
	}//end image check
	return $image_check;
}
////////////////////////////////////////////////////////////
function upload_images_form($idPost,$title,$date=0){//upload image files from the form.  Used in new item and manage item
	$date = standarizeDate($date);
    //images upload and resize
	if (MAX_IMG_NUM>0){

	//create dir for the images
		if ($date!=0) {
		    $date = standarizeDate($date);
	        $date = explode('-',$date);
		}
		if (count($date)==3){ //there's a date where needs to be uploaded
			$imgDir=$date[2].'/'.$date[1].'/'.$date[0].'/'.$idPost;
		}
		else $imgDir=date("Y").'/'.date("m").'/'.date("d").'/'.$idPost; //no date

		$up_path=IMG_UPLOAD_DIR.$imgDir;
		umask(0000);
		mkdir($up_path, 0755,true);//create folder for item


		$needFolder=false;//to know if it's needed the folder
		//upload images
		for ($i=1;$i<=MAX_IMG_NUM;$i++){
		    if (file_exists($_FILES["pic$i"]['tmp_name'])){//only for uploaded files
			    $file_name = $_FILES["pic$i"]['name'];
			    $file_name = friendly_url($title).'_'.$i. strtolower(substr($file_name, strrpos($file_name, '.')));
			    $up_file=$up_path."/".$_FILES["pic$i"]['name'];

			    if (move_uploaded_file($_FILES["pic$i"]['tmp_name'],$up_file)){ //file uploaded
				    //resizing images
				    $thumb=new thumbnail($up_file);
				    $thumb->size_width(IMG_RESIZE);	   // set width  for thumbnail
				    $thumb->save($up_path."/".$file_name);
				    unset($thumb);
				    //create thumb
				    $thumb=new thumbnail($up_file);
				    $thumb->size_width(IMG_RESIZE_THUMB);	   // set biggest width for thumbnail
				    $thumb->save($up_path."/thumb_$file_name");
				    unset($thumb);
				    @unlink($up_file);//delete old file
				    $needFolder=true;
			    }
			}//end if file exists
		}
		if (!$needFolder) @rmdir($up_path);//the folder is not needed no files uploaded
	}
	//end images
}
////////////////////////////////////////////////////////////
function getPostImages($idPost,$date,$just_one=false,$thumb=false){
	$no_pic=SITE_URL."/images/No Image Available.jpg";
	$date = standarizeDate($date);
	$date=explode('-',$date);
	if (count($date)==3){//is_date
		$types=split(",",IMG_TYPES);//creating array with the allowed images types

		$imgUrl=SITE_URL.IMG_UPLOAD;//url for the image
		$imgPath=IMG_UPLOAD_DIR;//path of the image

		$imgDir=$date[2].'/'.$date[1].'/'.$date[0].'/'.$idPost.'/';	//$imgDir=$idPost.'/';

		$files = scandir($imgPath.$imgDir);
		foreach($files as $img){//searching for images
			$file_ext  = strtolower(substr(strrchr($img, "."), 1 ));//get file ext
			if (in_array($file_ext,$types))$images[]=$img;//we only keep images with allowed ext
		}
		//print_r($images);
		if (count($images)>0){//there's at least 1 image
			foreach($images as $img){

				$is_thumb=(substr($img,0,6)=='thumb_');

				if ($just_one){//we want just one image
					if (!$thumb && !$is_thumb) return $imgUrl.$imgDir.$img;//first image match
					elseif($thumb && $is_thumb) return $imgUrl.$imgDir.$img;//first thumb match
				}
				else{//we want all the images
					if (!$thumb && !$is_thumb) {//images and thumbs
						$r_images[]=array($imgUrl.$imgDir.$img,$imgUrl.$imgDir.'thumb_'.$img);//images array
					}
					elseif($thumb && $is_thumb){//only thumbs
						$r_images[]=$imgUrl.$imgDir.$img;//thumbs array
					}
				}

			}
		}
		elseif($thumb) return $no_pic;//nothing in the folder

		return $r_images;
	}//no date :(
	else return $no_pic;
}

////////////////////////////////////////////////////////////
function deletePostImages($idPost,$date=0){
	if ($date!=0) {
	    $date = standarizeDate($date);
	    $dateD=explode('-',$date);
	}
	else $dateD=0;
	
	if (count($dateD)!=3){// we do not have the date for the post retrieving from DB
		$ocdb=phpMyDB::GetInstance();
		$date=setDate($ocdb->getValue("select insertDate from ".TABLE_PREFIX."posts where idPost=$idPost Limit 1",'none'));
		$dateD=explode('-',$date);
	}

    if (count($dateD)==3){
    	$imgPath=IMG_UPLOAD_DIR.$dateD[2].'/'.$dateD[1].'/'.$dateD[0].'/'.$idPost;//path images
    	if (is_dir($imgPath)) removeRessource($imgPath);//delete
    }

    return $date;//we return the date to reuse in other places
}

////////////////////////////////////////////////////////////
function mediaPostDesc($the_content){//from a description add the media
//using http://www.robertbuzink.nl/journal/2006/11/23/youtube-brackets-wordpress-plugin/
    if (VIDEO){
        $stag = "[youtube=http://www.youtube.com/watch?v=";
        $etag = "]";
        $spos = strpos($the_content, $stag);
        if ($spos !== false){
            $epos = strpos($the_content, $etag, $spos);
            $spose = $spos + strlen($stag);
            $slen = $epos - $spose;
            $file  = substr($the_content, $spose, $slen);
			//youtube
            $tags = '<object width="425" height="350">
                    <param name="movie" value="'.$file.'"></param>
                    <param name="wmode" value="transparent" ></param>
                    <embed src="http://www.youtube.com/v/'. $file.'" type="application/x-shockwave-flash" wmode="transparent" width="425" height="350"></embed>
                    </object>';
            $new_content = substr($the_content,0,$spos);
            $new_content .= $tags;
            $new_content .= substr($the_content,($epos+1));

            if ($epos+1 < strlen($the_content)) {//reciproco
                $new_content = mediaPostDesc($new_content);
            }
            return $new_content;
        }
        else return $the_content;
    }
    else return $the_content;
}

////////////////////////////////////////////////////////////
function confirmPost($post_id,$post_password,$edited=0){//confirm a post
	$ocdb=phpMyDB::GetInstance();
	//update table
	$ocdb->update(TABLE_PREFIX."posts","isConfirmed=1","idPost=$post_id and password='$post_password'");
	
	//redirect to the item
	$query="select email, title,type,friendlyName,password,c.name cname,p.description,p.name AS UserName,price,hasImages,p.insertDate,p.idLocation,p.place,
	        (select friendlyName from ".TABLE_PREFIX."categories where idCategory=c.idCategoryParent limit 1) parent
			    from ".TABLE_PREFIX."posts p 
			    inner join ".TABLE_PREFIX."categories c
			    on c.idCategory=p.idCategory
			where idPost=$post_id and password='$post_password' and isConfirmed=1 Limit 1";
	$result=$ocdb->query($query);
	if (mysql_num_rows($result)){
		$row=mysql_fetch_assoc($result);
		$title=$row["title"];
		$postTitle=friendly_url($title);
		$postTypeName=getTypeName($row["type"]);
		$fcategory=$row["friendlyName"];
		$parent=$row["parent"];
		$UserName=$row["UserName"];
		$email=$row["email"];
			
		$postUrl=itemURL($post_id,$fcategory,$postTypeName,$postTitle,$parent);
		
	    $bodyHTML='New ad '.SITE_URL.$postUrl.'<br />
		<a href="'.SITE_URL.'/manage/?post='.$post_id.'&amp;pwd='.$post_password.'&amp;action=edit">'.T_("Edit").'</a>'.SEPARATOR.'
		<a href="'.SITE_URL.'/manage/?post='.$post_id.'&amp;pwd='.$post_password.'&amp;action=deactivate">'.T_("Deactivate").'</a>'.SEPARATOR.'
		<a href="'.SITE_URL.'/manage/?post='.$post_id.'&amp;pwd='.$post_password.'&amp;action=spam">'.T_("Spam").'</a>'.SEPARATOR.'
		<a href="'.SITE_URL.'/manage/?post='.$post_id.'&amp;pwd='.$post_password.'&amp;action=delete">'.T_("Delete").'</a>';
		sendEmail(NOTIFY_EMAIL,"NEW ad in ".SITE_URL,$bodyHTML);//email to the NOTIFY_EMAIL	
			
		if (CACHE_DEL_ON_POST) deleteCache();//delete cache on post if is activated
		if (SITEMAP_DEL_ON_POST) generateSitemap();//new item generate sitemap
		
		//only if you pay using paypal
		//if (PAYPAL_ACTIVE){
			 //generate the email to send to the client
			if(FRIENDLY_URL) {
			    $linkDeactivate=SITE_URL."/manage/?post=$post_id&pwd=$post_password&action=deactivate";
			    $linkEdit=SITE_URL."/manage/?post=$post_id&pwd=$post_password&action=edit";
			}
			else{
			    $linkDeactivate=SITE_URL."/content/item-manage.php?post=$post_id&pwd=$post_password&action=deactivate";
			    $linkEdit=SITE_URL."/content/item-manage.php?post=$post_id&pwd=$post_password&action=edit";
			}
			
			$imageURL		=	SITE_URL."/images/postwala-gif.gif";
			
			$PriceImage		=	SITE_URL."/images/Pricemask_promo.jpg";
			
			$PostwalaURL	=	SITE_URL;

			$PricemaskURL	=	PRICEMASK_URL;
			

			if($edited == 1) {
				$emailWord		=	T_("You have edited/activated your Ad");
				$emailWord2		=	T_("and we approved it ");

			}
			else { 
				$emailWord	=	T_("Your New Ad"); 
				$emailWord2	=	T_("has been posted");
			}
			$message="<p>".$emailWord."&nbsp;<a href='".SITE_URL.$postUrl."'>$postTitle</a>&nbsp;".$emailWord2." on ". SITE_NAME . ".</p>"; 
					
	        $message.="<p>".T_("If you want to see your Ad click here").":
						<a href='".SITE_URL.$postUrl."'>$postTitle</a><br />".
	        			"<p>".T_("If you want to edit your Ad click here").":
						<a href='$linkEdit'>$postTitle</a><br /><br />".
						T_("If you want to deactivate your Ad click here").":
						<a href='$linkDeactivate'>$postTitle</a></p>";

			$message.="<p>".T_("Thanks for posting your Ad in ". SITE_NAME . ".")."</p>" ;

			$message.="<p>".T_("Do you want to post some more Ads ? : ")."<a href='".$PostwalaURL."'>".T_("Click Here")."</a></p>" ;					

			$UserName		=	ucwords($UserName);

	        $array_content[]=array("ACCOUNT", T_($UserName));
			$array_content[]=array("IMAGEURL", $imageURL);
			$array_content[]=array("PRICEIMAGE", $PriceImage);
			$array_content[]=array("POSTURL", $PostwalaURL);
			$array_content[]=array("PRICEURL", $PricemaskURL);

			$array_content[]=array("MESSAGE", $message);
	        $bodyHTML=buildEmailBodyHTML($array_content);
	         
			 //echo $email;
			 //echo $title;
			 //echo $bodyHTML;

	         //email to payer
	         sendEmail($email,$title,$bodyHTML);
			//}
		
		
		
		//if(!PAYPAL_ACTIVE){
			alert(T_("Ad was successfully activated, thank you"));
			jsRedirect(SITE_URL.$postUrl);
		//}
	}
}

////////////////////////////////////////////////////////////
function deactivatePost($post_id,$post_password){//deactivate a post
	$ocdb=phpMyDB::GetInstance();
	
	$ocdb->update(TABLE_PREFIX."posts","isAvailable=0","idPost=$post_id and password='$post_password'");
		if (CACHE_DEL_ON_POST) deleteCache();//delete cache
        
        //show confirmation message or return to admin listing
        if (isset($_SESSION['admin']) && isset($_SESSION['ADMIN_QUERY_STRING'])){
            $pag_url=SITE_URL."/admin/listing.php?rd=deactivate&".$_SESSION['ADMIN_QUERY_STRING'];
            redirect($pag_url);//redirect to the admin listing
        } else
		  echo "<div id='sysmessage'>".T_("Your Ad was successfully deactivated")."</div>";
}

////////////////////////////////////////////////////////////
function activatePost($post_id,$post_password){//activate a post
	$ocdb=phpMyDB::GetInstance();
	$ocdb->update(TABLE_PREFIX."posts","isAvailable=1","idPost=$post_id and password='$post_password'");
		if (CACHE_DEL_ON_POST) deleteCache();//delete cache 
        
        //show confirmation message or return to admin listing
        if (isset($_SESSION['admin']) && isset($_SESSION['ADMIN_QUERY_STRING'])){
            $pag_url=SITE_URL."/admin/listing.php?rd=activate&".$_SESSION['ADMIN_QUERY_STRING'];
                
            redirect($pag_url);//redirect to the admin listing
        } else
		  echo "<div id='sysmessage'>".T_("Your Ad was successfully activated")."</div>";
}

////////////////////////////////////////////////////////////
function spamPost($post_id,$post_password){//flag post as spam
	$ocdb=phpMyDB::GetInstance();
		
		$ocdb->update(TABLE_PREFIX."posts","isAvailable=2","idPost=$post_id and password='$post_password'");//set post as spam state 2
		deletePostImages($post_id);// delete the images cuz of spammer
		if (CACHE_DEL_ON_POST) deleteCache();//delete cache
		
		
        //show confirmation message or return to admin listing
        if (isset($_SESSION['admin']) && isset($_SESSION['ADMIN_QUERY_STRING'])){
            $pag_url=SITE_URL."/admin/listing.php?rd=spam&".$_SESSION['ADMIN_QUERY_STRING'];
                
            redirect($pag_url);//redirect to the admin listing
            die();
        } else
		  echo "<div id='sysmessage'>".T_("Spam reported")."</div>";
}

////////////////////////////////////////////////////////////
function deletePost($post_id,$post_password){//delete post
	$ocdb=phpMyDB::GetInstance();
	deletePostImages($post_id);//delete images! and folder
		$ocdb->delete(TABLE_PREFIX."posts","idPost=$post_id and password='$post_password'");
		if (CACHE_DEL_ON_POST) deleteCache();//delete cache

        
        //show confirmation message or return to admin listing
        if (isset($_SESSION['admin']) && isset($_SESSION['ADMIN_QUERY_STRING'])){
            $pag_url=SITE_URL."/admin/listing.php?rd=delete&".$_SESSION['ADMIN_QUERY_STRING'];
                
            redirect($pag_url);//redirect to the admin listing
        } else
		  echo "<div id='sysmessage'>".T_("Your Ad was successfully deleted")."</div>";
}

////////////////////////////////////////////////////////////
function newPost(){
	global $client_ip;
	$AdTypeID = 0;
	$AdActiveDays = 0;
	$AdCost = 0;
	$PostedDate = date("Y-m-d H:i:s");
	
	if(checkMathCaptcha('newitem'))	{
		if (isEmail(cP("email"))){//is email
			if(!isSpam(cP("name"),cP("email"),cP("description"))){//check if is spam!
				$ocdb=phpMyDB::GetInstance();
				$image_check=check_images_form();//echo $image_check;
				
				if (is_numeric($image_check)){//if the images were right, or not any image uploaded
				
					$price=cP("price");
					//DB insert
					$post_password=generatePassword();					
					if (HTML_EDITOR) $desc=cPR("description");
					else $desc=cP("description");
					if (VIDEO && cp("video")!="" && strpos(cp("video"), "http://www.youtube.com/watch?v=")==0) $desc.='[youtube='.cp("video").']';//youtube video
					$title=cP("title");
					$email=cP("email");
		                        if (cP("location")!="") $location = intval(cP("location"));
		                        else $location=0;
					
					
		                        if ($image_check>1) $hasImages=1;
		                        else $hasImages=0;
                        
					$ocdb->insert(TABLE_PREFIX."posts (idCategory,type,title,description,price,idLocation,place,name,email,phone,password,ip,hasImages)","".
							intval(cP("category")).",".intval(cP("type")).",'$title','$desc','$price',$location,'".cP("place")."','".cP("name")."','$email','".cP("phone")."','$post_password','$client_ip',$hasImages");
					$idPost=$ocdb->getLastID();
					
					for($k = 1; $k <= cP("CustomRows"); $k++) {
						$IdFieldArg			=	"CustomFieldId".$k;
						$IdField			=	cP($IdFieldArg);					
						
						$FieldDescriptionArg=	"CustomFieldDescription".$k;
						$FieldDescValue=	$_REQUEST[$FieldDescriptionArg];
						
						$FieldValueArg		=	"CustomField".$k;
						$FieldValues		=	$_REQUEST[$FieldValueArg];

						if(isset($FieldDescValue) && ($FieldDescValue == $FieldValues)) {
							$FieldValue = '';
						}
						else {
							$FieldValue			=	$FieldValues;
							if(is_array($FieldValue)) {
								$FieldValue		=	implode(',',$FieldValue);
							}
							else {
								$FieldValue;
							}
						}
						$ocdb->insert(TABLE_PREFIX."posts_ad_data	(idPost,idField,FieldValue,PostUpdatedDate)","'$idPost','$IdField','$FieldValue',NOW()");
						$idAdData=$ocdb->getLastID();
						
						if(($FieldValueArg == "CustomField2" && $FieldValue != '') || ($FieldValueArg == "CustomField3" && $FieldValue != '') || ($FieldValueArg == "CustomField4" && $FieldValue != '')) {
							if($FieldValueArg == "CustomField2") {
								$param = "name='$FieldValue',insertDate=NOW()";							
							}
							else if($FieldValueArg == "CustomField3") {
								$param = "title='$FieldValue',insertDate=NOW()";						
							}
							else if($FieldValueArg == "CustomField4") {
								$findString = stristr($FieldValue,"offer");
								if($findString) {
									$FieldValue = 0;
								}
								else { $FieldValue = 1; }
								$param = "type='$FieldValue',insertDate=NOW()";							
							}
							$ocdb->update(TABLE_PREFIX."posts",$param,"idPost = '$idPost' Limit 1");
						}
					}
					$AdType=cP("AdTypeGroup");
					if($AdType!="")
					{
						$arrAdType =  explode("|", $AdType);
						
						$AdTypeID = $arrAdType[0];
						$AdActiveDays = $arrAdType[1];
						$AdCost = $arrAdType[2];
						
						$idBatch = intval(cP("BatchGroup"));
						
             					$ocdb->insert(TABLE_PREFIX."premium_posts (idPost,idAdType,PostedDate,StartDate,EndDate,idBatch,ActiveDays,Payment, Paid)","".
							$idPost.",".intval($AdTypeID) .",'". $PostedDate ."','".date("Y-m-d") ."','". date("Y-m-d") ."',". $idBatch .",". intval($AdActiveDays) .",". intval($AdCost) .",'N' " );

					}
					//end database insert
					
					if ($image_check>1) upload_images_form($idPost,$title);
					
				  	if (PAYPAL_ACTIVE) paypalForm($idPost);//if paypal active redirect the user to paypal and die
                    		
					//EMAIL notify
					//generate the email to send to the client , we allow them to erase posts? mmmm
					if(FRIENDLY_URL) {
					    $linkConfirm=SITE_URL."/manage/?post=$idPost&pwd=$post_password&action=confirm";
					    $linkDeactivate=SITE_URL."/manage/?post=$idPost&pwd=$post_password&action=deactivate";
					    $linkEdit=SITE_URL."/manage/?post=$idPost&pwd=$post_password&action=edit";
					}
					else{
					    $linkConfirm=SITE_URL."/content/item-manage.php?post=$idPost&pwd=$post_password&action=confirm";
					    $linkDeactivate=SITE_URL."/content/item-manage.php?post=$idPost&pwd=$post_password&action=deactivate";
					    $linkEdit=SITE_URL."/content/item-manage.php?post=$idPost&pwd=$post_password&action=edit";
					}
					
                      
							
					if (!CONFIRM_POST){
		                $message="<p>".T_("If you want to edit your Ad click here").":
									<a href='$linkEdit'>$linkEdit</a><br />".
									T_("If this Ad is no longer available please click here").":
									<a href='$linkDeactivate'>$linkDeactivate</a></p>";
				    }
					else {
						$message="<p>".T_("To confirm users Ad click here").": ".
							"<a href='$linkConfirm'>$linkConfirm</a><br /><br />".
							T_("If you want to edit users Ad click here").":
							<a href='$linkEdit'>$linkEdit</a><br />".
							T_("If this Ad is no longer available please click here").":
							<a href='$linkDeactivate'>$linkDeactivate</a></p>";
	                }
                
                        
                        $array_content[]=array("ACCOUNT", T_("Admin"));
                        $array_content[]=array("MESSAGE", $message);
                        
                        $bodyHTML=buildEmailBodyHTML($array_content);
                        
                      
					if (!CONFIRM_POST){
						sendEmail($email,$title." ". $_SERVER['SERVER_NAME'],$bodyHTML);
					}
					else {
						_e("Thank you for your post! Postwala Admin team will review your post and confirm."); ?>
						<a class="okclass" href="<?php echo SITE_URL; ?>" ><img src="<?php echo SITE_URL; ?>/images/okclick.jpg" width="20" height="20">&nbsp;Click to Continue</a>
						<?php sendEmail(NOTIFY_EMAIL,T_("Confirm")." ".$title." ". $_SERVER['SERVER_NAME'],$bodyHTML);
					}
					
					if (!CONFIRM_POST) 
					        jsRedirect($linkConfirm);
					else 
					{
					  	if (ONLINEPAYMENT_ACTIVE && isset($_POST['featured_ad']) &&  $_POST['featured_ad'] == 'Yes') 
							directpayForm($idPost, $AdCost, $PostedDate); //if directpay active redirect the user to directpay and die
						require_once('../includes/footer.php');
					}
					
					die();
				}
				else echo "<div id='sysmessage'>".$image_check."</div>";//end upload verification
			}//end akismet
			else {//is spam!
				echo "<div id='sysmessage'>".T_("Oops! Spam? If it was not spam, contact us")."</div>";
				require_once('../includes/footer.php');
				exit;
			}
		}//email validation
		else echo "<div id='sysmessage'>".T_("Wrong email address")."</div>";//Wrong email address
	}//captcha validation
	else echo "<div id='sysmessage'>".T_("Wrong captcha")."</div>";//wrong captcha
}

function editPost($post_id,$post_password){
	global $client_ip;
	if(checkMathCaptcha('edititem'))	
	{   
	    //everything ok
	    $ocdb=phpMyDB::GetInstance();
	    $image_check=check_images_form();//echo $image_check;
	    if (is_numeric($image_check))
	    {
	           //if the images were right, or not any image uploaded
		    $price=cP("price");
		    //DB update				
	            if (HTML_EDITOR) 
		    {
		    	$desc=cPR("description"); 
			if ($_POST['video'])
			{
				$desc.='[youtube='.cPR("video").']';
		    	}
	    	    }
            	    else 
		    {
		            $desc=cP("description"); 
			    if ($_POST['video'])
			    {
			    	$desc.='[youtube='.cP("video").']';
		    	    }
		    }
               
		    if ($image_check>1) $hasImages= " ,hasImages=1";
		    
		    $title=cP("title");	
                    if (cP("location")!="") $location = cP("location");
                    else $location=0;
                    
                    $param = "isConfirmed=0,idCategory=".intval(cP("category")).",type=".intval(cP("type")).",
					    title='$title',description='$desc',price='$price',
					    place='".cP("place")."',name='".cP("name")."',
					    phone='".cP("phone")."',ip='$client_ip'".$hasImages.",Edited=1";
                    if (is_numeric(cP("location"))) $param .= ",idLocation=".intval(cP("location"));
		    $ocdb->update(TABLE_PREFIX."posts",$param,"idPost=$post_id and password='$post_password' Limit 1");
			
			for($k = 1; $k <= cP("CustomRows"); $k++) {
				$PostId				=	cP("PostId");
				$CustomPostIdArg	=	"CustomPostId".$k;
				$CustomPostId		=	cP($CustomPostIdArg);				
				$IdFieldArg			=	"CustomFieldId".$k;
				$IdField			=	cP($IdFieldArg);					
				$FieldValueArg		=	"CustomField".$k;
				$FieldValue			=	$_REQUEST[$FieldValueArg];
				if(is_array($FieldValue)) {
					$FieldValue		=	implode(',',$FieldValue);
				}
				else {
					$FieldValue;
				}
				
				$param =
					"idField='$IdField',FieldValue='$FieldValue',PostUpdatedDate=NOW()";
				$ocdb->update(TABLE_PREFIX."posts_ad_data",$param,"idAddData='$CustomPostId' and idPost = '$PostId' Limit 1");

				if(($FieldValueArg == "CustomField2" && $FieldValue != '') || ($FieldValueArg == "CustomField3" && $FieldValue != '') || ($FieldValueArg == "CustomField4" && $FieldValue != '')) {
					if($FieldValueArg == "CustomField2") {
						$param = "name='$FieldValue',insertDate=NOW()";							
					}
					else if($FieldValueArg == "CustomField3") {
						$param = "title='$FieldValue',insertDate=NOW()";						
					}
					else if($FieldValueArg == "CustomField4") {
						$findString = stristr($FieldValue,"offer");
						if($findString) {
							$FieldValue = 0;
						}
						else { $FieldValue = 1; }
						$param = "type='$FieldValue',insertDate=NOW()";							
					}
					$ocdb->update(TABLE_PREFIX."posts",$param,"idPost = '$PostId' Limit 1");
				}

			}


		    if (CACHE_DEL_ON_POST) deleteCache();//delete cache on post
		    //end database update
		
		    if ($image_check>1){//something to upload
			    $date=deletePostImages($post_id); //delete previous images
			    upload_images_form($post_id,$title,$date);//upload new ones
		    }
		    //end images	
	
                    //show confirmation message or return to admin listing
                    if (isset($_SESSION['admin']) && isset($_SESSION['ADMIN_QUERY_STRING'])){
                        $pag_url=SITE_URL."/admin/listing.php?rd=edit&".$_SESSION['ADMIN_QUERY_STRING'];
                            
                        redirect($pag_url);//redirect to the admin listing
                    } else
              echo "<div id='sysmessage'>".T_("Your Ad was successfully updated")."</div>";
		}//image check
	    else echo "<div id='sysmessage'>".$image_check."</div>";//end upload verification
	}//end captcha
    else echo "<div id='sysmessage'>".T_("Wrong captcha")."</div>";
}
?>