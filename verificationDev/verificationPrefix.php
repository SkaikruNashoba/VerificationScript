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

		foreach ($fileContents as $line) {
			if ($argThree !== "-noExplain") {
				echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";
				echo "\033[32mActual line\033[0m : " . ($lineNumber) . "\n";
				echo "\033[32mActual file\033[0m : $filePath\n";
			}

			$trimmedLine = rtrim($line);

			if (
				(
					preg_match("/function ([\w]+)\(.*\)\s*\{$/U", $trimmedLine, $matches)
					|| preg_match("/^const ([\w]+)( = \(.*\) \=> )\{/U", $trimmedLine, $matches)
					|| preg_match("/export default ([\w]+);?$/U", $trimmedLine, $matches)
				)
			) {
				if (isset($argTwo) && $argTwo === '-noEdit') {
					echo "\033[31mPotential missing prefix at this line \033[1;31m(line " . ($lineNumber + 1) . ")\033[0m\033[31m of $filePath\033[0m\n";
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

			if (isset($argThree) && $argThree !== "-noExplain") {
				echo "\033[32mActual line content\033[0m : $line";
				echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";
				echo "\033[33mNext line...\033[0m\n\n";
			}
		}

		if (!empty($listOfLine)) {
			if (isset($argThree) && $argThree === "-noExplain") {
				echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";
				echo "Lines affected for $filePath:\n\n[ ";
				foreach ($listOfLine as $line) {
					echo " \033[31m" . ($line + 1) . "\033[0m,";
				}
				echo " ]\n";
			}
		} else {
			echo "\033[32mNo line affected for $filePath\033[0m\n";
		}
		file_put_contents($filePath, $newContents);
	} else {
		echo "\n\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n\n";
		echo "\033[31mFile $filePath is not a js or php file.\033[0m\n";
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
 *  1.0.0
 */
