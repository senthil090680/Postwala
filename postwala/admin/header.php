<?php
////////////////////////////////////////////////////////////
//Common header for admin
////////////////////////////////////////////////////////////
#============================================================================================================
# Author 		: J P Senthil Kumar
# Start Date	: 18 May 2011
# End Date		: 18 May 2011
# Project		: Admin Header
# Description	: This file is used to have the header of admin panel
#============================================================================================================

require_once('../includes/functions.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET;?>" />
	<title><?php _e("Administration").' | '.SITE_NAME;?></title>
	<meta name="generator" content="Open Classifieds <?php echo VERSION;?>" />
	<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL;?>/admin/style.css" media="screen" />
	<link rel="shortcut icon" href="<?php echo SITE_URL;?>/images/favicon.ico" />
	<script type="text/javascript" src="<?php echo SITE_URL;?>/includes/js/common.js"></script>	
</head>
<body>
<div id="container" <?php if (!isset($_SESSION['admin'])) echo 'class="login"';?>>
	<div id="header">
		<?php if (isset($_SESSION['admin'])){?>
        <span class="logout"><a href="logout.php"><?php _e("Logout");?></a></span>
        <?php }?>
		<h1><a href="<?php echo SITE_URL;?>"><?php echo SITE_NAME; ?></a> &raquo; <span><?php _e("Administration");?></span></h1>
		<?php if (isset($_SESSION['admin'])){?>
		<ul id="nav_main">
			<li><a href="index.php" title="<?php _e("Dashboard");?>" <?php if(strpos($_SERVER["REQUEST_URI"], "index.php") !== false){?>class="current"<?php }?>><?php _e("Dashboard");?></a></li>
			<li><a href="customfields.php" title="<?php _e("Manage Custom Fields by adding, editing and deleting");?>" <?php if(strpos($_SERVER["REQUEST_URI"], "customfields.php") !== false){?>class="current"<?php }?>><?php _e("Custom");?></a></li>
			<li><a href="mapcustocat.php" title="<?php _e("Mapping Custom Fields to Category");?>" <?php if(strpos($_SERVER["REQUEST_URI"], "mapcustocat.php") !== false){?>class="current"<?php }?>><?php _e("Map Cat");?></a></li>
			<li><a href="imageupload.php" title="<?php _e("Upload");?>" <?php if(strpos($_SERVER["REQUEST_URI"], "imageupload.php") !== false){?>class="current"<?php }?>><?php _e("Upload");?></a></li>
			<li><a href="menufinallink.php" title="<?php _e("Map Final Link");?>" <?php if(strpos($_SERVER["REQUEST_URI"], "menufinallink.php") !== false){?>class="current"<?php }?>><?php _e("Map Link");?></a></li>
			<li><a href="googleads.php" title="<?php _e("Google Ads");?>" <?php if(strpos($_SERVER["REQUEST_URI"], "googleads.php") !== false){?>class="current"<?php }?>><?php _e("Google Ads");?></a></li>
			<li><a href="searchengine.php" title="<?php _e("SEO");?>" <?php if(strpos($_SERVER["REQUEST_URI"], "searchengine.php") !== false){?>class="current"<?php }?>><?php _e("SEO");?></a></li>
			<li><a href="listing.php?reviewed=0" title="<?php _e("Approve Pending Ads");?>" <?php if(strpos($_SERVER["REQUEST_URI"], "listing.php?reviewed=0") !== false){?>class="current"<?php }?>><?php _e("Aprv Ads");?></a></li>
			<li><a href="listing.php" title="<?php _e("Listings");?>" <?php if(strpos($_SERVER["REQUEST_URI"], "listing.php") !== false){?>class="current"<?php }?>><?php _e("Listings");?></a></li>
			<li><a href="categories.php" title="<?php _e("Categories");?>" <?php if(strpos($_SERVER["REQUEST_URI"], "categories.php") !== false){?>class="current"<?php }?>><?php _e("Categories");?></a></li>
			<li><a href="locations.php" title="<?php _e("Locations");?>" <?php if(strpos($_SERVER["REQUEST_URI"], "locations.php") !== false){?>class="current"<?php }?>><?php _e("Locations");?></a></li>
			<li><a href="accounts.php" title="<?php _e("Accounts");?>" <?php if(strpos($_SERVER["REQUEST_URI"], "accounts.php") !== false){?>class="current"<?php }?>><?php _e("Accounts");?></a></li>
			<li><a href="settings.php" title="<?php _e("Settings");?>" <?php if(strpos($_SERVER["REQUEST_URI"], "settings.php") !== false){?>class="current"<?php }?>><?php _e("Settings");?></a></li>
			<li><a href="stats.php" title="<?php _e("Site Statistics");?>" <?php if(strpos($_SERVER["REQUEST_URI"], "stats.php") !== false){?>class="current"<?php }?>><?php _e("Statistics");?></a></li>
		</ul>
		<?php }?>
	</div>
	<div id="main">
		<div id="content">