<?php

require 'utf8.php';

function getIMDbTitles($imdb_url)
{
	$titles = array();

	set_time_limit(180);
	
	if(($str = @file_get_contents(rtrim($imdb_url, '/')))
	|| ($str = @file_get_contents(eregi_replace('\.com/', '.com.nyud.net:8090/', rtrim($imdb_url, '/')))))
	{
		$str = str_replace("&#32;", "", $str);
		$str = str_replace("\r", "", $str);
		$str = str_replace("\n", "|", $str);
		
		if(preg_match('/<title>(.+)<\/title>/i', $str, $regs))
//		if(preg_match('/<strong class="title">(.+)<\/strong>/i', $str, $regs))
			$titles[] = html2utf8(trim(strip_tags($regs[1])));

		// TODO: stripos
		$aka = '<b class="ch">Also Known As';
		if(($str = stristr($str, $aka))
		&& ($str = substr($str, strlen($aka), strpos($str, '|') - strlen($aka))))
		{
			$tmp = explode('<br>', $str);
			foreach($tmp as $title)
			{
				$title = trim(strip_tags($title));
				if($i = strpos($title, ') ')) $title = substr($title, 0, $i+1);
				if(!empty($title) && strlen($title) > 1) $titles[] = html2utf8($title);
			}
		}
	}
	
	return $titles;
}

function storeMovie($imdb_id, $titles)
{
	$db_titles = array();
	foreach($titles as $title)
		$db_titles[] = addslashes($title);

	$movie_id = 0;

	global $db;
	$db->query("select * from movie where imdb = $imdb_id && imdb != 0 ");

	if($row = $db->fetchRow())
	{
		$movie_id = $row['id'];
	}
	else
	{
		$db->query("insert into movie (imdb) values ($imdb_id) ");
		$movie_id = $db->fetchLastInsertId();
	}

	chkerr();
	
	foreach($db_titles as $db_title)
		if($db->count("title where movie_id = $movie_id && title = '$db_title'") == 0)
			$db->query("insert into title (movie_id, title) values ($movie_id, '$db_title') ");

	chkerr();
			
	return $movie_id;
}

?>