<?php
ob_start();
session_start();
		if ($_POST){//try to login
	    require_once('../includes/functions.php');//loading functions
		
		/*echo cP('user')."<br>";
		echo cP('pwd')."<br>";
		echo $_SESSION['admin']."<br>";
		echo ADMIN."<br>";
		echo ADMIN_PWD."<br>";*/
	    if (ADMIN==cP('user') && ADMIN_PWD==cP('pwd')){//it's the same as in config.php?
			$_SESSION['admin']=cP('user');//setting the session
			//echo $_SESSION['admin']."<br>";
			redirect('index.php');
		} else echo "MEC!!";	
	}
	
	require_once('header.php');
?>
<h2><?php _e("Administration Login");?></h2>
<form action="login.php" method="post" onsubmit="return checkForm(this);" >
	<fieldset>
    	<p>
            <label><?php _e("User");?>:</label>
            <input name="user" type="text" class="text-long" onblur="validateText(this);" lang="false" value=""  />
        </p>
        <p>
            <label><?php _e("Password");?>:</label>
            <input name="pwd" type="password" class="text-long" onblur="validateText(this);"  lang="false" value="" />
        </p>
		<input type="submit" value="<?php _e("Submit")?>" class="button-submit" />
	</fieldset>
</form>
<?php
require_once('footer.php');
?>
