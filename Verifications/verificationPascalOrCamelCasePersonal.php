<?php

if (($argv[1] === '-h' || $argv[1] === '-help' || $argv[1] === '?')) {
	echo ("\n\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n");
	echo ("\033[32mGlobal explanation of verificationPascalOrCamelCasePersonal.php\033[0m\n\n");
	echo ("This script is used to verify if the folder and file name is in PascalCase or camelCase.\n\n");
	echo ("\033[32mHow to use\033[0m:\n");
	echo ("php verificationPascalOrCamelCasePersonal.php \033[1;33m[path]\033[0m\n\n");
	echo ("[path] = path of the folder or file to analyze\n\n");
	echo ("@param string \033[1;33m\$path\033[0m\n\n");
	echo ("\033[1;32m@return cli output\033[0m\n");
	echo ("\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n");
	echo ("\033[1;31m!!! Please read README.md for more explanation !!!\033[0m\n");
	echo ("\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n\n");
	exit;
} else {
	$path = $argv[1];
};

echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n\n";

function explorePath($path) {
	if (!isset($path)) {
		echo "\033[31mPlease indicate the path of the folder or file to analyze.\033[0m\n";
		echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";
		exit;
	};

	if (is_file($path)) {
		exploreFile($path);
	} elseif (is_dir($path)) {
		$files = scandir($path);
		foreach ($files as $file) {
			if ($file === "node_modules" || $file === "vendor" || $file === "build" || $file === "public") {
				echo "\033[35mV\033[0m \033[93mFolder\033[0m '$file' \033[35mwas skip automaticaly\033[0m\n";
				continue;
			};
			if ($file != "." && $file != "..") {
				$filePath = $path . '/' . $file;
				if (is_dir($filePath)) {
					validateFolder($filePath);
				};
				explorePath($filePath);
			};
		};
	};
};

function validateFolder($folderPath) {
	$folderName = basename($folderPath);

	if (!preg_match('/^([[:lower:]]+)[A-Z][a-zA-Z]+|^[A-Z][a-z]+$/', $folderName)) {
		echo "\033[31mX\033[0m \033[93mFolder\033[0m '$folderPath' \033[31mnot validated\033[0m.\n";
	} else {
		echo "\033[32mV\033[0m \033[93mFolder\033[0m '$folderPath' \033[32mvalidated\033[0m.\n";
	};
};

function exploreFile($path) {
	if (!isset($path)) {
		echo "\033[31mPlease indicate the path of the folder or file to analyze.\033[0m\n";
		echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";
		exit;
	};
	$passedFiles = [
		'App.test.js',
		".gitignore",
		"webpack.mix.js",
		"gulpfile.js",
		"Gruntfile.js",
		"README.md",
		"package.json",
		"package-lock.json",
	];

	foreach ($passedFiles as $passedFile) {
		if (str_contains($path, $passedFile)) {
			echo "\033[35mV\033[0m \033[93mFile\033[0m '$path' \033[35mwas skip automaticaly\033[0m\n";
			return;
		};
	};

	$fileNameParts = explode('.', basename($path));
	$baseName = reset($fileNameParts);

	if (strtolower($baseName) === 'index') {
		echo "\033[35mV\033[0m \033[93mFile\033[0m '$path' \033[35mwas skip automaticaly\033[0m\n";
	} elseif (!preg_match('/^([[:lower:]]+)[A-Z][a-zA-Z]+|^[A-Z][a-z]+$/', $baseName)) {
		echo "\033[31mX\033[0m \033[93mFile\033[0m '$path' \033[31mnot validated\033[0m.\n";
	} else {
		echo "\033[32mV\033[0m \033[93mFile\033[0m '$path' \033[32mvalidated\033[0m.\n";
	};
};

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
