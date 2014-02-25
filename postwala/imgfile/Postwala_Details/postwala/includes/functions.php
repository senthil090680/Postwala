<?php
ob_start();
session_start();
////////////////////////////////////////////////////////////
//Functions: call to all the includes
////////////////////////////////////////////////////////////

//Initial defines
define('VERSION','1.7.1');
define('DEBUG',false); //if you change this to true, returns error in the page instead of email, also enables debug from phpMyDB and disables disqus

if (!DEBUG){//do not display any error message and expire Headers for 24hours
    error_reporting(0);
    ini_set('display_errors','off');
}
else{//displays error messages 
	error_reporting(E_ALL | E_STRICT);
	ini_set('display_errors','on');
}

//config includes
//include('licence.php');//configuration file

require_once('config.php');//configuration file

require_once('config2.php');//configuration file

require_once('classes/phpMyDB.php');//configuration file

require_once('classes/wrapperCache.php');//configuration file

require_once('classes/class.phpmailer.php');//configuration file

require_once('classes/class.smtp.php');//configuration file

require_once('gettext/gettext.inc');//configuration file

require_once('common.php');//configuration file

require_once('item-common.php');//configuration file

require_once('sanitizer.class.php');//configuration file

require_once('controller.php');//configuration file

require_once('classes/phpSEO.php');//configuration file

require_once('classes/class.account.php');//configuration file

require_once('search-advanced.php');//configuration file

require_once('error.php');//configuration file

require_once('menu.php');//configuration file

require_once('sidebar.php');//configuration file

require_once('seo.php');//configuration file

$ocdb=phpMyDB::GetInstance(DB_USER,DB_PASS,DB_NAME,DB_HOST);

if (PAYPAL_ACTIVE) require_once('paypal.php');//paypal functions

if (ONLINEPAYMENT_ACTIVE) require_once('directpay.php');//paypal functions

//special functions from the theme if they exists
if (file_exists(SITE_ROOT.'/themes/'.THEME.'/functions.php')){
	require_once(SITE_ROOT.'/themes/'.THEME.'/functions.php'); 
}
//rajesh20131234
//harihar1
?>