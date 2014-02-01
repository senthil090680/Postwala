<?php
////////////////////////////////////////////////////////////
//Common Functions
////////////////////////////////////////////////////////////
function curPageURL() {
	$pageURL = 'http';
	if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
		$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	return $pageURL;
}


function clean($var){//request string cleaner

	$san = new HTML_Sanitizer;
	$san->allowStyle();
	return $san->sanitize( $var )  ;

	//if(get_magic_quotes_gpc()) $var = stripslashes($var); //clean
	//$var = mysql_real_escape_string($var); //clean
	//return strip_tags($var, ALLOWED_HTML_TAGS);//returning clean var
}
////////////////////////////////////////////////////////////
function cG($name){//clean Get, to prevent mysql injection Get method
	return clean($_GET[$name]);
}
////////////////////////////////////////////////////////////
function cP($name){//clean post, to prevent mysql injection Post method
	return clean($_POST[$name]);//posts are already clean
}
////////////////////////////////////////////////////////////
function cPR($name){//clean post, to prevent mysql injection Post method, and convert some variables
	return ToHtml(clean($_POST[$name]));
}
////////////////////////////////////////////////////////////
function hackerDefense(){//thanks to Allen Sanford
    if (!isset($_SESSION['admin']) ) {//only for users the admin can do whatever he wants
        // begin hacker defense       
        foreach ($_POST as &$postvalue){    //checking posts
            $postvalue = clean($postvalue);//cleaning the value
        }
    }
} // end hacker defense
////////////////////////////////////////////////////////////
function ToHtml($string){//replaces for special things
	$string = str_replace ("<br>","<br />", $string);//problem with new lines
	$string = str_replace ("&nbsp;"," ", $string);//problem with spaces
	$string = str_replace ("href=","rel=\"nofollow\" href=", $string);	//nofollow
	$string = str_replace ("\\","\\\\", $string);	//to avoid backslash
	$string = str_replace ("'","''", $string);	//to avoid quotes
	return $string;
}
////////////////////////////////////////////////////////////
function friendly_url($url){//post slug
	$url= mb_strtolower(replace_accents($url), CHARSET);
	return preg_replace(array('/[^a-zA-Z0-9 -]/', '/[ -]+/', '/^-|-$/'), array('','-',''),$url);
}
////////////////////////////////////////////////////////////
function remove_querystring_var($key) {
    $arrquery = explode("&", $_SERVER["QUERY_STRING"]);
    
    foreach ($arrquery as $query_value) {
        $valor = substr($query_value, strpos($query_value, "=") + 1);
        $chave = substr($query_value, 0, strpos($query_value, "="));
        $querystring[$chave] = $valor;
    }
    
    unset($querystring[$key]);
    
    foreach ($querystring as $query_key => $query_value) {
        $query[] = "{$query_key}={$query_value}";
    }

    $query = implode("&", $query);

    return $query;
}
////////////////////////////////////////////////////////////
function u($word){//returns the firendly word with html parsed
	return friendly_url(html_entity_decode($word,ENT_QUOTES,CHARSET));
}
////////////////////////////////////////////////////////////
function replace_accents($var){ //replace for accents catalan spanish and more
    $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');
    $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
    $var= str_replace($a, $b,$var);
    return $var;
}
////////////////////////////////////////////////////////////
function sqlOption($sql,$name,$option){//generates a select tag with the values specified on the sql, 2nd parameter name for the combo, , 3rd value selected if there's
	//$ocdb=phpMyDB::GetInstance();
	global $ocdb;
	$result =$ocdb->query($sql);//1 value needs to be the ID, second the Name, if there's more doens't work
	$sqloption= "<select name='".$name."' id='".$name."'>
				<option value='0'>Select</option>";
	while ($row=mysql_fetch_assoc($result)){
		$first=mysql_field_name($result, 0);
		$second=mysql_field_name($result, 1);

			if ($option==$row[$first]) { $sel="selected=selected";}
			$sqloption=$sqloption .  "<option ".$sel." value='".$row[$first]."'>" .ucwords($row[$second]). "</option>";
			$sel="";
	}
		$sqloption=$sqloption . "</select>";
		echo $sqloption;
}
////////////////////////////////////////////////////////////
function sqlOptionTwoValues($sql,$name,$option){//generates a select tag with the values specified on the sql, 2nd parameter name for the combo, , 3rd value selected if there's
	//$ocdb=phpMyDB::GetInstance();
	global $ocdb;
	$result =$ocdb->query($sql);//1 value needs to be the ID, second the Name, if there's more doens't work
	$sqloption= "<select name='".$name."' id='".$name."'>
				<option value='0'>Select</option>";
	while ($row=mysql_fetch_assoc($result)){
		$first=mysql_field_name($result, 0);
		$second=mysql_field_name($result, 1);
		$third=mysql_field_name($result, 2);

			if ($option==$row[$first]) { $sel="selected=selected";}
			$sqloption=$sqloption .  "<option ".$sel." value='".$row[$first]."'>" .$row[$third]. "&nbsp;>&nbsp;" .$row[$second]. "</option>";
			$sel="";
	}
		$sqloption=$sqloption . "</select>";
		echo $sqloption;
}
////////////////////////////////////////////////////////////
function getGoogleAds($banId){//gets the google ads dynamically
	//$ocdb=phpMyDB::GetInstance();
	global $ocdb;

	$queryGoogle="SELECT bannerCode FROM classifieds_banners where bannerId = $banId order by bannerId asc limit 1 ";
	$res												=	$ocdb->query($queryGoogle);	
	while($rowGoogle = mysql_fetch_assoc($res)) 
		$GoogleCode										=	$rowGoogle[bannerCode]; // bannerCode OF 
		$GoogleCode										=	str_replace("bancode","<",$GoogleCode);
		$GoogleCode										=	str_replace("rightone",">",$GoogleCode);		
		return $GoogleCode; // bannerCode OF THE GOOGLE AD
}
////////////////////////////////////////////////////////////
function getSeo($seoId){//gets the seo dynamically
	//$ocdb=phpMyDB::GetInstance();
	global $ocdb;

	$querySeo="SELECT seoTitle,seoKeyword,seoDescription FROM ".TABLE_PREFIX."seo where idSeo = $seoId order by idSeo asc limit 1 ";
	$res												=	$ocdb->query($querySeo);	
	while($rowSeo = mysql_fetch_assoc($res)) {
		$seoCode[0]										=	$rowSeo[seoTitle]; // seo title
		$seoCode[1]										=	$rowSeo[seoKeyword]; // seo Keyword
		$seoCode[2]										=	$rowSeo[seoDescription]; // seo Description
	}
		return $seoCode; // seo title, keyword,description
}
////////////////////////////////////////////////////////////

