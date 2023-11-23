<?php

$path = $argv[1] = $argv[2];
$argThree = $argv[3];

echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n\n";

function explorePath($path) {
	if (!isset($path)) {
		echo "\033[31mPlease indicate the path of the folder or file to analyze.\033[0m\n";
		echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";
		exit;
	}

	if (is_file($path)) {
		exploreFile($path);
	} elseif (is_dir($path)) {
		$files = scandir($path);
		foreach ($files as $file) {
			if ($file === "node_modules" || $file === "vendor" || $file === "build" || $file === "public") {
				echo "\033[35mV\033[0m \033[93mFolder\033[0m '$file' \033[35mwas skip automaticaly\033[0m\n";
				continue;
			}
			if ($file != "." && $file != "..") {
				$filePath = $path . '/' . $file;
				if (is_dir($filePath)) {
					validateFolder($filePath);
				}
				explorePath($filePath);
			}
		}
	}
}

function validateFolder($folderPath) {
	$folderName = basename($folderPath);

	if (!preg_match('/^([[:lower:]]+)[A-Z][a-zA-Z]+|^[A-Z][a-z]+$/', $folderName)) {
		echo "\033[31mX\033[0m \033[93mFolder\033[0m '$folderPath' \033[31mnot validated\033[0m.\n";
	} else {
		echo "\033[32mV\033[0m \033[93mFolder\033[0m '$folderPath' \033[32mvalidated\033[0m.\n";
	}
}

function exploreFile($path) {
	if (!isset($path)) {
		echo "\033[31mPlease indicate the path of the folder or file to analyze.\033[0m\n";
		echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";
		exit;
	}
	$passedFiles = [
		'App.test.js',
		".gitignore",
		"webpack.mix.js",
		"gulpfile.js",
		"Gruntfile.js",
		"App.test.js",
	];

	foreach ($passedFiles as $passedFile) {
		if (str_contains($path, $passedFile)) {
			echo "\033[35mV\033[0m \033[93mFile\033[0m '$path' \033[35mwas skip automaticaly\033[0m\n";
			continue;
		}
	}

	$fileNameParts = explode('.', basename($path));
	$baseName = reset($fileNameParts);

	if (strtolower($baseName) === 'index') {
		echo "\033[35mV\033[0m \033[93mFile\033[0m '$path' \033[35mwas skip automaticaly\033[0m\n";
	} elseif (!preg_match('/^([[:lower:]]+)[A-Z][a-zA-Z]+|^[A-Z][a-z]+$/', $baseName)) {
		echo "\033[31mX\033[0m \033[93mFile\033[0m '$path' \033[31mnot validated\033[0m.\n";
	} else {
		echo "\033[32mV\033[0m \033[93mFile\033[0m '$path' \033[32mvalidated\033[0m.\n";
	}
}

$startTime = microtime(true);
$path = $argv[1];
explorePath($path);
$endTime = microtime(true);
$executionTime = round($endTime - $startTime, 2);

echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";
echo "Execution time : \033[32m$executionTime seconds\033[0m\n";
echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";

/**
 *  Creator:
 *  https://github.com/SkaikruNashoba
 * 
 *  Version
 *  1.0.3
 */
