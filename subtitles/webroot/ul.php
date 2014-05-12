<?php

session_start();

require '../include/MySmarty.class.php';
require '../include/DataBase.php';
require '../include/isolang.inc';
require '../include/imdb.php';

//

$maxtitles = 4;
$maxsubs = 8;

//

if(isset($_GET['clearimdb']))
{
	unset($_SESSION['imdb_id']);
	unset($_SESSION['imdb_titles']);
	RedirAndExit($_SERVER['PHP_SELF']);
}

if(!empty($_GET))
{
	session_unset();
	$_SESSION['POST'] = $_GET;
	
	$_SESSION['file'] = array();
	for($i = 0; !empty($_GET['name'][$i]) 
		&& !empty($_GET['hash'][$i]) && ereg('[0-9a-fA-F]{16}', $_GET['hash'][$i]) 
		&& !empty($_GET['size'][$i]) && ereg('[0-9a-fA-F]{16}', $_GET['size'][$i]);
		$i++)
	{
		$file['name'] = $_GET['name'][$i];
		$file['hash'] = $_GET['hash'][$i];
		$file['size'] = $_GET['size'][$i];
		sscanf($_GET['size'][$i], "%x", $file['intsize']);
		$_SESSION['file'][$i+1] = $file;
		
		if(!isset($_SESSION['POST']['guessedtitle']))
			$_SESSION['POST']['guessedtitle'] = $file['name'];
	
		// TODO: search imdb on name || size || hash -> imdb
	}
		
	global $maxsubs;
	for($i = 0; $i < $maxsubs; $i++)
		$_SESSION['POST']['file_sel'][$i] = $i < count($_SESSION['file']) ? $i+1 : 0;
	
	RedirAndExit($_SERVER['PHP_SELF']);
}

//

function mergeTitles($a, $b)
{
	$ret = array();
	foreach(array_merge($a, $b) as $title)
	{
		$skip = false;
		foreach($ret as $i => $title0)
		{
			if(stristr($title, $title0) !== false) {$ret[$i] = $title; $skip = true;}
			else if(stristr($title0, $title) !== false) $skip = true;
		}
		if(!$skip) $ret[] = $title;
	}
	return $ret;
}

