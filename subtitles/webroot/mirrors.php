<?php

session_start();

require '../include/MySmarty.class.php';
require '../include/DataBase.php';

$http_host = split(':', $_SERVER['HTTP_HOST']);
$db_host = addslashes($http_host[0]);

$db->query(
	"select scheme, host, port, path, name, (to_days(NOW()) - to_days(lastseen)) as lastseen_days ".
	"from mirror where host <> '$db_host' order by lastseen desc ");

$mirrors = array();

while($row = $db->fetchRow())
{
	$url = "{$row['scheme']}://{$row['host']}";
	if($row['port'] != 80 && $row['port'] != 0) $url .= ":{$row['port']}";
	$url .= $row['path'];
	// MAYDO: verify url, skip unreachable
	$mirrors[] = array('url' => $url, 'name' => $row['name'], 'days' => $row['lastseen_days']);
}

$smarty->assign('mirrors', $mirrors);

$smarty->display('main.tpl');

?>