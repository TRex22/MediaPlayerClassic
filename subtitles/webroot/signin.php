<?php

session_start();

require '../include/MySmarty.class.php';
require '../include/DataBase.php';

if(isset($_POST['signin']) || isset($_POST['register']))
{	
	$_SESSION['nick'] = isset($_POST['nick']) ? stripslashes(trim($_POST['nick'])) : "";
	$_SESSION['password'] = isset($_POST['password']) ? stripslashes(trim($_POST['password'])) : "";
	$_SESSION['email'] = isset($_POST['email']) ? stripslashes(trim($_POST['email'])) : "";
	$_SESSION['rememberme'] = isset($_POST['rememberme']);

	unset($_SESSION['err']);
	
	if(!ereg('^[a-zA-Z0-9]{3,}$', $_SESSION['nick']))
		$_SESSION['err']['nick'] = true;
	if(!ereg('^[a-zA-Z0-9]{5,}$', $_SESSION['password']))
		$_SESSION['err']['password'] = true;

	if(isset($_POST['signin']))
	{
		unset($_SESSION['email']);
		
		if(empty($_SESSION['err']))
			$db->Login($_SESSION['nick'], $_SESSION['password'], $_SESSION['rememberme']);
	}
	else if(isset($_POST['register']))
	{
		if(!ereg('^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]{1,})*\.([a-z]{2,}){1}$', $_SESSION['email']))
			$_SESSION['err']['email'] = true;
		
		$nick = addslashes($_SESSION['nick']);
		if($db->count("user where nick = '$nick'") > 0)
			$_SESSION['err']['nick'] = true;
		
		if(empty($_SESSION['err']))
			if($db->Register($_SESSION['nick'], $_SESSION['password'], $_SESSION['email']))
				$db->Login($_SESSION['nick'], $_SESSION['password'], $_SESSION['rememberme']);
	}

	RedirAndExit($_SERVER['PHP_SELF']);
}

$smarty->assign('nick', isset($_SESSION['nick']) ? $_SESSION['nick'] : "");
$smarty->assign('email', isset($_SESSION['email']) ? $_SESSION['email'] : "");
$smarty->assign('rememberme', isset($_SESSION['rememberme']) ? $_SESSION['rememberme'] : false);
$smarty->assign('err', isset($_SESSION['err']) ? $_SESSION['err'] : null);

$smarty->display('main.tpl');

?>