<?php

if (($argv[1] === '-h' || $argv[1] === '-help' || $argv[1] === '?')) {
	echo ("\n\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n");
	echo ("\033[32mGlobal explanation of verificationPrefix.php\033[0m\n\n");
	echo ("This script is used to verify if the function name is prefixed by the prefix given in argument.\n");
	echo ("if the function name is not prefixed, the script will add the prefix to the function name.\n\n");
	echo ("How to use:\n");
	echo ("php verificationPrefix.php \033[1;33m[path]\033[0m \033[33m[option] [option] [prefix]\033[0m\n\n");
	echo (" [path]  = path of the folder or file to analyze\n");
	echo ("[option] = \"-noEdit\" (to not edit the files)\n");
	echo ("[option] = \"-noExplain\" (to not explain the process)\n");
	echo ("[option] = \"-[your_prefix]\" (to add a prefix to the function name)\n\n");
	echo ("@param string \033[1;33m\$path\033[0m\n");
	echo ("@param string \033[33m\$argTwo\033[0m\n");
	echo ("@param string \033[33m\$argThree\033[0m\n");
	echo ("@param string \033[33m\$argFour\033[0m\n\n");
	echo ("\033[1;32m@return cli output\033[0m\n");
	echo ("\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n");
	echo ("\033[1;31m!!! Please read README.md for more explanation !!!\033[0m\n");
	echo ("\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n\n");
	exit;
} else {
	$path = $argv[1];
	$argTwo = $argv[2];
	$argThree = $argv[3];
	$argFour = $argv[4];
	switch (true) {
		case ((isset($argTwo) && !($argTwo === '-noEdit' || $argTwo === '-')) || !isset($argTwo)):
			echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";
			echo "\033[31mPlease indicate a valid command for 1st option.\033[0m\n";
			echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";
			exit;
		case ((isset($argThree) && !($argThree === '-noExplain' || $argThree === '-')) || !isset($argThree)):
			echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";
			echo "\033[31mPlease indicate a valid command for 2nd option.\033[0m\n";
			echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";
			exit;
		case (!isset($argFour)):
			echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";
			echo "\033[31mPlease indicate a valid command for 2nd option.\033[0m\n";
			echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";
			exit;
	};
};

function explorePath($path, &$argTwo, &$argThree, &$argFour) {
	if (!isset($path)) {
		echo "\033[31mPlease indicate the path of the folder or file to analyze.\033[0m\n";
		echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";
		exit;
	};

	if (is_file($path)) {
		exploreFile($path, $argTwo, $argThree, $argFour);
	} elseif (is_dir($path)) {
		$files = scandir($path);
		foreach ($files as $file) {
			if ($file === "node_modules" || $file === "vendor" || $file === "build" || $file === "public") {
				echo "\033[35mV\033[0m \033[93mFolder\033[0m '$file' \033[35mwas passed automaticaly\033[0m\n";
				continue;
			};
			if ($file != "." && $file != "..") {
				$filePath = $path . '/' . $file;
				explorePath($filePath, $argTwo, $argThree, $argFour);
			};
		};
	};
};