function unhtmlentities($string)
			{
				// replace numeric entities
				$string = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $string);
				$string = preg_replace('~&#([0-9]+);~e', 'chr(\\1)', $string);
				// replace literal entities
				$trans_tbl = get_html_translation_table(HTML_ENTITIES);
				$trans_tbl = array_flip($trans_tbl);
				return strtr($string, $trans_tbl);
			}
			////////////////////////////////////////////////////////////
//returns an array with the TWO SQL values
function sqlTwoValues($sql){//generates a select tag with the values specified on the sql, 2nd parameter name for the combo, , 3rd value selected if there's
	//$ocdb=phpMyDB::GetInstance();
	global $ocdb;
	$result =$ocdb->query($sql); //1 value needs to be the ID, second the Name, if there's more doens't work
	$ArrayValue = array();
	while ($row=mysql_fetch_row($result)){
		$ArrayValue[0] = $row[0];
		$ArrayValue[1] = $row[1];
	}
	return $ArrayValue;
}
////////////////////////////////////////////////////////////
function selectAdType($idCategory, $type){
	//$ocdb=phpMyDB::GetInstance();
	global $ocdb;

	$sql = "SELECT AD.idAdType, AD.Name , AD.ActiveDays, 
	        CAST(CASE WHEN AD.ActiveDays = 15 
		          THEN (AD.Discount) 
		          ELSE (AD.Discount)  
		     END AS Decimal(10,2)) AS Cost FROM `classifieds_premium_adtypes` AD,  `classifieds_categories` CAT 
                WHERE AD.Active = 'Y'
                AND idCategory = (SELECT idCategoryParent FROM classifieds_categories WHERE idCategory =  " . $idCategory . " ) " ;
		 
	$result =$ocdb->query($sql);
	
	$sqloption = "<br/><b>Select your premium ad option:</b> <br>";
	
	while ($row=mysql_fetch_assoc($result)){
	    
	    $sqloption .= "<input style=\"width: 13px;\" type=\"radio\" name=\"AdTypeGroup\" value=\"". $row["idAdType"] ."|". $row["ActiveDays"] ."|". number_format($row["Cost"], PAYMENT_DECIMALS, '.', '') ."\" id=\"".$row["idAdType"] . "\"  disabled/> ";
	    $sqloption .=  $row["Name"] . " - just for " . CURRENCY . number_format($row["Cost"], PAYMENT_DECIMALS, '.', '');
	    if($row["Discount"] > 0)
	      $sqloption .=  "  (" . $row["Discount"] . "% Discount)";
	    
	    $sqloption .=  "<br />" ;
	    
	}
	return $sqloption;
}
////////////////////////////////////////////////////////////
function selectAdBatch(){
	//$ocdb=phpMyDB::GetInstance();
	global $ocdb;
	$sql = "SELECT idBatch, BatchDesc ,  ImageName, ToolTip FROM `classifieds_premium_adbatches`  " ; 

	$result =$ocdb->query($sql);
	$i=1;
	$sqloption = "<br/><b>Highlight your ad with a tag:</b> <br>";
	while ($row=mysql_fetch_assoc($result)){
	    
	    $sqloption .= "<input style=\"width: 13px;\" type=\"radio\" name=\"BatchGroup\" value=\"". $row["idBatch"] ."\" id=\"BatchGroup_".$row["idBatch"] . "\" disabled /> ";
	    $sqloption .=  "<image src=\"". SITE_URL. "/images/premium-ads/" . $row["ImageName"] . "\" height=\"75px\" width=\"75px\"  /> " ;
	    if($i%8 == 0) $sqloption .=  "<br />" ; 
	    
	    $i = $i+1;
	}
	$sqloption .=  "<br />" ;
	
	return $sqloption;
}
////////////////////////////////////////////////////////////
function sqlOptionGroup($sql,$name,$option,$category=""){//generates a select tag with the values specified on the sql, 2nd parameter name for the combo, , 3rd value selected if there's
	//$ocdb=phpMyDB::GetInstance();
	global $ocdb;
	$result =$ocdb->query($sql);//1 value needs to be the ID, second the Name, 3rd is the group
	//echo $sql;

	if($category != "") {
		$path = SITE_URL."/content/";
		$selectOnChange = "onChange=\"getCustomFields(this,'$path');\"";
	}
	else {
		$selectOnChange = "onChange=validateAdNumber(this);";
	}
	$sqloption= "<select name='".$name."' class='borderedge' id='".$name."' onBlur=\"validateSelect(this); validateAdText(this);\"  $selectOnChange lang=false ><option></option>";
	$lastLabel = "";
	while ($row=mysql_fetch_assoc($result)){
		$first=mysql_field_name($result, 0);
		$second=mysql_field_name($result, 1);
		$third= mysql_field_name($result,2);

		if($lastLabel!=$row[$third]){
			if($lastLabel!=""){
				$sqloption.="</optgroup>";
			}
			$sqloption.="<optgroup label='$row[$third]'>";
			$lastLabel = $row[$third];
		}

			if ($option==$row[$first]) { $sel="selected=selected";}
			$sqloption=$sqloption .  "<option ".$sel." value='".$row[$first]."'>" .$row[$second]. "</option>";
			$sel="";
	}
		$sqloption.="</optgroup>";
		$sqloption=$sqloption . "</select>";
		echo $sqloption;
}

