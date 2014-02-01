<?php
	require_once('../../includes/functions.php');
	require_once('../../includes/common.php');
	require_once('../../content/email/en_EN/template.html');
	require_once('../../includes/gettext/gettext.inc');
	require_once('../../includes/header.php');
	
	echo $idpost		=	cG("id");
	echo $replymob		=	cG("mob");
	echo $replyname		=	cG("name");
	echo $replyname		=	ucwords($replyname);
	$queryPost		=	"SELECT title,email,name,password FROM ".TABLE_PREFIX."posts WHERE idPost = '$idpost'";
	$resPost		=	$ocdb->query($queryPost);	
	$rowPost		=	mysql_fetch_assoc($resPost); 
	$PostTitle		=	$rowPost[title]; 
	$PostEmail		=	$rowPost[email];
	$PostName		=	$rowPost[name];
	$PostPass		=	$rowPost[password];

	//generate the email to send to the client that is contacted
	$subject=T_("Contact")." ".html_entity_decode($itemTitle, ENT_QUOTES, CHARSET).SEPARATOR. SITE_NAME;

	$imageURL		=	SITE_URL."/images/postwala-gif.gif";

	$PriceImage		=	SITE_URL."/images/Pricemask_promo.jpg";

	$PostwalaURL	=	SITE_URL;

	$PricemaskURL	=	PRICEMASK_URL;

	$LinkUrl		=	SITE_URL."/manage/?post=$idpost&pwd=$PostPass&action=edit";

	$BorderImage	=	SITE_URL."/images/Border2.jpg";

	$message="<p>".$replyname."&nbsp;(".cG("email").") ".T_("contacted you about the Ad") ."&nbsp;<a href='".$LinkUrl."'>".$PostTitle."</a><br /><br />".$replyname."&nbsp;".T_("responded that :") ."<br/>&nbsp;".
			 cG("msg");

			 if($replymob != '') { $message .= "<br /><br />".$replyname."'s mobile number is :<br/>&nbsp;".
			 cG("mob"); }

			 $message .= "<br /><br />".T_("Do not answer this email, answer to this email").": ".cG("email");

			 $message .= "<br /><br />Thank you for choosing Postwala Classifieds.</p>";

	$array_content[]=array("ACCOUNT", T_(ucwords($PostName)));
	$array_content[]=array("MESSAGE", $message);
	$array_content[]=array("IMAGEURL", $imageURL);
	$array_content[]=array("PRICEIMAGE", $PriceImage);
	$array_content[]=array("POSTURL", $PostwalaURL);
	$array_content[]=array("PRICEURL", $PricemaskURL);
	$array_content[]=array("BORDERIMAGE", $BorderImage);

	$bodyHTML=buildEmailBodyHTML($array_content);
	sendEmailComplete($PostEmail,$subject,$bodyHTML,cG("email"));
?>