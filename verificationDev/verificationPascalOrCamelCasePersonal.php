<?php

/**
 * Explore directory and subdirectory to check if files and folders are named in PascalCase or camelCase
 */

function exploreDirectory($directoryPath, $command, $commandDir, &$fileCount, $parentPath = '') {
	if (!isset($directoryPath)) {
		echo "\033[31mPlease indicate the path of the folder to analyze.\033[0m\n";
		echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";
		exit;
	}

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
					$fileCount++;
					$fileNameParts = explode('.', $file);
					$baseName = reset($fileNameParts);

					if (isset($commandDir) && $commandDir === '-onlyDir') {
						echo "";
					} else {
						if (strtolower($baseName) === 'index') {
							echo "\033[32mV\033[0m \033[93mFile\033[0m '$relativePath' \033[32mvalided\033[0m.\n";
						} elseif (!preg_match('/^([[:lower:]]+)[A-Z][a-zA-Z]+|^[A-Z][a-z]+$/', $baseName)) {
							echo "\033[31mX\033[0m \033[93mFile\033[0m '$relativePath' \033[31mnot valided\033[0m.\n";
						} else {
							echo "\033[32mV\033[0m \033[93mFile\033[0m '$relativePath' \033[32mvalided\033[0m.\n";
						}
					}
				} elseif (is_dir($filePath)) {
					$folderName = ucfirst($file);

					if (isset($commandDir) && $commandDir === '-onlyFile') {
						echo "";
					} else {
						if ($file !== $folderName) {
							echo "\033[31mX\033[0m \033[97mDirectory\033[0m '$relativePath' \033[31mnot valided\033[0m. (Must have a Capital letter at the beginning)\n";
						} else {
							echo "\033[32mV\033[0m \033[97mDirectory\033[0m '$relativePath' \033[32mvalided\033[0m.\n";
						}
					}

					exploreDirectory($filePath, $command, $commandDir, $fileCount, $relativePath);
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
}

$startTime = microtime(true);
$directoryPath = $argv[1];
$command = $argv[2];
$commandDir = $argv[3];
$fileCount = 0;
exploreDirectory($directoryPath, $command, $commandDir, $fileCount);
$endTime = microtime(true);
$executionTime = $endTime - $startTime;

echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";
echo "Total of files and folders scanned: \033[32m$fileCount\033[0m\n";
echo "Execution time : \033[32m$executionTime secondes\033[0m\n";
echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";
