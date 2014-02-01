<?php
#============================================================================================================
# Author 		: J P Senthil Kumar
# Start Date	: 16 June 2011
# End Date		: 
# Project		: Dynamic Premium Ads
# Description	: This file is used to bring the premium ads dynamically when choosing category
#============================================================================================================

require_once('../includes/functions.php');
if (file_exists(SITE_ROOT.'/themes/'.THEME.'/dynamiccustomfields.php')){//item-new from the theme!
	require_once(SITE_ROOT.'/themes/'.THEME.'/dynamiccustomfields.php');
}
else{//not found in theme
	$idCategory			=	cG("idCategory");
	$offerNeed			=	cG("type");
	$offerNeed = stristr(strtolower($LabelName),"offer");
	if($offerNeed) $type = 0;
	else $type = 1;
	if($idCategory != "") {
		if (PAYPAL_ACTIVE) echo T_('Price to post using Paypal: ').PAYPAL_AMOUNT.PAYPAL_CURRENCY.'<br />';
		if (ONLINEPAYMENT_ACTIVE)
		{ ?>
				<div class="green_box" style="background-color: rgb(250, 250, 215);font-weight: normal;">
			 
				<input type="checkbox" class="feature checkbox" name="featured_ad" id="featured_ad" value="Yes" onClick="featured_adOnClick()" style="width: 19px;"> 
			<?php
				_e("<b>Promote your ad</b> - Use these options below to drive increased responses to your ad (Recommended).  Read <a href=\"".SITE_URL."/content/refund-policy.php\" target=\"_blank\">Refund Policy</a> <br /> ");  
				echo selectAdType($idCategory, $type);
				echo selectAdBatch($idCategory);
			?>
			
			</div>
			<?php
		}
	}
}
?>