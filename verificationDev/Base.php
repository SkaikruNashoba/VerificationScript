<?php

/**
 * The exploreDirectory function is the base of this script. It recursively explores a directory and performs a command on each file.
 * 
 * @param string $path The path of the folder or file to analyze.
 * @param string $argTwo The second argument of the command.
 * @param string $argThree The third argument of the command.
 * 
 * @return string The result of the command.
 */
function explorePath($path, &$argTwo, &$argThree) {

	if (!isset($path)) {
		echo "\033[31mPlease indicate the path of the folder or file to analyze.\033[0m\n";
		echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";
		exit;
	}

	if (is_file($path)) {
		exploreFile($path, $argTwo, $argThree);
	} elseif (is_dir($path)) {
		$files = scandir($path);
		foreach ($files as $file) {
			if ($file != "." && $file != "..") {
				$filePath = $path . '/' . $file;
				explorePath($filePath, $argTwo, $argThree, $path);
			}
		}
	}
}

function exploreFile($path, $argTwo, $argThree) {
	// Do something for each file
}

// Start the timer
$startTime = microtime(true);

// Get the path and command from the command line arguments
$path = $argv[1];
$argTwo = $argv[2];
$argThree = $argv[3];

// Call the explorePath function
explorePath($path, $argTwo, $argThree);

// Stop the timer and calculate the execution time
$endTime = microtime(true);
$executionTime = $endTime - $startTime;

// Display the results
echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";
echo "Execution time: \033[32m$executionTime\033[0m seconds\n";
echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";

/**
 *  Creator :
 *  https://github.com/SkaikruNashoba
 * 
 *  Version
 *  1.0.0
 */
