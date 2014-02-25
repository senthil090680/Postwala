<?php
////////////////////////////////////////////////////////////
//Common header for all the themes
////////////////////////////////////////////////////////////
require_once('functions.php');
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo substr(LANGUAGE,0,2);?>" lang="<?php echo substr(LANGUAGE,0,2);?>">
<head>
		<?php if(is_numeric($location)) $locationSeo = getLocationFriendlyName($location);
				else $locationSeo = $location;

		if($locationSeo != '') {		
			if($locationSeo == 'chennai' || $locationSeo == 'andhra-pradesh' || $locationSeo == 'new-delhi' || $locationSeo == 'mumbai' || $locationSeo == 'bangalore') {
				$seoArray	=	getSeo(2);
				$seoTitle	=	$seoArray[0];
				$seoKey		=	$seoArray[1];
				$seoDesc	=	$seoArray[2];
			}
			elseif($locationSeo == 'goa' || $locationSeo == 'bihar' || $locationSeo == 'assam' || $locationSeo == 'gujarat' || $locationSeo == 'orissa') {
				$seoArray	=	getSeo(3);
				$seoTitle	=	$seoArray[0];
				$seoKey		=	$seoArray[1];
				$seoDesc	=	$seoArray[2];	
			}
			else {
				$seoArray	=	getSeo(4);
				$seoTitle	=	$seoArray[0];
				$seoKey		=	$seoArray[1];
				$seoDesc	=	$seoArray[2];
			}

		}
		else if($locationSeo == '') {
			$seoArray	=	getSeo(1);
			$seoTitle	=	$seoArray[0];
			$seoKey		=	$seoArray[1];
			$seoDesc	=	$seoArray[2];
		}
		?>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET;?>" />
		<title><?php echo $seoTitle;?></title>
		<meta name="title" content="<?php echo $seoTitle;?>" />
		<meta name="description" content="<?php echo $seoKey;?>" />
		<meta name="keywords" content="<?php echo $seoDesc;?>" />		
		<meta name="generator" content="Postwala Classifieds <?php echo VERSION;?>" />
		<!--<meta name="title" content="<?php echo $html_title;?>" />
		<meta name="description" content="<?php echo $html_description;?>" />
		<meta name="keywords" content="<?php echo $html_keywords;?>" />		
		<meta name="generator" content="Postwala Classifieds <?php echo VERSION;?>" />-->
		<link rel="shortcut icon" href="<?php echo SITE_URL;?>/images/favicon.ico" />
	<?php if (isset($currentCategory) || isset($type) || isset($location) ){?>
		<link rel="alternate" type="application/rss+xml" title="<?php _e("Latest Ads");?> 
		<?php echo ucwords($currentCategory);?> <?php echo ucwords(getTypeName($type));?> <?php echo getLocationName($location);?>"
		href="<?php echo rssURL().'?category='.$currentCategory.'&amp;type='.$type.'&amp;location='.$location;?>" />
	<?php }?>
		<link rel="alternate" type="application/rss+xml" title="<?php _e("Latest Ads");?>" href="<?php echo SITE_URL;?>/rss/" />
		<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL;?>/themes/<?php echo THEME;?>/style.css" media="screen" />
		<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL;?>/themes/wordcloud.css" media="screen" />
	<?php if (isset($idItem)) {//only in the item the greybox?>
		<script type="text/javascript">var GB_ROOT_DIR = "<?php echo SITE_URL;?>/includes/greybox/";</script>
		<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL;?>/includes/greybox/gb_styles.css" media="screen" />
	<?php }?>
		<script type="text/javascript" src="<?php echo SITE_URL;?>/includes/js/common.js"></script>
	<?php if (ANALYTICS!=""){?>
        <script type="text/javascript">
          var _gaq = _gaq || [];
          _gaq.push(['_setAccount', '<?php echo ANALYTICS;?>']);
          _gaq.push(['_trackPageview']);
          (function() {
            var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
          })();
        </script>
    <?php }?>
	<script src="<?php echo SITE_URL; ?>/themes/<?php echo THEME; ?>/js/divpop.js" type="text/javascript"></script>
</head>
<?php if(!is_numeric($category) && cG("nofilter") == 1) { $catValueOnLoad = cG("category"); $query="select idCategory from ".TABLE_PREFIX."categories where friendlyName='$catValueOnLoad' Limit 1";
			$idCategory = $ocdb->getValue($query); $path = SITE_URL."/content/"; ?>
<body onload="getCustomFieldsOnLoad('<?php echo $idCategory; ?>','<?php echo $path; ?>')"> <?php } else { ?>
<body <?php if((isset($_POST["publishdate"]) && cP("publishdate") != '') || $idCategory !='') { ?> onload="loadcustomfields('<?php echo $idCategory; ?>','<?php echo SITE_URL; ?>/themes/<?php echo THEME; ?>/','','<?php echo cP("publishdate"); ?>','<?php echo $customFields; ?>','<?php echo cP("publishdate"); ?>','<?php echo cP("advancedlocation"); ?>')" <?php } ?> > <?php } ?>
<?php require_once(SITE_ROOT.'/themes/'.THEME.'/header.php');?>
<!--googleoff: index-->
<noscript>
	<div style="height:30px;border:3px solid #6699ff;text-align:center;font-weight: bold;padding-top:10px">
		Your browser does not support JavaScript!
	</div>
</noscript>
<!--googleon: index-->

<!--
<div style="margin-left: auto; margin-right: auto; width: 468px;">
<?php if (ADVERT_TOP!='') 
{
        echo ADVERT_TOP;

}?>
</div>
-->