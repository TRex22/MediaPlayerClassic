<?php

session_start();

require '../include/MySmarty.class.php';
require '../include/DataBase.php';

if(empty($_GET['id']) 
|| empty($_GET['ticket']) || empty($_SESSION['ticket'])
|| $_GET['ticket'] != $_SESSION['ticket'])
	error404();

$db = new SubtitlesDB();

$id = intval($_GET['id']);
$db->query(
	"select t1.name, t2.id, t2.hash, t2.mime from movie_subtitle t1 ".
	"join subtitle t2 on t1.subtitle_id = t2.id ".
	"where t1.id = $id ");
if(!($row = $db->fetchRow())) error404();

$id = $row['id'];
$hash = $row['hash'];
$name = $row['name'];
$mime = $row['mime'];

$fn = "../subcache/$hash";

@mkdir("../subcache");

if(!($sub = @file_get_contents($fn)) || empty($sub))
{
	$db->query("select sub from subtitle where id = $id");
	if(!($row = $db->fetchRow())) error404();

	$sub = $row['sub'];
	if($fp = fopen($fn, "wb")) {fwrite($fp, $sub); fclose($fp);}
}

$db->query("update subtitle set downloads = downloads+1 where id = $id");

//header("Content-Type: $mime");
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"$name\"");
header("Pragma: no-cache");
$sub = gzuncompress($sub);

if(!headers_sent() && extension_loaded("zlib")
&& ereg("gzip", $_SERVER["HTTP_ACCEPT_ENCODING"]))
{
	$sub = gzencode($sub, 9);

	header("Content-Encoding: gzip");
	header("Vary: Accept-Encoding");
	header("Content-Length: ".strlen($sub));
}

echo $sub;

exit;

?>