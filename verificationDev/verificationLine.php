<?php

/**
 * Explore directory and subdirectory to check if files have more than X lines
 */
function exploreDirectory($directoryPath, $numLines, $command, &$fileCount, &$filesMoreThanXLinesCount, $parentPath = '') {
	if (!isset($directoryPath) || !isset($numLines)) {
		echo "\033[31mPlease indicate the path of the folder to analyze and the number of lines.\033[0m\n";
		echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";
		exit;
	}

	$filesMoreThanXLines = [];
	$filesLessThanXLines = [];

	$directory = opendir($directoryPath);

	if ($directory) {
		if (isset($command) && $command === '-noExplain') {
			echo "";
		} else {
			echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";
			echo "Scan of the folder \033[33m$directoryPath/\033[0m\n";
			echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";
		}
		while (($file = readdir($directory)) !== false) {
			if ($file != "." && $file != "..") {
				$filePath = $directoryPath . '/' . $file;
				$relativePath = $parentPath . '/' . $file;

				if (is_file($filePath)) {
					$lineCount = count(file($filePath));

					if ($lineCount > $numLines) {
						$line = "x The file \033[31m$relativePath\033[0m has more than \033[31m$numLines\033[0m lines (currently: \033[31m$lineCount\033[0m lines).\n";
						$filesMoreThanXLines[] = $line;
						$filesMoreThanXLinesCount++;
					} else {
						$line = "\u{21AA} The file \033[32m$relativePath\033[0m has less than \033[32m$numLines\033[0m lines (currently: \033[32m$lineCount\033[0m lines).\n";
						$filesLessThanXLines[] = $line;
					}

					$fileCount++;
				} elseif (is_dir($filePath)) {
					exploreDirectory($filePath, $numLines, $command, $fileCount, $filesMoreThanXLinesCount, $relativePath);
					if (isset($command) && $command === '-noExplain') {
						echo "";
					} else {
						echo "\n~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";
						echo "Exit of the folder \033[33m$directoryPath/\033[0m\n";
						echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n\n";
					}
				}
			}
		}
		closedir($directory);
	} else {
		echo "Cannot open directory: $directoryPath\n";
	}

	foreach ($filesMoreThanXLines as $line) {
		echo $line;
	}
	$result = array_merge($filesLessThanXLines, $filesMoreThanXLines);

	foreach ($result as $line) {
		echo $line;
	}
}

$startTime = microtime(true);
$directoryPath = $argv[1];
$command = $argv[2];
$numLines = $argv[3];
$fileCount = 0;
$filesMoreThanXLinesCount = 0;
exploreDirectory($directoryPath, $numLines, $command, $fileCount, $filesMoreThanXLinesCount);
$endTime = microtime(true);
$executionTime = round($endTime - $startTime, 2);

echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";
echo "Total of files scanned: \033[32m$fileCount\033[0m\n";
if ($filesMoreThanXLinesCount === 0) {
	echo "Total of files with more than $numLines lines : \033[32m$filesMoreThanXLinesCount\033[0m\n";
} else {
	echo "Total of files with more than $numLines lines : \033[31m$filesMoreThanXLinesCount\033[0m\n";
}
echo "Execution time : \033[32m$executionTime secondes\033[0m\n";
echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";

/**
 *  Creator :
 *  https://github.com/SkaikruNashoba
 */