function exploreFile($filePath, $argTwo, $argThree, $argFour) {
	$fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
	if (isset($argThree) && $argThree !== "-noExplain") {
		echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n\n";
	};
	if (isset($argFour) && str_contains($argFour, "_") && $fileExtension === 'php') {
		echo "\033[1;31mAn underscore was detected in the prefix, please use a camelCase or PascalCase prefix.\033[0m\n";
		return;
	};
	if ($fileExtension === 'js' || $fileExtension === 'php') {
		$fileContents = file($filePath);
		$newContents = '';
		$lineNumber = 0;
		$listOfLine = [];
		$passedFiles = [
			'reportWebVitals.js',
			'index.js',
			'App.test.js',
			'setupTests.js',
			'.gitignore',
			'webpack.mix.js',
			'gulpfile.js',
			'Gruntfile.js',
		];

		foreach ($passedFiles as $passedFile) {
			if (str_contains($filePath, $passedFile)) {
				echo "\033[35mV\033[0m \033[93mFile\033[0m '$filePath' \033[35mwas skip automaticaly\033[0m\n";
				continue;
			};
		};

		foreach ($fileContents as $line) {
			$trimmedLine = rtrim($line);

			if (preg_match("/^function App\(.*\)\s*\{$|^export default App;?$/U", $trimmedLine)) {
				$newContents .= $trimmedLine . PHP_EOL;
				$lineNumber++;
				continue;
			};
			if (str_contains($trimmedLine, $argFour)) {
				$newContents .= $trimmedLine . PHP_EOL;
				$lineNumber++;
				continue;
			};

			$matchResult = null;
			switch (true) {
				case preg_match("/^function ([\w]+)\(.*\)\s*\{$/", $trimmedLine, $matches):
					if (isset($argThree) && $argThree !== "-noExplain") {
						echo "case 1 | prefix: " . $argFour . " | line: " . ($lineNumber + 1) . "\n";
					};
					$matchResult = $matches[1];
					break;
				case preg_match("/^const ([\w]+)( = \(.*\) \=> )\{$/", $trimmedLine, $matches):
					if (isset($argThree) && $argThree !== "-noExplain") {
						echo "case 2 | prefix: " . $argFour . " | line: " . ($lineNumber + 1) . "\n";
					};
					$matchResult = $matches[1];
					break;
				case preg_match("/^export default ([\w]+);?$/", $trimmedLine, $matches):;
					if (isset($argThree) && $argThree !== "-noExplain") {
						echo "case 3 | prefix: " . $argFour . " | line: " . ($lineNumber + 1) . "\n";
					};
					$matchResult = $matches[1];
					break;
				case preg_match("/^\s*([a-zA-Z0-9]*[^\s*])\(.*\);?$/", $trimmedLine, $matches) && $fileExtension === 'php':
					if (isset($argThree) && $argThree !== "-noExplain") {
						echo "case 4 | prefix: " . $argFour . " | line: " . ($lineNumber + 1) . "\n";
					};
					$matchResult = $matches[1];
					break;
				case preg_match("/^.*(\w++ )\s*(from|require)\s*\"/U", $trimmedLine, $matches):
					if (isset($argThree) && $argThree !== "-noExplain") {
						echo "case 5 | prefix: " . $argFour . " | line: " . ($lineNumber + 1) . "\n";
					};
					$matchResult = $matches[1];
					break;
				case preg_match("/^\s*<Route\s*path=[\"|'].*[\"|']\s*element=\{<(.* )\s*\/>/U", $trimmedLine, $matches):
					if (isset($argThree) && $argThree !== "-noExplain") {
						echo "case 6 | prefix: " . $argFour . " | line: " . ($lineNumber + 1) . "\n";
					};
					$matchResult = $matches[1];
					break;
				case preg_match("/^\s*<(\w* )\/>$/U", $trimmedLine, $matches):
					if (isset($argThree) && $argThree !== "-noExplain") {
						echo "case 7 | prefix: " . $argFour . " | line: " . ($lineNumber + 1) . "\n";
					};
					$matchResult = $matches[1];
					break;
			};

			if (!is_null($matchResult)) {
				if (isset($argTwo) && $argTwo === '-noEdit') {
					$listOfLine[] = $lineNumber;
				} else {
					if (str_contains($matchResult, $argFour)) {
						echo "\033[32mV\033[0m \033[93mLine\033[0m " . ($lineNumber + 1) . " \033[32mwas validated\033[0m.\n";
					} else {
						$replaceMatch = $argFour . ucfirst($matchResult);
						$trimmedLine = str_replace($matchResult, $replaceMatch, $trimmedLine);
						$listOfLine[] = $lineNumber;
						echo "\033[32mModified line " . ($lineNumber + 1) . " of file $filePath\033[0m\n";
					};
				};
			};
			$lineNumber++;
			$newContents .= $trimmedLine . PHP_EOL;
		};

		if (!empty($listOfLine) && !in_array($filePath, $passedFiles)) {
			if (isset($argThree) && $argThree !== "-noExplain") {
				echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";
				echo "Lines affected for $filePath:\n(Possible no prefix)\n\n[ ";
				foreach ($listOfLine as $line) {
					echo " \033[31m" . ($line + 1) . "\033[0m,";
				};
				echo " ]\n\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n\n";
			};
		} else {
			echo "\033[32mV\033[0m \033[93mFile\033[0m '$filePath' \033[32mvalidated\033[0m\n";
		};
		file_put_contents($filePath, $newContents);
	} else {
		echo "\033[31mX\033[0m \033[93mFile\033[0m $filePath isn't a js or php file.\033[0m\n";
	};
};

$startTime = microtime(true);
explorePath($path, $argTwo, $argThree, $argFour);
$endTime = microtime(true);
$executionTime = round($endTime - $startTime, 2);
if (isset($argThree) && $argThree !== "-noExplain") {
	echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";
	echo "Execution time: \033[32m$executionTime\033[0m seconds\n";
	echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";
};

/**
 *  Creator:
 *  https://github.com/SkaikruNashoba
 *
 *  Version
 *  1.0.7
 */
