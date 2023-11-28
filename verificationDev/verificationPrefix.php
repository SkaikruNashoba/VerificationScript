<?php

/** Global explanation
 *
 * This script is used to verify if the function name is prefixed by the prefix given in argument.
 * if the function name is not prefixed, the script will add the prefix to the function name.
 *
 * How to use:
 * php verificationPrefix.php [path] [option] [option] [prefix]
 *
 * [path] = path of the folder or file to analyze
 * [option] = -noEdit (to not edit the files)
 * [option] = -noExplain (to not explain the process)
 * [option] = -prefix (to add a prefix to the function name)
 *
 * @param string $path
 *
 * @param string $argTwo
 * @param string $argThree
 * @param string $argFour
 *
 * @return cli output
 */

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
	echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n\n";
	if (isset($argFour) && str_contains($argFour, "_")) {
		echo "\033[1;31mAn underscore was detected in the prefix, please use a camelCase or PascalCase prefix.\033[0m\n";
		return;
	};
	$fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
	if ($fileExtension == 'js' || $fileExtension == 'php') {
		$fileContents = file($filePath);
		$newContents = '';
		$lineNumber = 0;
		$listOfLine = [];
		$passedFiles = [
			'reportWebVitals.js',
			'index.js',
			'App.test.js',
			'setupTests.js',
			".gitignore",
			"webpack.mix.js",
			"gulpfile.js",
			"Gruntfile.js",
			"App.test.js",
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

				case preg_match("/^\s*([a-zA-Z0-9]*[^\s*])\(.*\);?$/", $trimmedLine, $matches):
					if (isset($argThree) && $argThree !== "-noExplain") {
						echo "case 4 | prefix: " . $argFour . " | line: " . ($lineNumber + 1) . "\n";
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
				echo "Lines affected for $filePath:\n(Don't have a prefix)\n\n[ ";
				foreach ($listOfLine as $line) {
					echo " \033[31m" . ($line + 1) . "\033[0m,";
				};
				echo " ]\n\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n\n";
			};
		} else {
			echo "\033[32mV\033[0m \033[93mFile\033[0m '$filePath' \033[32mvalidated\033[0m.\n";
		};
		file_put_contents($filePath, $newContents);
	} else {
		echo "\033[31mX\033[0m \033[93mFile\033[0m $filePath isn't a js or php file.\033[0m\n";
	};
};

$startTime = microtime(true);
$path = $argv[1];
$argTwo = $argv[2];
$argThree = $argv[3];
$argFour = $argv[4];

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
 *  1.0.5
 */
