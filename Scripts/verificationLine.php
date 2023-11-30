<?php

if (($argv[1] === '-h' || $argv[1] === '-help' || $argv[1] === '?')) {
	echo ("\n\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n");
	echo ("\033[32mGlobal explanation of verificationLine.php\033[0m\n\n");
	echo ("This script is used to verify if the file has more than number lines (you specify the number).\n\n");
	echo ("\033[32mHow to use\033[0m:\n");
	echo ("php verificationLine.php \033[1;33m[path]\033[0m \033[33m[option] [option]\033[0m\n\n");
	echo (" [path]  = path of the folder or file to analyze\n");
	echo ("[option] = \"-noExplain\" (to not explain the process)\n");
	echo ("[option] = \"[number]\" (to indicate the number of lines)\n\n");
	echo ("@param string \033[1;33m\$path\033[0m\n");
	echo ("@param string \033[33m\$argTwo\033[0m\n");
	echo ("@param int    \033[33m\$argThree\033[0m\n\n");
	echo ("\033[1;32m@return cli output\033[0m\n");
	echo ("\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n");
	echo ("\033[1;31m!!! Please read README.md for more explanation !!!\033[0m\n");
	echo ("\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n\n");
	exit;
} else {
	$path = $argv[1];
	$command = $argv[2];
	$numLines = $argv[3];
	switch (true) {
		case (isset($numLines) && (is_nan($numLines) || $numLines <= 0)):
			echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";
			echo "\033[31mPlease indicate the number of lines. (the number must be positive and not equal to 0)\033[0m\n";
			echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";
			exit;
		case (isset($command) && (!str_contains($command, "-"))):
			echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";
			echo "\033[31mPlease indicate a valid command.\033[0m\n";
			echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";
			exit;
		case (isset($command) && !($command == '-noExplain' || $command === '-')):
			echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";
			echo "\033[31mThis command doesn't exist.\033[0m\n";
			echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";
			exit;
	}
}

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
