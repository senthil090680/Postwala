<?php

require_once('../../includes/header.php');

if (file_exists(SITE_ROOT.'/themes/'.THEME.'/account/logout.php')){//account logout from the theme!
	require_once(SITE_ROOT.'/themes/'.THEME.'/account/logout.php'); 
}
else{//not found in theme

Account::logOut();
redirect(SITE_URL);

}//if else

?>