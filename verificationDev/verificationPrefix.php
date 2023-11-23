<?php

function explorePath($path, &$argTwo, &$argThree, &$argFour) {

	if (!isset($path)) {
		echo "\033[31mPlease indicate the path of the folder or file to analyze.\033[0m\n";
		echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";
		exit;
	}

	if (is_file($path)) {
		exploreFile($path, $argTwo, $argThree, $argFour);
	} elseif (is_dir($path)) {
		$files = scandir($path);
		foreach ($files as $file) {
			if ($file != "." && $file != "..") {
				$filePath = $path . '/' . $file;
				explorePath($filePath, $argTwo, $argThree, $argFour);
			}
		}
	}
}

function exploreFile($filePath, $argTwo, $argThree, $argFour) {
	echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n\n";
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
		];
		foreach ($passedFiles as $passedFile) {
			if (str_contains($filePath, $passedFile)) {
				echo "\033[32mThe file nammed \"$filePath\" was passed automaticaly\033[0m\n";
				continue;
			}
		}

		foreach ($fileContents as $line) {
			$trimmedLine = rtrim($line);

			if (
				preg_match("/^function App\(.*\)\s*\{$/U", $trimmedLine)
				|| preg_match("/^export default App;?$/U", $trimmedLine)
			) {
				$newContents .= $trimmedLine . PHP_EOL;
				continue;
			}
			if (
				(
					preg_match("/^function ([\w]+)\(.*\)\s*\{$/U", $trimmedLine, $matches)
					|| preg_match("/^const ([\w]+)( = \(.*\) \=> )\{/U", $trimmedLine, $matches)
					|| preg_match("/^export default ([\w]+);?$/U", $trimmedLine, $matches)
				)
			) {
				if (isset($argTwo) && $argTwo === '-noEdit') {
					$listOfLine[] = $lineNumber;
				} else {
					if (str_contains($matches[1], $argFour)) {
						$newContents .= $trimmedLine . PHP_EOL;
						continue;
					} else {
						$replaceMatch = $argFour . $matches[1];
						$trimmedLine = str_replace($matches[1], $replaceMatch, $trimmedLine);
						$listOfLine[] = $lineNumber;
						echo "Modified line $lineNumber of file $filePath\n";
					}
				}
			}

			$lineNumber++;
			$newContents .= $trimmedLine . PHP_EOL;
		}

		if (!empty($listOfLine) && !in_array($filePath, $passedFiles)) {
			if (isset($argThree) && $argThree === "-noExplain") {
				echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";
				echo "Lines affected for $filePath:\n(Line where missing a semilicon or a comma)\n\n[ ";
				foreach ($listOfLine as $line) {
					echo " \033[31m" . ($line + 1) . "\033[0m,";
				}
				echo " ]\n\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n\n";
			}
		} else {
			echo "\033[32mNo line affected for $filePath\033[0m\n";
		}
		file_put_contents($filePath, $newContents);
	} else {
		echo "\033[31mFile $filePath isn't a js or php file.\033[0m\n";
	}
}

$startTime = microtime(true);

$path = $argv[1];
$argTwo = $argv[2];
$argThree = $argv[3];
$argFour = $argv[4];

explorePath($path, $argTwo, $argThree, $argFour);

$endTime = microtime(true);
$executionTime = round($endTime - $startTime, 2);

echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";
echo "Execution time: \033[32m$executionTime\033[0m seconds\n";
echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";

/**
 *  Creator :
 *  https://github.com/SkaikruNashoba
 * 
 *  Version
 *  1.0.4
 */
