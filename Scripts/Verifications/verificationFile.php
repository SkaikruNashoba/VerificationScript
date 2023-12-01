<?php

if (($argv[1] === '-h' || $argv[1] === '-help' || $argv[1] === '?')) {
	echo ("\n\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n");
	echo ("\033[32mGlobal explanation of verificationFile.php\033[0m\n\n");
	echo ("This script is used to check if all semicolons or comma are present in the files of a project.\n");
	echo ("Also check if there is no carriage return on 3 lines in a row\n\n");
	echo ("\033[32mHow to use\033[0m:\n");
	echo ("php verificationSemicolon.php \033[1;33m[path]\033[0m \033[33m[option] [option]\033[0m\n\n");
	echo (" [path]  = path of the folder or file to analyze\n");
	echo ("[option] = \"-noEdit\" (to not edit the files)\n");
	echo ("[option] = \"-noExplain\" (to not explain the process)\n\n");
	echo ("@param string \033[1;33m\$path\033[0m\n");
	echo ("@param string \033[33m\$argTwo\033[0m\n");
	echo ("@param string \033[33m\$argThree\033[0m\n\n");
	echo ("\033[1;32m@return cli output\033[0m\n");
	echo ("\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n");
	echo ("\033[1;31m!!! Please read README.md for more explanation !!!\033[0m\n");
	echo ("\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n\n");
	exit;
} else {
	$path = $argv[1];
	$argTwo = $argv[2];
	$argThree = $argv[3];
	switch (true) {
		case (isset($argTwo) && !($argTwo === '-noEdit' || $argTwo === '-')):
			echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";
			echo "\033[31mPlease indicate a valid command for 1st option.\033[0m\n";
			echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";
			exit;
		case (isset($argThree) && !($argThree === '-noExplain' || $argThree === '-')):
			echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";
			echo "\033[31mPlease indicate a valid command for 2nd option.\033[0m\n";
			echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";
			exit;
	};
};

if (isset($argThree) && $argThree !== "-noExplain") {
	echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";
};

function explorePath($path, &$argTwo, &$argThree) {
	if (!isset($path)) {
		echo "\033[31mPlease indicate the path of the folder or file to analyze.\033[0m\n";
		echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";
		exit;
	};

	if (is_file($path)) {
		exploreFile($path, $argTwo, $argThree);
	} elseif (is_dir($path)) {
		$dirs = scandir($path);
		foreach ($dirs as $dir) {
			if ($dir === "node_modules" || $dir === "vendor" || $dir === "build" || $dir === "public") {
				echo "\033[35mFolder \"$dir\" was skip automatically.\033[0m\n";
				continue;
			};
			if ($dir !== "." && $dir !== "..") {
				$dirPath = $path . '/' . $dir;
				explorePath($dirPath, $argTwo, $argThree, $path);
			};
		};
	};
};