if(isset($_POST['update']) || isset($_POST['submit']))
{
	$_SESSION['POST'] = $_POST;
	
	// validation

	unset($_SESSION['err']);
	
	$titles = array();
	for($i = 0; $i < $maxtitles; $i++)
		if($title = trim(strip_tags(getParam('title', $i))))
			$titles[] = $title;
	$imdb_url = trim(getParam('imdb_url'));
	$nick = strip_tags(getParam('nick'));
	$email = strip_tags(getParam('email'));

	if(!empty($imdb_url))
	{
		$imdb_titles = array();
		
		if(eregi('/title/tt([0-9]+)', $imdb_url, $regs))
		{
			$imdb_id = intval($regs[1]);

			if(empty($imdb_titles))
			{
				if(isset($_SESSION['imdb_id']) && $_SESSION['imdb_id'] == $imdb_id)
					$_SESSION['imdb_titles'] = $imdb_titles;
			}

			if(empty($imdb_titles))
			{
				$db->query("select title from title where movie_id in (select id from movie where imdb = $imdb_id)");
				while($row = $db->fetchRow()) $imdb_titles[] = $row['title'];
				$_SESSION['imdb_id'] = $imdb_id;
				$_SESSION['imdb_titles'] = $imdb_titles;
			}

			if(empty($imdb_titles))
			{
				$imdb_titles = getIMDbTitles($imdb_url);
				$_SESSION['imdb_id'] = $imdb_id;
				$_SESSION['imdb_titles'] = $imdb_titles;
				storeMovie($imdb_id, $imdb_titles);
			}
		}

		$titles = mergeTitles($imdb_titles, $titles);

		if(empty($imdb_titles))
			$_SESSION['err']['imdb_url'] = true;
	}
	else
	{
		$imdb_id = 0;
	}
	
	if(empty($titles)) $_SESSION['err']['title'][0] = true;

	$_SESSION['err']['nosub'] = true;

	for($i = 0; $i < $maxsubs; $i++)
	{
		if(empty($_FILES['sub']['tmp_name'][$i])) continue;

		$format_sel = getParam('format_sel', $i);
		$isolang_sel = getParam('isolang_sel', $i);
		$discs = intval(getParam('discs', $i));
		$disc_no = intval(getParam('disc_no', $i));
		$file_sel = intval(getParam('file_sel', $i));
			
		if(empty($format_sel)) $_SESSION['err']['format_sel'][$i] = true;
		if(empty($isolang_sel)) $_SESSION['err']['isolang_sel'][$i] = true;
		if($discs < 1 || $discs > 127 || $disc_no < 1 || $disc_no > 127 || $disc_no > $discs) $_SESSION['err']['disc_no'][$i] = true;
		if(!empty($_SESSION['file']) && empty($_SESSION['file'][$file_sel])) $_SESSION['err']['file_sel'][$i] = true;
	
		if(!isset($_SESSION['err']['format_sel'][$i])
		&& !isset($_SESSION['err']['isolang_sel'][$i])
		&& !isset($_SESSION['err']['disc_no'][$i])
		&& !isset($_SESSION['err']['file_sel'][$i]))
		{
			unset($_SESSION['err']['nosub']);
		}	
	}

	if(!empty($_SESSION['err']) || isset($_POST['update']))
	{
		RedirAndExit($_SERVER['PHP_SELF']);
	}

	//
	
	$db->begin();

	$movie_id = storeMovie($imdb_id, $titles);
	$files = array();
	
	for($i = 0; $i < $maxsubs; $i++)
	{
		if(empty($_FILES['sub']['tmp_name'][$i])) continue;
		
		$sub = @file_get_contents($_FILES['sub']['tmp_name'][$i]);
		$db_sub = addslashes(gzcompress($sub, 9));
		$db_name = addslashes(basename(stripslashes($_FILES['sub']['name'][$i])));
		$db_hash = md5($sub);
		$db_mime = addslashes($_FILES['sub']['type'][$i]);
		$format_sel = getParam('format_sel', $i); // TODO: verify this
		$isolang_sel = getParam('isolang_sel', $i); // TODO: verify this
		$discs = intval(getParam('discs', $i));
		$disc_no = intval(getParam('disc_no', $i));
		$file_sel = intval(getParam('file_sel', $i));
		$db_notes = addslashes(strip_tags(getParam('notes', $i)));

		$db->query("select id from subtitle where hash = '$db_hash'");

		if($row = $db->fetchRow())
		{
			$subtitle_id = $row[0];
		}
		else
		{
			$db->query(
				"insert into subtitle (discs, disc_no, sub, hash, mime) ".
				"values ($discs, $disc_no, '$db_sub', '$db_hash', '$db_mime')");
				
			$subtitle_id = $db->fetchLastInsertId();
		}

		chkerr();

		if($db->count("movie_subtitle where movie_id = $movie_id && subtitle_id = $subtitle_id") == 0)
			$db->query(
				"insert into movie_subtitle (movie_id, subtitle_id, name, userid, date, notes, format, iso639_2) ".
				"values($movie_id, $subtitle_id, '$db_name', {$db->userid}, NOW(), '$db_notes', '$format_sel', '$isolang_sel') ");

		chkerr();
		
		if(isset($_SESSION['file'][$file_sel]))
		{
			$file = $_SESSION['file'][$file_sel];
			
			$hash = $file['hash'];
			$size = $file['size'];

			$db->query("select * from file where hash = '$hash' && size = '$size'");
			if($row = $db->fetchRow()) $file_id = $row['id'];
			else {$db->query("insert into file (hash, size) values ('$hash', '$size')"); $file_id = $db->fetchLastInsertId();}
			
			chkerr();

			if($db->count("file_subtitle where file_id = $file_id && subtitle_id = $subtitle_id") == 0)
				$db->query("insert into file_subtitle (file_id, subtitle_id) values($file_id, $subtitle_id)");
	
			chkerr();
			
			$files[] = $_SESSION['file'][$file_sel];
		}
	}
	
	if(!empty($email) && !empty($nick))
	{
		$db->query("update subtitle set nick = '$db_nick' where email = '$db_email'");
	}
	
	$redir = 'index.php?text='.urlencode($titles[0]);
	
	if(!empty($files))
	{
		$args = array();
		foreach($files as $i => $file)
			foreach($file as $param => $value)
				$args[] .= "{$param}[$i]=".urlencode($value);
		$redir = 'index.php?'.implode('&', $args);
	}

	// TODO: move all these under one struct
	unset($_SESSION['POST']);
	unset($_SESSION['file']);
	unset($_SESSION['imdb_id']);
	unset($_SESSION['imdb_titles']);	

	$db->commit();
	
	RedirAndExit($redir);
}

// subs

$subs = array();
for($i = 0; $i < $maxsubs; $i++) $subs[] = $i;
$smarty->assign('subs', $subs);

function assign($param, $limit = 0)
{
	global $smarty;
	
	if($limit > 0)
	{
		$tmp = array();
		for($i = 0; $i < $limit; $i++) $tmp[$i] = getParam($param, $i);
		$smarty->assign($param, $tmp);
	}
	else
	{
		$smarty->assign($param, $tmp = getParam($param));
	}

	return $tmp;
}

function assign_cookie($param)
{
	global $smarty;
	$value = getParam($param);
	if($value !== false) setcookie($param, $value, time()+60*60*24*30, '/');
	$smarty->assign($param, $value);
	return $value;
}

// titles, imdb

assign('title', $maxtitles);
assign('guessedtitle');
assign('imdb_url');

if(isset($_SESSION['imdb_id']) && !empty($_SESSION['imdb_titles']))
{
	$smarty->assign('imdb_id', $_SESSION['imdb_id']);
	$smarty->assign('imdb_titles', $_SESSION['imdb_titles']);
}

// subs

$smarty->assign('isolang', $isolang);
assign('isolang_sel', $maxsubs);
$smarty->assign('format', $db->enumsetValues('movie_subtitle', 'format'));
assign('format_sel', $maxsubs);
assign('discs', $maxsubs);
assign('disc_no', $maxsubs);
$smarty->assign('file', !empty($_SESSION['file']) ? $_SESSION['file'] : false);
assign('file_sel', $maxsubs);
assign('notes', $maxsubs);

// err

if(isset($_SESSION['err'])) 
	$smarty->assign('err', $_SESSION['err']);

//

$smarty->display('main.tpl');

?>