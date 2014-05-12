<?php

session_start();

$EncodingTypeOverride = 'iso-8859-2'; // FIXME

require '../include/MySmarty.class.php';

$intypes = array(
	0 => 'detect',
	1 => '???',
	2 => '"mie" :P',
	3 => 'emi');

$outtypes = array(
	0 => 'srt',
	1 => 'sub');
	
$smarty->assign('intypes', $intypes);
$smarty->assign('outtypes', $outtypes);

$fps = !empty($_POST['fps']) ? stripslashes($_POST['fps']) : 25;
$intype = isset($_POST['intype']) ? intval($_POST['intype']) : 0;
$outtype = isset($_POST['outtype']) ? intval($_POST['outtype']) : 0;
$text = isset($_POST['text']) ? stripslashes($_POST['text']) : '';

if(!empty($_POST))
{
	$subs = array();

	if($intype == 0)
	{
		$rows = explode("\n", $text);
		$text = '';
		
		$sample = array_slice($rows, 0, min(10, count($rows)));
		foreach($sample as $row)
		{	
			if(preg_match('/^([0-9]{4})\.([0-9]+)-([0-9]{4})\.([0-9]+)/', $row, $matches))
			{
				$intype = 1;
				break;
			}
			else if(preg_match('/^File:<</', $row, $matches))
			{
				$intype = 2;
				break;
			}
			else if(preg_match('/^SUBTITLE:.+?TIMEIN:.+?TIMEOUT:/i', $row, $matches))
			{
				$intype = 3;
				break;				
			}
		}
		
		if($intype == 1)
		{
			foreach($rows as $row)
			{
				$row = trim($row);
				
				if(preg_match('/^([0-9]{4})\.([0-9]+)-([0-9]{4})\.([0-9]+)/', $row, $matches))
				{
					$start = ((intval($matches[1])*16 + intval($matches[2])) / $fps)*1000;
					$stop = ((intval($matches[3])*16 + intval($matches[4])) / $fps)*1000;
					$subs[] = array('start' => $start, 'stop' => $stop, 'rows' => array());
					$sub = end($subs);
				}
				else if(!empty($row))
				{
					if(empty($subs)) break;
					$subs[count($subs)-1]['rows'][] = $row;
				}
			}
		}
		else if($intype == 2)
		{
			$text = array();
			$start = 0;
			$stop = 0;
			
			foreach($rows as $row)
			{
				$row = trim($row);

				if(preg_match('/^[0-9]+\.(.+?)([0-9]+), *([0-9]+) +([0-9]+), *([0-9]+)/', $row, $matches))
				{
					$text = array();
					
					$row = trim($matches[1]);
					if(!empty($row)) $text[] = $row;
					
					$start = intval($matches[2])*600 + intval($matches[3])*1000/16;
					$stop = intval($matches[4])*600 + intval($matches[5])*1000/16;
				}				
				else if(preg_match('/^[0-9]+\.(.+?)([0-9]+):([0-9]+):([0-9]+), *([0-9]+) +([0-9]+):([0-9]+):([0-9]+), *([0-9]+)/', $row, $matches))
				{
					$text = array();
					
					$row = trim($matches[1]);
					if(!empty($row)) $text[] = $row;
					
					$start = ((intval($matches[2])*60 + intval($matches[3]))*60 + intval($matches[4]))*1000 + intval($matches[5])*1000/16;
					$stop = ((intval($matches[6])*60 + intval($matches[7]))*60 + intval($matches[8]))*1000 + intval($matches[9])*1000/16;
				}
				else if(preg_match('/^\[ *[0-9]+\](.+)$/', $row, $matches))
				{
					if($start == 0 && $stop == 0) continue;

					$row = trim($matches[1]);
					if(!empty($row)) $text[] = $row;
					
					foreach($text as $i => $t)
					{
						for($j = 0; $j < strlen($t); $j++)
						{
							$c = ord($t[$j]);
							switch($c)
							{
								case 0x81: $c = 'ü'; break;
								case 0x82: $c = 'é'; break;
								case 0x90: $c = 'É'; break;
								case 0x94: $c = 'ö'; break;
								case 0x99: $c = 'Ö'; break;
								case 0x9A: $c = 'Ü'; break;
								case 0xa0: $c = 'á'; break;
								case 0xa1: $c = 'í'; break;
								case 0xa2: $c = 'ó'; break;
								case 0xa3: $c = 'ú'; break;
								case 0xf0: $c = 'Í'; break;
								case 0xf1: $c = 'Ó'; break;
								case 0xf2: $c = 'Õ'; break;
								case 0xf3: $c = 'õ'; break;
								case 0xf4: $c = 'Ú'; break;
								case 0xf9: $c = 'û'; break;
								case 0xfa: $c = 'Á'; break;
								default: $c = chr($c); break;
							}
							$text[$i][$j] = $c;
						}
					}
					
					$subs[] = array('start' => $start, 'stop' => $stop, 'rows' => $text);
					
					$start = $stop = 0;
				}
			}
		}
		else if($intype == 3)
		{
			foreach($rows as $row)
			{
				$row = trim($row);
				$row = str_replace("\t", " ", $row);
				if(preg_match('/SUBTITLE: *[0-9]+ *TIMEIN: *([0-9]+):([0-9]+):([0-9]+):([0-9]+) *TIMEOUT: *([0-9]+):([0-9]+):([0-9]+):([0-9]+)/', $row, $matches))
				{
					$start = ((intval($matches[1])*60 + intval($matches[2]))*60 + intval($matches[3])) * 1000 + intval($matches[4]) * 1000 / $fps;
					$stop = ((intval($matches[5])*60 + intval($matches[6]))*60 + intval($matches[7])) * 1000 + intval($matches[8]) * 1000 / $fps;
					$subs[] = array('start' => $start, 'stop' => $stop, 'rows' => array());
					$sub = end($subs);
				}
				else if(!empty($row))
				{
					if(empty($subs)) break;
					$subs[count($subs)-1]['rows'][] = $row;
				}
			}			
		}
	}
	
	$text = array();
	
	foreach($subs as $i => $sub)
	{
		$start = $sub['start'];
		$stop = $sub['stop'];
		
		$ms1 = $sub['start'] % 1000; $sub['start'] /= 1000;
		$ss1 = $sub['start'] % 60; $sub['start'] /= 60;
		$mm1 = $sub['start'] % 60; $sub['start'] /= 60;
		$hh1 = $sub['start'];
		$ms2 = $sub['stop'] % 1000; $sub['stop'] /= 1000;
		$ss2 = $sub['stop'] % 60; $sub['stop'] /= 60;
		$mm2 = $sub['stop'] % 60; $sub['stop'] /= 60;
		$hh2 = $sub['stop'];

		if($outtype == 0)
		{
			$text[] = $i;
			$text[] = sprintf("%02d:%02d:%02d,%03d --> %02d:%02d:%02d,%03d", 
				$hh1, $mm1, $ss1, $ms1, $hh2, $mm2, $ss2, $ms2);
			foreach($sub['rows'] as $row)
				$text[] = $row;
			$text[] = '';
		}
		else if($outtype == 1)
		{
			$text[] = sprintf("{%d}{%d}%s", 
				$start * $fps / 1000, 
				$stop * $fps / 1000, 
				implode('|', $sub['rows']));
		}
	}
	
	$text = implode("\r\n", $text);
	
	if(!empty($text))
	{
		header('Content-Type: application/octet-stream');
		header("Content-Disposition: attachment; filename=\"subtitle.{$outtypes[$outtype]}\"");
		header("Pragma: no-cache");
	
		if(!headers_sent() && extension_loaded("zlib")
		&& ereg("gzip", $_SERVER["HTTP_ACCEPT_ENCODING"]))
		{
			$text = gzencode($text, 9);
			
			header("Content-Encoding: gzip");
			header("Vary: Accept-Encoding");
			header("Content-Length: ".strlen($text));
		}
	
		echo $text;
		exit;
	}
	
	$smarty->assign('intype', $intype);
	$smarty->assign('outtype', $outtype);
	$smarty->assign('text', $_POST['text']);
	$smarty->assign('conversion_error', true);
}

$smarty->assign('fps', $fps);

$smarty->display('main.tpl');

?>