function exploreFile($filePath, $argTwo, $argThree) {
	$fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
	if ($fileExtension === 'js' || $fileExtension === 'php') {
		$fileContents = file($filePath);
		$newContents = '';
		$lineNumber = 0;
		$listOfLine = [];
		$blockOfCodeSkipped = false;
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

		if (isset($argThree) && $argThree !== "-noExplain") {
			echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";
			echo "(LINE) | (MISS) | (CASE)\n";
			echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";
		};

		foreach ($fileContents as $line) {
			$trimmedLine = rtrim($line);
			$matched = false;

			/* Start skip a part of code */
			if (
				preg_match("/^\s*<style\s*.*>$|^\s*\/\*(\*?)\s*(.*[^\*\/])?$|^\s*if\s*\(?$/U", $trimmedLine)
				|| (preg_match("/^\s*<>$/U", $trimmedLine) && preg_match("/^\s*return\s*\($/U", $fileContents[$lineNumber - 1]))
			) {
				if (isset($argThree) && $argThree !== "-noExplain") {
					echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";
					echo "\033[32m" . ($lineNumber + 1) . " | Start skipping " . trim($trimmedLine) . "\n";
				};
				$newContents .= $trimmedLine . PHP_EOL;
				$lineNumber++;
				$blockOfCodeSkipped = true;
				continue;
			};
			if (
				$blockOfCodeSkipped
				&&
				(
					(preg_match("/^\s*.*\*\/$/U", $trimmedLine)) ||
					(preg_match("/^\s*<\/>$/U", $trimmedLine) && preg_match("/^\s*<\/.*>$/U", $fileContents[$lineNumber - 1])) ||
					(preg_match("/^\s*\)\s*\{$/U", $trimmedLine)) ||
					(preg_match("/^\s*<\/style>$/U", $trimmedLine))
				)
			) {
				if (isset($argThree) && $argThree !== "-noExplain") {
					echo "\033[32m" . ($lineNumber + 1) . " | End skipping " . trim($trimmedLine) . "\n";
					echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";
				};
				$newContents .= $trimmedLine . PHP_EOL;
				$lineNumber++;
				$blockOfCodeSkipped = false;
				continue;
			};
			if ($blockOfCodeSkipped) {
				$newContents .= $trimmedLine . PHP_EOL;
				$lineNumber++;
				continue;
			};
			/* End skip a part of code */

			switch (true) {
				case preg_match("/^\s*\)$/U", $trimmedLine) && preg_match("/^\s*}\)$/U", $fileContents[$lineNumber - 1]):
					if (isset($argThree) && $argThree !== "-noExplain") {
						echo ("\033[31m" . $lineNumber + 1 . " | (;) | 1\033[0m\n");
					};
					if (isset($argTwo) && $argTwo !== '-noEdit') {
						$trimmedLine .= ';';
					};
					$newContents .= $trimmedLine . PHP_EOL;
					$lineNumber++;
					continue 2;
				case preg_match("/^\s*}, .*?\)$/U", $trimmedLine) && preg_match("/^\s*\)$/U", $fileContents[$lineNumber - 1]):
					if (isset($argThree) && $argThree !== "-noExplain") {
						echo ("\033[31m" . $lineNumber + 1 . " | (;) | 2\033[0m\n");
					};
					if (isset($argTwo) && $argTwo !== '-noEdit') {
						$trimmedLine .= ';';
					};
					$newContents .= $trimmedLine . PHP_EOL;
					$lineNumber++;
					continue 2;
				case preg_match("/^\s*\".*?\"\}/U", $trimmedLine) && preg_match("/^\s*\".*?\"\s*?\+$/U", $fileContents[$lineNumber - 1]):
					if (isset($argThree) && $argThree !== "-noExplain") {
						echo ("\033[32m" . $lineNumber + 1 . " | (passed) | 3\033[0m\n");
					};
					$matched = true;
					break;
				case preg_match("/^\s*\}$/U", $trimmedLine) && preg_match("/^\s*:\s*\"?.*\"?$/U", $fileContents[$lineNumber - 1]) && preg_match("/^\s*\?\s*\".*\"$/U", $fileContents[$lineNumber - 2]):
					if (isset($argThree) && $argThree !== "-noExplain") {
						echo ("\033[32m" . $lineNumber + 1 . " | (passed) | 4\033[0m\n");
					};
					$matched = false;
					break;
				case preg_match("/^\s*.*:\s*\".*\"$/U", $trimmedLine, $matches) && preg_match("/^\s*.*:\s*\".*\",$/U", $fileContents[$lineNumber - 1]):
					if (isset($argThree) && $argThree !== "-noExplain") {
						echo ("\033[1;31m" . $lineNumber + 1 . " | (,) | 5\033[0m\n");
					};
					if (isset($argTwo) && $argTwo !== '-noEdit') {
						$trimmedLine .= ',';
					};
					$listOfLine[] = $lineNumber;
					$newContents .= $trimmedLine . PHP_EOL;
					$lineNumber++;
					$matched = false;
					continue 2;
				case preg_match("/^\s*\}$/U", $trimmedLine) &&
					preg_match("/^\s*.*:\s*\".*\",?$/U", $fileContents[$lineNumber - 1]):
					if (isset($argThree) && $argThree !== "-noExplain") {
						echo ("\033[1;31m" . $lineNumber + 1 . " | (,) | 6\033[0m\n");
					};
					if (isset($argTwo) && $argTwo !== '-noEdit') {
						$trimmedLine .= ',';
					};
					$listOfLine[] = $lineNumber;
					$newContents .= $trimmedLine . PHP_EOL;
					$lineNumber++;
					$matched = false;
					continue 2;
				case preg_match("/^\s*\}$/U", $trimmedLine) && preg_match("/^\s*\},$/U", $fileContents[$lineNumber - 1]):
					if (isset($argThree) && $argThree !== "-noExplain") {
						echo ("\033[1;31m" . $lineNumber + 1 . " | (,) | 7\033[0m\n");
					};
					if (isset($argTwo) && $argTwo !== '-noEdit') {
						$trimmedLine .= ',';
					};
					$listOfLine[] = $lineNumber;
					$newContents .= $trimmedLine . PHP_EOL;
					$lineNumber++;
					$matched = false;
					continue 2;
				case preg_match("/^\s*.*:\s*\"?.*\"$/U", $trimmedLine) && preg_match("/^\s*\".*\":\s*\{/U", $fileContents[$lineNumber - 1]):
					if (isset($argThree) && $argThree !== "-noExplain") {
						echo ("\033[1;31m" . $lineNumber + 1 . " | (,) | 8\033[0m\n");
					};
					if (isset($argTwo) && $argTwo !== '-noEdit') {
						$trimmedLine .= ',';
					};
					$listOfLine[] = $lineNumber;
					$newContents .= $trimmedLine . PHP_EOL;
					$lineNumber++;
					$matched = false;
					continue 2;
				case preg_match("/^\s*\}$/U", $trimmedLine) && preg_match("/^\s*\};$/U", $fileContents[$lineNumber - 1]):
					if (isset($argThree) && $argThree !== "-noExplain") {
						echo ("\033[31m" . $lineNumber + 1 . " | (;) | 9\033[0m\n");
					};
					if (isset($argTwo) && $argTwo !== '-noEdit') {
						$trimmedLine .= ';';
					};
					$listOfLine[] = $lineNumber;
					$newContents .= $trimmedLine . PHP_EOL;
					$lineNumber++;
					$matched = false;
					continue 2;
				case preg_match("/^\s*\}$/U", $trimmedLine) && preg_match("/^\s*\)(;?)$/U", $fileContents[$lineNumber - 1]) && preg_match("/^\s*<\/>$/U", $fileContents[$lineNumber - 2]):
					if (isset($argThree) && $argThree !== "-noExplain") {
						echo ("\033[31m" . $lineNumber + 1 . " | (;) | 10\033[0m\n");
					};
					if (isset($argTwo) && $argTwo !== '-noEdit') {
						$trimmedLine .= ';';
					};
					$listOfLine[] = $lineNumber;
					$newContents .= $trimmedLine . PHP_EOL;
					$lineNumber++;
					$matched = false;
					continue 2;
				case preg_match("/^\s*\}$/U", $trimmedLine) && preg_match("/^\s*(.*)\"?(\)|(\)\]);?)$/U", $fileContents[$lineNumber - 1]):
					if (isset($argThree) && $argThree !== "-noExplain") {
						echo ("\033[31m" . $lineNumber + 1 . " | (;) | 11\033[0m\n");
					};
					if (isset($argTwo) && $argTwo !== '-noEdit') {
						$trimmedLine .= ';';
					};
					$listOfLine[] = $lineNumber;
					$newContents .= $trimmedLine . PHP_EOL;
					$lineNumber++;
					$matched = false;
					continue 2;
				case preg_match("/^\s*\}$/U", $trimmedLine) && preg_match("/^\s*\}$/U", $fileContents[$lineNumber - 1]) && preg_match("/^\s*(.*)\"?\);?$/U", $fileContents[$lineNumber - 2]):
					if (isset($argThree) && $argThree !== "-noExplain") {
						echo ("\033[31m" . $lineNumber + 1 . " | (;) | 12\033[0m\n");
					};
					if (isset($argTwo) && $argTwo !== '-noEdit') {
						$trimmedLine .= ';';
					};
					$listOfLine[] = $lineNumber;
					$newContents .= $trimmedLine . PHP_EOL;
					$lineNumber++;
					$matched = false;
					continue 2;
				case preg_match("/^\s*\}$/U", $trimmedLine) && preg_match("/^\s*.*\);?$/U", $fileContents[$lineNumber - 1]):
					if (isset($argThree) && $argThree !== "-noExplain") {
						echo ("\033[31m" . $lineNumber + 1 . " | (;) | 13\033[0m\n");
					};
					if (isset($argTwo) && $argTwo !== '-noEdit') {
						$trimmedLine .= ';';
					};
					$listOfLine[] = $lineNumber;
					$newContents .= $trimmedLine . PHP_EOL;
					$lineNumber++;
					$matched = false;
					continue 2;
				case preg_match("/^\s*\}$/U", $trimmedLine) && preg_match("/^\s*.*;?$/U", $fileContents[$lineNumber - 1]):
					if (isset($argThree) && $argThree !== "-noExplain") {
						echo ("\033[31m" . $lineNumber + 1 . " | (;) | 14\033[0m\n");
					};
					if (isset($argTwo) && $argTwo !== '-noEdit') {
						$trimmedLine .= ';';
					};
					$listOfLine[] = $lineNumber;
					$newContents .= $trimmedLine . PHP_EOL;
					$lineNumber++;
					$matched = false;
					continue 2;

					/**
					 * to remove all \n please run the command until there is no more line in CLI with 'carriage return'
					 */
					/* special cases for delete excess "\n" */
				case preg_match("/^\n*$/U", $trimmedLine) && preg_match("/^\s*<.*>\s*$/U", $fileContents[$lineNumber - 1]):
					if (isset($argThree) && $argThree !== "-noExplain") {
						echo ("\033[35m" . $lineNumber + 1 . " | (carriage return) | 15\033[0m\n");
					};
					if (isset($argTwo) && $argTwo !== '-noEdit') {
						continue 2;
					};
				case preg_match("/^\n*$/U", $trimmedLine) && preg_match("/^\s*(function|foreach|while)\s*.*\{$/U", $fileContents[$lineNumber - 1]):
					if (isset($argThree) && $argThree !== "-noExplain") {
						echo ("\033[35m" . $lineNumber + 1 . " | (carriage return) | 16\033[0m\n");
					};
					if (isset($argTwo) && $argTwo !== '-noEdit') {
						continue 2;
					};
				case preg_match("/^\n*$/U", $trimmedLine) && preg_match("/^\n*$/U", $fileContents[$lineNumber - 1]) && preg_match("/^.*$|^\n*$/U", $fileContents[$lineNumber - 2]):
					if (isset($argThree) && $argThree !== "-noExplain") {
						echo ("\033[35m" . $lineNumber + 1 . " | (carriage return) | 18\033[0m\n");
					};
					if (isset($argTwo) && $argTwo !== '-noEdit') {
						continue 2;
					};

					/* special case when a line or multiple line have a comment */
				case preg_match("/^\s*\/\/\s*(.*)?$|^\s*\/\*\s*(.*)\*\//U", $trimmedLine):
					if (isset($argThree) && $argThree !== "-noExplain") {
						echo ("\033[32m" . $lineNumber + 1 . " | (auto skip) | 19\033[0m\n");
					};
					$newContents .= $trimmedLine . PHP_EOL;
					$lineNumber++;
					$matched = false;
					continue 2;
			};

			if ($matched === true) {
				$listOfLine[] = $lineNumber;
				$newContents .= $trimmedLine . PHP_EOL;
				$lineNumber++;
				continue;
			};

			if (
				!preg_match("/\{[^\}]+\}$|':$|:$|\{$|;$|}}$|return \($|\),$|{`[\w\W\d]*`}$|^\s*\}\)|^\s*[\w]*=({\"?.*\"?})$/U", $trimmedLine)
				&& preg_match("/'}$|return\s*.*?$|break$|exit$|(from|require) ['\"]?.*['\"]?$|(export) .*$|^\s*(.*\))$/U", $trimmedLine)
			) {
				if (isset($argTwo) && $argTwo === '-noEdit') {
					if (isset($argThree) && $argThree !== "-noExplain") {
						echo ("\033[31m" . $lineNumber + 1 . " | (;) | 20\033[0m\n");
					};
					$listOfLine[] = $lineNumber;
				} else {
					$trimmedLine .= ';';
					$listOfLine[] = $lineNumber;
					echo "Modified line $lineNumber of file $filePath\n";
				};
			};
			$newContents .= $trimmedLine . PHP_EOL;
			$lineNumber++;
		};

		if (!empty($listOfLine)) {
			echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";
			echo "Lines affected for $filePath:\n(Line where missing a semilicon or a comma)\n\n[ ";
			foreach ($listOfLine as $line) {
				echo " \033[31m" . ($line + 1) . "\033[0m,";
			};
			echo " ]\n\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n\n";
		} else {
			echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";
			echo "\033[32mV\033[0m \033[93mFile\033[0m '$filePath' \033[32mvalidated\033[0m.\n";
		};
		file_put_contents($filePath, $newContents);
	} else {
		echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";
		echo "\033[31mX\033[0m \033[93mFile\033[0m $filePath \033[34misn't a js or php file\033[0m\n";
	};
};

$startTime = microtime(true);
explorePath($path, $argTwo, $argThree);

$endTime = microtime(true);
$executionTime = round($endTime - $startTime, 2);
echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";
if (isset($argThree) && $argThree !== "-noExplain") {
	echo "\033[32mExecution time: $executionTime seconds\033[0m\n";
	echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";
};
if (isset($argTwo) && $argTwo !== '-noEdit') {
	echo "\033[31mPlease check all modified files in case of a potential error during replacement\033[0m\n";
	echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";
};

/**
 *  Creator:
 *  https://github.com/SkaikruNashoba
 *
 *  Contributors:
 *  https://github.com/Baptiste-R-epi
 *
 *  Version
 *  1.2.4
 */