////////////////////////////////////////////////////////////
function sqlOptionGroupSearch($sql,$name,$option,$category="",$style=""){//generates a select tag with the values specified on the sql, 2nd parameter name for the combo, , 3rd value selected if there's
	//$ocdb=phpMyDB::GetInstance();
	global $ocdb;
	$result =$ocdb->query($sql);//1 value needs to be the ID, second the Name, 3rd is the group
	//echo $sql;

	if($category != "") {
		$path = SITE_URL."/content/";
		$selectOnChange = "onChange=\"getCustomFields(this,'$path'); getPremiumAds(this,'$path');\"";
	}
	else {
		$selectOnChange = "onChange=validateAdNumber(this);";
	}
	if($style == 1) { $widthstyle = 'heightstyle widthstyle'; $customlocation = "locationSelect(this);"; }
	else { $widthstyle = 'searchselect widthstyle'; $customlocation = "validateSelect(this);"; }
	$sqloption= "<select name='".$name."' class=\"borderedge ".$widthstyle."\" id='".$name."' onBlur=\"validateAdText(this); ".$customlocation."\"  $selectOnChange lang=false ><option></option>";
	$lastLabel = "";
	while ($row=mysql_fetch_assoc($result)){
		$first=mysql_field_name($result, 0);
		$second=mysql_field_name($result, 1);
		$third= mysql_field_name($result,2);

		if($lastLabel!=$row[$third]){
			if($lastLabel!=""){
				$sqloption.="</optgroup>";
			}
			$sqloption.="<optgroup label='$row[$third]'>";
			$lastLabel = $row[$third];
		}

			if ($option==$row[$first]) { $sel="selected=selected";}
			$sqloption=$sqloption .  "<option ".$sel." value='".$row[$first]."'>" .$row[$second]. "</option>";
			$sel="";
	}
		$sqloption.="</optgroup>";
		$sqloption=$sqloption . "</select>";
		echo $sqloption;
}
////////////////////////////////////////////////////////////
function generatePassword ($length = PASSWORD_SIZE){
	  // start with a blank password
	  $password = "";
	  // define possible characters
	  $possible = "0123456789abcdefghijklmnopqrstuvwxyz";
	  // set up a counter
	  $i = 0;
	  // add random characters to $password until $length is reached
	  while ($i < $length) {
		// pick a random character from the possible ones
		$char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
		// we do not want this character if it's already in the password
		if (!strstr($password, $char)) {
		  $password .= $char;
		  $i++;
		}
	  }
	  // done!
	  return $password;
}
////////////////////////////////////////////////////////////
function setDate($L_date,$L_dateFormat=DATE_FORMAT){//sets a date in a format
	if(strlen($L_date)>0){
		$L_arrTemp = explode(" ",$L_date);
		$L_strDate = $L_arrTemp[0]; // 2007-07-21 year month day
		$L_arrDate = explode("-",$L_strDate);// split date
		$L_strYear =  $L_arrDate[0];
		$L_strMonth = $L_arrDate[1];
		$L_strDay = $L_arrDate[2];

		if($L_dateFormat == 'yyyy-mm-dd'){//default
		    return $L_arrTemp[0];
        }
		elseif($L_dateFormat == "dd-mm-yyyy"){//day month year
			$returnDate = $L_strDay."-".$L_strMonth."-".$L_strYear;
			return $returnDate;
		}
		elseif($L_dateFormat == "mm-dd-yyyy"){//month day year
			$returnDate = $L_strMonth."-".$L_strDay."-".$L_strYear;
			return $returnDate;
		}
	}
	else return false;
}
////////////////////////////////////////////////////////////
function getTypeName($type){//get the type name
	if ($type==TYPE_OFFER) $type=T_("offer");
	else $type=T_("need");

	return $type;
}
////////////////////////////////////////////////////////////
function getTypeNum($type){//get the type in number
	if ($type==T_("offer")){
		$type=TYPE_OFFER;
	}
	else $type=TYPE_NEED;

	return $type;
}
////////////////////////////////////////////////////////////
function getLocationName($location){//get the location name
    if (isset($location)&&is_numeric($location)) {
       //$ocdb=phpMyDB::GetInstance();
		global $ocdb;
        $query="select name from ".TABLE_PREFIX."locations where idLocation=$location Limit 1";
		return $ocdb->getValue($query);
	} else return "";//nothing returned for that item
}
////////////////////////////////////////////////////////////
function getLocationFriendlyName($location){//get the location name
    if (isset($location)&&is_numeric($location)) {
        //$ocdb=phpMyDB::GetInstance();
		global $ocdb;
        $query="select friendlyName from ".TABLE_PREFIX."locations where idLocation=$location Limit 1";
		return $ocdb->getValue($query);
	} else return "";//nothing returned for that item
}
////////////////////////////////////////////////////////////
function getLocationParent($location){//get the location name
    if (isset($location)&&is_numeric($location)) {
        //$ocdb=phpMyDB::GetInstance();
		global $ocdb;
        $query="select idLocationParent from ".TABLE_PREFIX."locations where idLocation=$location Limit 1";
		$result=$ocdb->getValue($query);
		if (is_numeric($result)) return $result;
		else return 0;//nothing returned for that item
	} else return 0;//nothing returned for that item
}
////////////////////////////////////////////////////////////
function getLocationParentName($location){//get the parent location name
    if (isset($location)&&is_numeric($location)) {
        //$ocdb=phpMyDB::GetInstance();
		global $ocdb;
        $query="select name from ".TABLE_PREFIX."locations where idLocation = (select idLocationParent from ".TABLE_PREFIX."locations where idLocation=$location ) Limit 1";
		$result=$ocdb->getValue($query);
		return $result;
	} else return "";
}
////////////////////////////////////////////////////////////
function getLocationNum($location){//get the location in number
    if (isset($location)) {
        //$ocdb=phpMyDB::GetInstance();
		global $ocdb;
        $query="select idLocation from ".TABLE_PREFIX."locations where lower(friendlyName)='".friendly_url($location)."' Limit 1";
		$result=$ocdb->getValue($query);
		if (is_numeric($result)) return $result;
		else return 0;//nothing returned for that item
	} else return 0;//nothing returned for that item
}
////////////////////////////////////////////////////////////
function buildEmailBodyHTML($var_array){
    $filename = SITE_ROOT.'/content/email/'.LANGUAGE.'/template.html';
    if (!file_exists($filename))
        $filename = SITE_ROOT.'/content/email/en_EN/template.html';

    $fd = fopen ($filename, "r");
    $mailcontent = fread ($fd, filesize ($filename));

    foreach ($var_array as $key=>$value)
    {
    $mailcontent = str_replace("%$value[0]%", $value[1],$mailcontent);
    }

    $array_content[]=array("DATE", Date("l F d, Y"));
    $array_content[]=array("SITE_NAME", SITE_NAME);
    $array_content[]=array("SITE_URL", SITE_URL);

    foreach ($array_content as $key=>$value)
    {
    $mailcontent = str_replace("%$value[0]%", $value[1],$mailcontent);
    }

    $mailcontent = stripslashes($mailcontent);

    fclose ($fd);

    return $mailcontent;
}
////////////////////////////////////////////////////////////
function sendEmail($to,$subject,$body){//send email using smtp from gmail
	sendEmailComplete($to,$subject,$body,NOTIFY_EMAIL,SITE_NAME);
}
////////////////////////////////////////////////////////////
function sendEmailComplete($to,$subject,$body,$reply,$replyName){//send email using smtp from gmail
	$mail             = new PHPMailer();
	$mail->IsSMTP();

    //SMTP HOST config
	if (SMTP_HOST!=""){
		$mail->Host       = SMTP_HOST;              // sets custom SMTP server
    }

    //SMTP PORT config
	if (SMTP_PORT!=""){
		$mail->Port       = SMTP_PORT;              // set a custom SMTP port
    }

	//SMTP AUTH config
	if (SMTP_AUTH==true){
		$mail->SMTPAuth   = true;                   // enable SMTP authentication
		$mail->Username   = SMTP_USER;              // SMTP username
		$mail->Password   = SMTP_PASS;              // SMTP password
    }

	//GMAIL config
	if (GMAIL==true){
		$mail->SMTPAuth   = true;                   // enable SMTP authentication
		$mail->SMTPSecure = "ssl";                  // sets the prefix to the server
		$mail->Host       = "smtp.gmail.com";       // sets GMAIL as the SMTP server
		$mail->Port       = 465;                    // set the SMTP port for the GMAIL server
		$mail->Username   = GMAIL_USER;                     // GMAIL username
		$mail->Password   = GMAIL_PASS;                     // GMAIL password
    }

	$mail->From       = NOTIFY_EMAIL;
	$mail->FromName   = "no-reply ".SITE_NAME;
	$mail->Subject    = $subject;
	$mail->MsgHTML($body);

	$mail->AddReplyTo($reply,$replyName);//they answer here
	$mail->AddAddress($to,$to);
	$mail->IsHTML(true); // send as HTML

	if(!$mail->Send()) {//to see if we return a message or a value bolean
	  echo "Mailer Error: " . $mail->ErrorInfo;
	} else return false;
	 // echo "Message sent! $to";
}
////////////////////////////////////////////////////////////
function encode_str ($input){//converts the input into Ascii HTML, to ofuscate a bit
    for ($i = 0; $i < strlen($input); $i++) {
         $output .= "&#".ord($input[$i]).';';
    }
    //$output = htmlspecialchars($output);//uncomment to escape sepecial chars
    return $output;
}
////////////////////////////////////////////////////////////
function mathCaptcha($extra=''){//generates a captcha for the form
	$first_number=mt_rand(1, 94);//first operation number
	$second_number=mt_rand(1, 5);//second operation number

	$_SESSION["mathCaptcha".$extra]=($first_number+$second_number);//operation result

	$operation=" <b>".encode_str($first_number ." + ". $second_number)."</b>?";//operation codifieds

	echo T_("How much is")." ".$operation;
}
////////////////////////////////////////////////////////////
function checkMathCaptcha($extra=''){//checks if the captcha is correct
	if (CAPTCHA){//captcha enabled
		if (cP("math")==$_SESSION["mathCaptcha".$extra]){
			unsetMathCaptcha($extra);//prevents f5
			return true;
		}
		else return false;
	}
	else return true;//captcha is disabled always returns true
}
////////////////////////////////////////////////////////////
function unsetMathCaptcha($extra=''){//checks if the captcha is correct
	$_SESSION["mathCaptcha".$extra]='';
}
////////////////////////////////////////////////////////////
function isEmail($email){//check that the email is correct
	$pattern="/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/";
	return (preg_match($pattern, $email) > 0);
}

