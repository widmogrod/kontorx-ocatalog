#!/usr/bin/php
<?php
/**
 * Setup
 */
$basePath = dirname(dirname(__FILE__));

$appPath = $basePath . '/app/';
$tmpPath = $basePath . '/tmp/';
$logPath = $basePath . '/log/';
$pubPath = $basePath . '/pub/';

$logGlob= $logPath . '*';
$tmpGlob= $tmpPath . '***';
$updatesGlob= $appPath . '/modules/*/updates/';

$pubUpload = $pubPath . '/upload/';




/**
 * Clean module update info
 */



// print start info
print 'Clean modules update info [START]' . "\n";

// run ..
foreach (glob($updatesGlob, GLOB_ONLYDIR) as $path) {
	// debug
	print 'path: ' . $path . "\n";

	$result = chmod($path, 0777);
	// debug
	printf("\t chmod: %s \n", $result ? 'yes' : 'no');

	$result = false;
	$updateFile =  $path . '/.update';
	if (is_file($updateFile)) {
		if (is_writable($updateFile)) {
			$result = unlink($updateFile);
		}	
	} else {
		$result = true;
	}

	// debug
	printf("\t unlink: %s \n", $result ? 'yes' : 'no');
}

print 'Clean modules update info [END]' . "\n";




/**
 * Clean dir /log
 */




// print start info
print 'Clean dir [/log]  [START]' . "\n";

if (!$result = chmod($logPath, 0777)) {
	print 'log path: ' . $logPath . "\n";
	printf("\t chmod: %s \n", $result ? 'yes' : 'no');
}

foreach (glob($logGlob) as $path) {
	$result = false;
	if (is_file($path)) {
		if (is_writable($path)) {
			$result = unlink($path);
		}
	} else {
		$result = true;
	}

	if (!$result) {
		print 'path: ' . $path . "\n";
		printf("\t unlink: %s \n", $result ? 'yes' : 'no');
	}
}

print 'Clean dir [/log]  [END]' . "\n";




/**
 * Clean dir [/tmp]
 */




// print start info
print 'Clean dir [/tmp]  [START]' . "\n";

if (!$result = chmod($tmpPath, 0777)) {
	print 'tmp path: ' . $tmpPath . "\n";
	printf("\t chmod: %s \n", $result ? 'yes' : 'no');
}

$iterator = new RecursiveDirectoryIterator($tmpPath);
$iterator = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::SELF_FIRST);
foreach ($iterator as $item) {
	$path = $item->getPathname();

	$result = false;
	if ($item->isFile()) {
		if ($item->isWritable()) {
			if (!$result = unlink($path)) {
				print 'path: ' . $path . "\n";
				printf("\t unlink: %s \n", $result ? 'yes' : 'no');
			}
		}
	} else
	if ('.' != $item->getFilename() || '..' != $item->getFilename()){
		if (!$result = chmod($path, 0777)) {
			print 'path: ' . $path . "\n";
			printf("\t chmod: %s \n", $result ? 'yes' : 'no');
		}
	}
}

print 'Clean dir [/tmp]  [END]' . "\n";

// print start info
print 'Clean dir [/pub/upload]  [START]' . "\n";

// run ..
print 'tmp path: ' . $pubUpload . "\n";
$result = chmod($pubUpload, 0777);
printf("\t chmod: %s \n", $result ? 'yes' : 'no');

$iterator = new RecursiveDirectoryIterator($pubUpload);
$iterator = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::SELF_FIRST);
foreach ($iterator as $item) {
	$path = $item->getPathname();

	if ($item->isDir()) {
		if ('.' != $item->getFilename() || '..' != $item->getFilename()){
			if (!$result = chmod($path, 0777)) {
				print 'path: ' . $path . "\n";
				printf("\t chmod: %s \n", $result ? 'yes' : 'no');
			}
		}
	}
}

print 'Clean dir [/pub/upload]  [END]' . "\n";

