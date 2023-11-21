<?php

$path = $argv[1];
$argTwo = $argv[2];
$argThree = $argv[3];

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

function exploreFile($filePath, $argTwo, $argThree) {
	echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n\n";
	$fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
	if ($fileExtension == 'js' || $fileExtension == 'php') {
		$fileContents = file($filePath);
		$newContents = '';
		$lineNumber = 1;
		$listOfLine = [];

		foreach ($fileContents as $line) {
			if ($argThree !== "-withoutExplain") {
				echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";
				echo "\033[32mActual line\033[0m : " . ($lineNumber) . "\n";
				echo "\033[32mActual file\033[0m : $filePath\n";
			}

			$trimmedLine = rtrim($line);

			if (
				!preg_match("/\{[^\}]+\}$|':$|;$|}}$|return \($/Um", $trimmedLine)
				&& preg_match("/'}$|\)$|}$|'$|return?[\w\W]+$|break$|exit$/Um", $trimmedLine)
			) {
				if (isset($argTwo) && $argTwo === '-noEdit') {
					echo "\033[31mPotential missing a semicolon at this line \033[1;31m(line " . ($lineNumber) . ")\033[0m\033[31m of $filePath\033[0m\n";
					$listOfLine[] = $lineNumber - 1;
				} else {
					$trimmedLine .= ';';
					$listOfLine[] = $lineNumber - 1;
					echo "Modified line $lineNumber of file $filePath\n";
				}
			}

			$lineNumber++;
			$newContents .= $trimmedLine . PHP_EOL;

			if (isset($argThree) && $argThree !== "-withoutExplain") {
				echo "\033[32mActual line content\033[0m : $line";
				echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";
				echo "\033[33mNext line...\033[0m\n\n";
			}
		}

		if (!empty($listOfLine)) {
			if (isset($argThree) && $argThree === "-withoutExplain") {
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

explorePath($path, $argTwo, $argThree);

$endTime = microtime(true);
$executionTime = round($endTime - $startTime, 2);

echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";
if (isset($argTwo) && $argTwo !== '-noEdit') {
	echo "\n\033[31mPlease check all modified files in case of a potential error during replacement\033[0m\n\n";
	echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";
}

/**
 *  Creator
 *  https://github.com/SkaikruNashoba
 * 
 *  Version
 *  1.0.0
 */
