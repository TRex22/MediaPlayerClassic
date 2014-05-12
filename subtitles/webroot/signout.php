<?php

session_start();

require '../include/MySmarty.class.php';
require '../include/DataBase.php';

if(empty($_SERVER['HTTP_REFERER']) || $_SERVER['HTTP_REFERER'] == $_SERVER['PHP_SELF'])
{
	RedirAndExit('index.php');
}

$db->Logout();

echo '<html><head><META HTTP-EQUIV=REFRESH CONTENT="0;url='.$_SERVER['HTTP_REFERER'].'"></head></html>';

?>