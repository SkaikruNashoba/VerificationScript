<?php

$path = $argv[1];
$argTwo = $argv[2];
$argThree = $argv[3];
if (isset($argThree) && $argThree !== "-noExplain") {
	echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";
}
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
			if ($file === "node_modules" || $file === "vendor" || $file === "build" || $file === "public") {
				echo "\033[35mFolder \"$file\" was skip automatically.\033[0m\n";
				continue;
			}
			if ($file !== "." && $file !== "..") {
				$filePath = $path . '/' . $file;
				explorePath($filePath, $argTwo, $argThree, $path);
			}
		}
	}
}

function exploreFile($filePath, $argTwo, $argThree) {
	$fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
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
			}
		}

		if (isset($argThree) && $argThree !== "-noExplain") {
			echo "(case) | (miss) | (line)\n";
			echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";
		}

		foreach ($fileContents as $line) {
			$trimmedLine = rtrim($line);
			$matched = false;

			switch (true) {
				case preg_match("/^\n*$/U", $trimmedLine) && preg_match("/^\n*$/U", $fileContents[$lineNumber - 1]) && preg_match("/^\n*$/U", $fileContents[$lineNumber - 2]):
					/* special case for delete excess "\n" */
					if (isset($argThree) && $argThree !== "-noExplain") {
						echo ("case -1 (chariot) (" . $lineNumber + 1 . ")\n");
					}
					if (isset($argTwo) && $argTwo !== '-noEdit') {
						continue 2;
					}
				case preg_match("/^\s*\)$/U", $trimmedLine) && preg_match("/^\s*}\)/U", $fileContents[$lineNumber - 1]):
					if (isset($argThree) && $argThree !== "-noExplain") {
						echo ("case 0 (passed) (" . $lineNumber + 1 . ")\n");
					}
					$matched = true;
					break;
				case preg_match("/^\s*}, [\w\W]*?\)$/U", $trimmedLine) && preg_match("/^\s*\)$/U", $fileContents[$lineNumber - 1]):
					if (isset($argThree) && $argThree !== "-noExplain") {
						echo ("case 1 (passed) (" . $lineNumber + 1 . ")\n");
					}
					$matched = true;
					break;
				case preg_match("/^\s*\".*?\"\}/U", $trimmedLine) && preg_match("/^\s*\".*?\"\s*?\+$/U", $fileContents[$lineNumber - 1]):
					if (isset($argThree) && $argThree !== "-noExplain") {
						echo ("case 2 (passed) (" . $lineNumber + 1 . ")\n");
					}
					$matched = true;
					break;
				case preg_match("/^\s*\}$/U", $trimmedLine) && preg_match("/^\s*:\s*\"?.*\"?$/U", $fileContents[$lineNumber - 1]) && preg_match("/^\s*\?\s*\".*\"$/U", $fileContents[$lineNumber - 2]):
					if (isset($argThree) && $argThree !== "-noExplain") {
						echo ("case 3 (passed) (" . $lineNumber + 1 . ")\n");
					}
					$matched = false;
					break;
				case preg_match("/^\s*[\w\W]*:\s*\".*\"$/U", $trimmedLine, $matches) && preg_match("/^\s*[\w\W]*:\s*\".*\",$/U", $fileContents[$lineNumber - 1]):
					if (isset($argThree) && $argThree !== "-noExplain") {
						echo ("case 4  (,) (" . $lineNumber + 1 . ")\n");
					}
					if (isset($argTwo) && $argTwo !== '-noEdit') {
						$trimmedLine .= ',';
					}
					$listOfLine[] = $lineNumber;
					$newContents .= $trimmedLine . PHP_EOL;
					$lineNumber++;
					$matched = false;
					continue 2;
				case preg_match("/^\s*\}$/U", $trimmedLine) && preg_match("/^\s*[\w\W]*:\s*\".*\",?$/U", $fileContents[$lineNumber - 1]):
					if (isset($argThree) && $argThree !== "-noExplain") {
						echo ("case 5  (,) (" . $lineNumber + 1 . ")\n");
					}
					if (isset($argTwo) && $argTwo !== '-noEdit') {
						$trimmedLine .= ',';
					}
					$listOfLine[] = $lineNumber;
					$newContents .= $trimmedLine . PHP_EOL;
					$lineNumber++;
					$matched = false;
					continue 2;
				case preg_match("/^\s*\}$/U", $trimmedLine) && preg_match("/^\s*\},$/U", $fileContents[$lineNumber - 1]):
					if (isset($argThree) && $argThree !== "-noExplain") {
						echo ("case 6  (,) (" . $lineNumber + 1 . ")\n");
					}
					if (isset($argTwo) && $argTwo !== '-noEdit') {
						$trimmedLine .= ',';
					}
					$listOfLine[] = $lineNumber;
					$newContents .= $trimmedLine . PHP_EOL;
					$lineNumber++;
					$matched = false;
					continue 2;
				case preg_match("/^\s*.*:\s*\"?.*\"$/U", $trimmedLine) && preg_match("/^\s*\".*\":\s*\{/U", $fileContents[$lineNumber - 1]):
					if (isset($argThree) && $argThree !== "-noExplain") {
						echo ("case 7  (,) (" . $lineNumber + 1 . ")\n");
					}
					if (isset($argTwo) && $argTwo !== '-noEdit') {
						$trimmedLine .= ',';
					}
					$listOfLine[] = $lineNumber;
					$newContents .= $trimmedLine . PHP_EOL;
					$lineNumber++;
					$matched = false;
					continue 2;
				case preg_match("/^\s*\}$/U", $trimmedLine) && preg_match("/^\s*\};$/U", $fileContents[$lineNumber - 1]):
					if (isset($argThree) && $argThree !== "-noExplain") {
						echo ("case 8  (;) (" . $lineNumber + 1 . ")\n");
					}
					if (isset($argTwo) && $argTwo !== '-noEdit') {
						$trimmedLine .= ';';
					}
					$listOfLine[] = $lineNumber;
					$newContents .= $trimmedLine . PHP_EOL;
					$lineNumber++;
					$matched = false;
					continue 2;
				case preg_match("/^\s*\}$/U", $trimmedLine) && preg_match("/^\s*\)(;?)$/U", $fileContents[$lineNumber - 1]) && preg_match("/^\s*<\/>$/U", $fileContents[$lineNumber - 2]):
					if (isset($argThree) && $argThree !== "-noExplain") {
						echo ("case 9  (;) (" . $lineNumber + 1 . ")\n");
					}
					if (isset($argTwo) && $argTwo !== '-noEdit') {
						$trimmedLine .= ';';
					}
					$listOfLine[] = $lineNumber;
					$newContents .= $trimmedLine . PHP_EOL;
					$lineNumber++;
					$matched = false;
					continue 2;
				case preg_match("/^\s*\}$/U", $trimmedLine) && preg_match("/^\s*(.*)\"?(\)|(\)\]);?)$/U", $fileContents[$lineNumber - 1]):
					if (isset($argThree) && $argThree !== "-noExplain") {
						echo ("case 10 (;) (" . $lineNumber + 1 . ")\n");
					}
					if (isset($argTwo) && $argTwo !== '-noEdit') {
						$trimmedLine .= ';';
					}
					$listOfLine[] = $lineNumber;
					$newContents .= $trimmedLine . PHP_EOL;
					$lineNumber++;
					$matched = false;
					continue 2;
				case preg_match("/^\s*\}$/U", $trimmedLine) && preg_match("/^\s*\}$/U", $fileContents[$lineNumber - 1]) && preg_match("/^\s*(.*)\"?\);?$/U", $fileContents[$lineNumber - 2]):
					if (isset($argThree) && $argThree !== "-noExplain") {
						echo ("case 11 (;) (" . $lineNumber + 1 . ")\n");
					}
					if (isset($argTwo) && $argTwo !== '-noEdit') {
						$trimmedLine .= ';';
					}
					$listOfLine[] = $lineNumber;
					$newContents .= $trimmedLine . PHP_EOL;
					$lineNumber++;
					$matched = false;
					continue 2;
				case preg_match("/^\s*\}$/U", $trimmedLine) && preg_match("/^\s*.*\);?$/U", $fileContents[$lineNumber - 1]):
					if (isset($argThree) && $argThree !== "-noExplain") {
						echo ("case 12 (;) (" . $lineNumber + 1 . ")\n");
					}
					if (isset($argTwo) && $argTwo !== '-noEdit') {
						$trimmedLine .= ';';
					}
					$listOfLine[] = $lineNumber;
					$newContents .= $trimmedLine . PHP_EOL;
					$lineNumber++;
					$matched = false;
					continue 2;
				case preg_match("/^\s*\}$/U", $trimmedLine) && preg_match("/^\s*.*;?$/U", $fileContents[$lineNumber - 1]):
					if (isset($argThree) && $argThree !== "-noExplain") {
						echo ("case 13 (;) (" . $lineNumber + 1 . ")\n");
					}
					if (isset($argTwo) && $argTwo !== '-noEdit') {
						$trimmedLine .= ';';
					}
					$listOfLine[] = $lineNumber;
					$newContents .= $trimmedLine . PHP_EOL;
					$lineNumber++;
					$matched = false;
					continue 2;
					// case preg_match("/^\s*if\s*\(.*\)$/U", $trimmedLine):
					// 	if (isset($argThree) && $argThree !== "-noExplain") {
					// 		echo ("case 14 (;) (" . $lineNumber + 1 . ")\n");
					// 	}
					// 	if (isset($argTwo) && $argTwo !== '-noEdit') {
					// 		$trimmedLine .= ';';
					// 	}
					// 	$listOfLine[] = $lineNumber;
					// 	$newContents .= $trimmedLine . PHP_EOL;
					// 	$lineNumber++;
					// 	$matched = false;
					// 	continue 2;
				case preg_match("/^\/\/\s*?.*$/U", $trimmedLine):
					/* special case when a line start by "//" */
					if (isset($argThree) && $argThree !== "-noExplain") {
						echo ("case 15 (passed) (" . $lineNumber + 1 . ")\n");
					}
					$newContents .= $trimmedLine . PHP_EOL;
					$lineNumber++;
					$matched = false;
					continue 2;
			}

			if ($matched === true) {
				$listOfLine[] = $lineNumber;
				$newContents .= $trimmedLine . PHP_EOL;
				$lineNumber++;
				continue;
			}

			if (
				!preg_match("/\{[^\}]+\}$|':$|\{$|;$|}}$|return \($|\),$|{`[\w\W\d]*`}$|^\s*\}\)|^\s*[\w]*=({\"?.*\"?})$|^(?!(\/))(.*\))$/U", $trimmedLine)
				&& preg_match("/'}$|return?[\w\W]+$|break$|exit$|(from|require) ['\"]?[\w\W]*['\"]?$|(export) [\w\W]*$/U", $trimmedLine)
			) {
				if (isset($argTwo) && $argTwo === '-noEdit') {
					if (isset($argThree) && $argThree !== "-noExplain") {
						echo ("case 16 (;) (" . $lineNumber + 1 . ")\n");
					}
					$listOfLine[] = $lineNumber;
				} else {
					$trimmedLine .= ';';
					$listOfLine[] = $lineNumber;
					echo "Modified line $lineNumber of file $filePath\n";
				}
			}
			// else {
			// 	var_dump("case 13 (;) $lineNumber");
			// }
			$newContents .= $trimmedLine . PHP_EOL;
			$lineNumber++;
		}

		if (!empty($listOfLine)) {
			echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";
			echo "Lines affected for $filePath:\n(Line where missing a semilicon or a comma)\n\n[ ";
			foreach ($listOfLine as $line) {
				echo " \033[31m" . ($line + 1) . "\033[0m,";
			}
			echo " ]\n\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n\n";
		} else {
			echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";
			echo "\033[32mV\033[0m \033[93mFile\033[0m '$filePath' \033[32mvalidated\033[0m.\n";
		}
		file_put_contents($filePath, $newContents);
	} else {
		echo "\033[31mX\033[0m \033[93mFile\033[0m $filePath \033[34misn't a js or php file\033[0m\n";
	}
}

$startTime = microtime(true);

explorePath($path, $argTwo, $argThree);

$endTime = microtime(true);
$executionTime = round($endTime - $startTime, 2);

echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";
echo "\033[32mExecution time: $executionTime seconds\033[0m\n";
echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";

if (isset($argTwo) && $argTwo !== '-noEdit') {
	echo "\033[31mPlease check all modified files in case of a potential error during replacement\033[0m\n";
	echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";
}

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
