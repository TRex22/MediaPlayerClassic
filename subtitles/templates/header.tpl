{* Smarty *}

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>

<head>
	<title>{$ServerName} @ {$smarty.server.HTTP_HOST}</title>
	<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset={$EncodingType}">
	{popup_init src="script/overlib_mini.js"}
	<script type="text/javascript" src="script/flip.js"></script>
	<link rel="stylesheet" type="text/css" href="css/flip.css" />
	<link rel="stylesheet" type="text/css" href="css/default.css" />

	<script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
	</script>
	<script type="text/javascript">
	_uacct = "UA-83903-2";
	urchinTracker();
	</script>

</head>

<body bgcolor="#ffffff">

<div id="navcontainer">

<ul id="navlist">
	<li><a href="index.php" title="Search">Search</a></li>
	<li><a href="ul.php" title="Upload">Upload</a></li>
	<li><a href="mirrors.php" title="Mirrors">Mirrors</a></li>
	{if $user.userid > 0}<li><a href="signout.php" title="Sign out">Sign out ({$user.nick})</a></li>
	{else}<li><a href="signin.php" title="Sign in">Sign in</a></li>{/if}
</ul>

</div>

<div id="content">