<?php
//error_reporting(E_ALL);
ini_set('upload_max_filesize','500G');
echo ini_get('upload_max_filesize');
echo $_SERVER[DOCUMENT_ROOT]."<br>";
echo phpinfo();
?>