////////////////////////////////////////////////////////////
function redirect($url){//simple header redirect
	header('Location: '.$url);//redirect header
	die();
}
////////////////////////////////////////////////////////////
function jsRedirect($url){//simple JavaScript redirect
	echo "<script language='JavaScript' type='text/javascript'>location.href='$url';</script>";
	die();
}
////////////////////////////////////////////////////////////
function alert($msg){//simple JavaScript alert
	echo "<script language='JavaScript' type='text/javascript'>alert('$msg');</script>";
}
////////////////////////////////////////////////////////////
function removeRessource($_target) {//remove the done file
    //file?
    if( is_file($_target) ) {
        if( is_writable($_target) ) {
            if( @unlink($_target) ) {
                return true;
            }
        }
        return false;
    }
    //dir recursive
    if( is_dir($_target) ) {
        if( is_writeable($_target) ) {
            foreach( new DirectoryIterator($_target) as $_res ) {
                if( $_res->isDot() ) {
                    unset($_res);
                    continue;
                }
                if( $_res->isFile() ) {
                    removeRessource( $_res->getPathName() );
                } elseif( $_res->isDir() ) {
                    removeRessource( $_res->getRealPath() );
                }
                unset($_res);
            }
            if( @rmdir($_target) ) {
                return true;
            }
        }
        return false;
    }
}

