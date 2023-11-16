<?php

/**
 * Base of exploreDirectory function 
 */

function exploreDirectory($directoryPath, $numLines, $command, &$fileCount, &$filesMoreThan300LinesCount, $parentPath = '') {
	if (!isset($directoryPath)) {
		echo "\033[31mPlease indicate the path of the folder to analyze.\033[0m\n";
		echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";
		exit;
	}

	$filesMoreThan300Lines = [];
	$filesLessThan300Lines = [];

	$directory = opendir($directoryPath);

	if ($directory) {
		while (($file = readdir($directory)) !== false) {
			if ($file != "." && $file != "..") {
				$filePath = $directoryPath . '/' . $file;
				$relativePath = $parentPath . '/' . $file;

				if (is_file($filePath)) {
					/**
					 * Do something here for each file
					 */
				} elseif (is_dir($filePath)) {
					/**
					 * Do something here for each directory
					 */
					exploreDirectory($filePath, $numLines, $command, $fileCount, $filesMoreThan300LinesCount, $relativePath);
				}
			}
		}
		closedir($directory);
	} else {
		echo "Cannot open directory: $directoryPath\n";
	}

	foreach ($filesMoreThan300Lines as $line) {
		echo $line;
	}
	$result = array_merge($filesLessThan300Lines, $filesMoreThan300Lines);

	foreach ($result as $line) {
		echo $line;
	}
}

$startTime = microtime(true);
$directoryPath = $argv[1];
$numLines = $argv[2];
$command = $argv[3];
$fileCount = 0;
$filesMoreThan300LinesCount = 0;
exploreDirectory($directoryPath, $numLines, $command, $fileCount, $filesMoreThan300LinesCount);
$endTime = microtime(true);
$executionTime = $endTime - $startTime;

echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";
echo "Total files: \033[32m$fileCount\033[0m\n";
echo "Execution time: \033[32m$executionTime\033[0m seconds\n";
echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";
