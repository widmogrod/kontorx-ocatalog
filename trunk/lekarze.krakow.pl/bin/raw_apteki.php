<?php

$file = 'http://apteka.wkrakowie.net/apteki1%s.html';


$prefix1 = preg_quote('<font size="2" face="Verdana" color="#FFFFFF">', '|');
$prefix2 = preg_quote('<font color="#FFFFFF">', '|');
$prefix3 = preg_quote('<font face="Verdana" size="2">', '|');


$suffix = preg_quote('</font>', '|U');

$pattern = '([^/]+)';

/**
 * Problem z brakującymi rekordami wynika z pojawienia się nowego tagu
 * 
 * <img border="0" src="linia.gif" width="438" height="8"><font face="Verdana" size="2"><br>
 * 
 * trzeba dodać jego parsowanie
 */

$pattern1 = '|' . $prefix1 . $pattern . $suffix . '|i';
$pattern2 = '|' . $prefix2 . $pattern . $suffix . '|i';
$pattern3 = '|' . $prefix3 . $pattern . $suffix . '|i';

$data = array();

$files = array();
$pages = array();
for($i = 1; $i < 25; $i++) {
	if ($i < 10) {
		$i = '0'.$i;
	}

	echo 'load: ',$files[$i], "\n\t";

	$files[$i] = sprintf($file, $i);

	echo 'get content: ',$files[$i], "\n\t";

	$pages[$i] = file_get_contents($files[$i]);

	echo 'string size:',strlen($pages[$i]), "\n\t";

	
	$data1 = get_page_data1($pages[$i]);
	echo 'find data1: ',count($data1), "\n\t";

	$data2 = get_page_data2($pages[$i]);
	echo 'find data2: ',count($data2), "\n\t";
	
	$data3 = get_page_data3($pages[$i]);
	echo 'find data3: ',count($data3), "\n\t";
	
	echo "\n\n";
	

	_add_data($data1);
	_add_data($data2);
	_add_data($data3);
}

function _add_data($array)
{
	global $data; 
	
	foreach ($array as $d) {
		array_push($data, $d);
	}
}


echo "\n\n", count($data), "\n\n";

$refactor = array();

foreach($data as $d)
{
	if (strlen(trim($d['name'])) < 3) {
		continue;
	}
	
	if (strlen(trim($d['phone'])) < 3) {
		continue;
	}
	
	foreach($d as &$m) {
		if (mb_check_encoding($m,'iso-8859-2')) {
			$m = mb_convert_encoding($m,'iso-8859-2','utf-8');
		}
		$m = html_entity_decode($m);
	}

//	echo "encoding utf-8: ", mb_check_encoding($d['name'],'utf-8'), "\n\t";
//	echo "encoding iso-8859-2: ", mb_check_encoding($d['name'],'iso-8859-2'), "\n\t";
	
	$d['phone'] = str_replace('(012)','12',$d['phone']);
	
	$refactor[] = $d;
}


echo "\n\n refactor:", count($refactor), "\n\n";

//print_r($refactor);

$content = var_export($refactor, true);
$content = sprintf('<?php return %s;', $content);
file_put_contents('data.php', $content);

function get_page_data1($html)
{
	global $pattern1;
	$pattern = $pattern1;

	preg_match_all($pattern, $html, $matches, PREG_PATTERN_ORDER);
	
	$result = array();
	
	foreach ($matches[1] as $match) {
		$row = array();
	
		$split = explode('<br>', $match);
		$split2 = explode(',', $split[1]);
		
		$split = array_map('trim', $split);
		$split2 = array_map('trim', $split2);
	
		$row['name'] = $split[0];
//		$row['województwo'] = $split[2];
		$row['address'] = $split2[1];
		
		// szukaj telefon
		if (false !== preg_match('|tel\.\s*([\(\)\d\-\+ ]+)|', $split[3], $tel)) {
			$row['phone'] = $tel[1];
		}
		
		list($row['postcode'], $row['district']) = explode(' ', $split2[0]);
	
		// dodaj
		$result[] = $row;
	}
	
	return $result;
}



function get_page_data2($html)
{
	global $pattern2;
	$pattern = $pattern2;

	preg_match_all($pattern, $html, $matches, PREG_PATTERN_ORDER);
	$rrrrrr = explode('<img border="0" src="linia.gif" width="438" height="8">', $matches[1][0]);
// <img border="0" src="linia.gif" width="438" height="8">
// <img border="0" src="linia.gif" width="438" height="8">
// <img border="0" width="438" height="8" src="linia.gif">
	$result = array();
	
	foreach ($rrrrrr as $match) {
		$row = array();
	
		$split = explode('<br>', $match);
		$split = array_filter((array)$split);
		$split2 = explode(',', $split[3]);
		
		$split = array_map('trim', $split);
		$split2 = array_map('trim', $split2);
	
		$row['name'] = $split[2];
//		$row['województwo'] = $split[4];
		$row['address'] = $split2[1];
		
		// szukaj telefon
		if (false !== preg_match('|tel\.\s*([\(\)\d\-\+ ]+)|', $split[5], $tel)) {
			$row['phone'] = $tel[1];
		}
		
		list($row['postcode'], $row['district']) = explode(' ', $split2[0]);

		// dodaj
		$result[] = $row;
	}
	
	
//	if (count($result) == 2) {
//		print_r($result);
//	}
	
	return $result;
}



function get_page_data3($html)
{
	global $pattern3;
	$pattern = $pattern3;

	preg_match_all($pattern, $html, $matches, PREG_PATTERN_ORDER);

	$result = array();
	
//	var_dump($matches);
	
	foreach ((array)$matches[1] as $match) {
//		print_r($match);
		$row = array();
	
		$split = explode('<br>', $match);
		$split = array_filter((array)$split);
		$split2 = explode(',', $split[3]);
		
		$split = array_map('trim', $split);
		$split2 = array_map('trim', $split2);
	
		$row['name'] = $split[2];
//		$row['województwo'] = $split[3];
		$row['address'] = $split2[1];
		
		// szukaj telefon
		if (false !== preg_match('|tel\.\s*([\(\)\d\-\+ ]+)|', $split[5], $tel)) {
			$row['phone'] = $tel[1];
		}
		
		list($row['postcode'], $row['district']) = explode(' ', $split2[0]);

		// dodaj
		$result[] = $row;
	}
	
	
//	if (count($result) == 2) {
//		print_r($result);
//	}
	
	return $result;
}
//<img border="0" width="438" height="8" src="linia.gif">



///* @var $xml SimpleXMLElement */
//$xml = @simplexml_load_file($file);
//var_dump($xml);
//
//$dom = new DOMDocument($html);
//var_dump($dom);
////$p = $xml->xpath('/html/body/div[3]/table/tbody/tr[3]/td[2]/table[3]/tbody/tr/td/table/tbody/tr/td[2]/p');
////print $p;

