<?php

/**
 * The exploreDirectory function is the base of this script. It recursively explores a directory and performs a command on each file.
 * 
 * @param string $directoryPath The path of the directory to explore.
 * @param string $command The command to perform on each file.
 * @param int $fileCount A reference to the total file count.
 * @param string $parentPath The relative path of the parent directory (used for recursion).
 */
function exploreDirectory($directoryPath, $command, &$fileCount, $parentPath = '') {
	if (!isset($directoryPath)) {
		echo "\033[31mPlease indicate the path of the folder to analyze.\033[0m\n";
		echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";
		exit;
	}
	$directory = opendir($directoryPath);

	if ($directory) {
		while (($file = readdir($directory)) !== false) {
			if ($file != "." && $file != "..") {
				$filePath = $directoryPath . '/' . $file;
				$relativePath = $parentPath . '/' . $file;

				if (is_file($filePath)) {
					// Perform the command on the file here
				} elseif (is_dir($filePath)) {
					// If the item is a directory, recursively call this function
					exploreDirectory($filePath, $command, $fileCount, $relativePath);
				}
			}
		}
		closedir($directory);
	} else {
		echo "Cannot open directory: $directoryPath\n";
	}
}

// Start the timer
$startTime = microtime(true);

// Get the directory path and command from the command line arguments
$directoryPath = $argv[1];
$command = $argv[2];

// Initialize the file count
$fileCount = 0;

// Call the exploreDirectory function
exploreDirectory($directoryPath, $command, $fileCount);

// Stop the timer and calculate the execution time
$endTime = microtime(true);
$executionTime = $endTime - $startTime;

// Display the results
echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";
echo "Total files: \033[32m$fileCount\033[0m\n";
echo "Execution time: \033[32m$executionTime\033[0m seconds\n";
echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";