////////////////////////////////////////////////////////////
function deleteCache(){//delete cache
	$cache = wrapperCache::GetInstance();
	$cache->clearCache();
	unset ($cache);
}
////////////////////////////////////////////////////////////
function rssReader($url,$maxItems=15,$cache,$begin="",$end=""){//read RSS from the url and cache it
    $cache= (bool) $cache;
    if ($cache){
        $cacheRSS= wrapperCache::GetInstance();//seconds and path
        $out = $cacheRSS->$url;//getting values from cache
        if (!isset($out)) $out=false;
    }else $out=false;

    if (!$out) {	//no values in cache
        $rss = simplexml_load_file($url);
        $i=0;
        if($rss){
            $items = $rss->channel->item;
            foreach($items as $item){
                if($i==$maxItems){
                    if ($cache) $cacheRSS->cache($url,$out);//save cache
                    return $out;
                }
                else $out.=$begin.'<a href="'.$item->link.'" target="_blank" >'.$item->title.'</a>'.$end;
                $i++;
            }//for each
        }//if rss
    }
    return $out;
}
////////////////////////////////////////////////////////////
function standarizeDate($date) {
	
	if (DATE_FORMAT=='dd-mm-yyyy'){//normal date
	    return $date;
	}
	elseif (DATE_FORMAT=='yyyy-mm-dd'){
	    $L_arrDate = explode("-",$date);// split date
		$L_strYear =  $L_arrDate[0];
		$L_strMonth = $L_arrDate[1];
		$L_strDay = $L_arrDate[2];
		$date=$L_arrDate[2].'-'.$L_arrDate[1].'-'.$L_arrDate[0];
	}
	elseif (DATE_FORMAT=='mm-dd-yyyy'){
	    $L_arrDate = explode("-",$date);// split date
		$L_strMonth = $L_arrDate[0];
		$L_strDay = $L_arrDate[1];
		$L_strYear =  $L_arrDate[2];
		$date=$L_arrDate[1].'-'.$L_arrDate[0].'-'.$L_arrDate[2];
	}
	//else//not any known format TODO
	 
	return $date;
	
}

