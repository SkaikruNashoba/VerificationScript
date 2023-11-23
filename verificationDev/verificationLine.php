<?php

/**
 * Explore directory and subdirectory to check if files have more than X lines
 */

echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n\n";

function explorePath($path, &$command, &$numLines) {
	if (!isset($path)) {
		echo "\033[31mPlease indicate the path of the folder or file to analyze.\033[0m\n";
		echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";
		exit;
	}



	if (is_file($path)) {
		exploreFile($path, $command, $numLines);
	} elseif (is_dir($path)) {
		$files = scandir($path);
		foreach ($files as $file) {
			if ($file != "." && $file != "..") {
				$filePath = $path . '/' . $file;
				explorePath($filePath, $command, $numLines);
			}
		}
	}
}

function exploreFile($path, $command, $numLines) {
	if (!isset($path) || !isset($numLines)) {
		echo "\033[31mPlease indicate the path of the folder or file to analyze and the number of lines.\033[0m\n";
		echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";
		exit;
	}
	$filesMoreThanXLines = [];
	$filesLessThanXLines = [];

	if (isset($command) && $command === '-noExplain') {
		echo "";
	} else {
		echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";
		echo "Scan of the file \033[33m$path\033[0m\n";
		echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";
	}

	$lineCount = count(file($path));

	if ($lineCount > $numLines) {
		$line = "x The file \033[31m$path\033[0m (currently: \033[31m$lineCount\033[0m lines).\n";
		$filesMoreThanXLines[] = $line;
	} else {
		$line = "\u{21AA} The file \033[32m$path\033[0m (currently: \033[32m$lineCount\033[0m lines).\n";
		$filesLessThanXLines[] = $line;
	}

	$result = array_merge($filesLessThanXLines, $filesMoreThanXLines);

	foreach ($result as $line) {
		echo $line;
	}
}

$startTime = microtime(true);
$path = $argv[1];
$command = $argv[2];
$numLines = $argv[3];
explorePath($path, $command, $numLines);
$endTime = microtime(true);
$executionTime = round($endTime - $startTime, 2);

echo "\n\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";
echo "Execution time: \033[32m$executionTime\033[0m seconds\n";
echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";


/**
 *  Creator :
 *  https://github.com/SkaikruNashoba
 * 
 *  Version
 *  1.0.17
 */