////////////////////////////////////////////////////////////
// from MySQL to UNIX timestamp
function convert_datetime($str) {
	list($date, $time) = explode(' ', $str);
	list($year, $month, $day) = explode('-', $date);
	list($hour, $minute, $second) = explode(':', $time);
	
	$timestamp = mktime($hour, $minute, $second, $month, $day, $year);
	
	return $timestamp;
}

////////////////////////////////////////////////////////////
function _e($s) {//echo for the translation
	echo T_($s);
}
///////////////////functions used for the spam by country/////////////////////////////////////////
function get_tag($tag,$xml){
	preg_match_all('/<'.$tag.'>(.*)<\/'.$tag.'>$/imU',$xml,$match);
	return $match[1];
}
 
function valid_ip($ip){
	return ( ! preg_match( "/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/", $ip)) ? FALSE : TRUE;
}
///return IP using hostip.info service 
function geoIP($stringIp=-1){
 
    if (!valid_ip($stringIp)) $stringIp = $_SERVER['REMOTE_ADDR'];
 
    if ($_COOKIE['geoip']){
        $geoip=unserialize($_COOKIE['geoip']);        
        if ($geoip['ip']==$stringIp) return $geoip;//only return if IP is the same if not continue
	}
 
	$url='http://api.hostip.info/?ip='.$stringIp;// Making an API call to Hostip:
	$xml = file_get_contents($url);//echo $url;
 
	$city = get_tag('gml:name',$xml);
	$city = strtolower ($city[1]);
 
	$countryName = get_tag('countryName',$xml);
	$countryName = strtolower ($countryName[0]);
 
	$countryAbbrev = get_tag('countryAbbrev',$xml);
	$countryAbbrev = strtolower ($countryAbbrev[0]);
 
	$geoip['ip']=$stringIp;
	$geoip['city']=$city;
	$geoip['country']=$countryName;
	$geoip['countryAb']=$countryAbbrev;
 
	setcookie('geoip',serialize($geoip), time()+60*60*24*15);// Setting a cookie with the data, which is set to expire in half month:
	return $geoip;
}
?>
