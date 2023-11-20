<?php

/**
 * The exploreDirectory function is the base of this script. It recursively explores a directory and performs a command on each file.
 * 
 * @param string $directoryPath The path of the directory to explore.
 * @param string $command The command to perform on each file.
 * @param in A reference to the total file count.
 * @param string $parentPath The relative path of the parent directory (used for recursion).
 */
$argTwo = $argv[2];
$argThree = $argv[3];
$directoryPath = $argv[1];


function exploreDirectory($directoryPath, &$argTwo, &$argThree, $parentPath = '') {
	if (!isset($directoryPath)) {
		echo "\033[31mPlease indicate the path of the folder to analyze.\033[0m\n";
		echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";
		exit;
	}
	$directory = opendir($directoryPath);

	if ($directory) {
		while (($file = readdir($directory)) !== false) {
			if ($file != "." && $file != "..") {
				$filePath = $directoryPath . '/' . $file;
				$relativePath = $parentPath . '/' . $file;

				if (is_file($filePath)) {
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
									echo "\033[31mPotential missing a semicolon at this line \033[1;31m(line " . ($lineNumber) . ")\033[0m\033[31m of $relativePath\033[0m\n";
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
						if (!empty($listOfLine))
							if (isset($argThree) && $argThree === "-withoutExplain") {
								echo "\n\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";
								echo "Lines affected for $relativePath:\n\n[ ";
								foreach ($listOfLine as $line) {
									echo " \033[31m" . ($line + 1) . "\033[0m,";
								}
								echo " ]\n";
							}
						file_put_contents($filePath, $newContents);
					} else {
						echo "\033[31mFile $filePath is not a js or php file.\033[0m\n";
					}
				} elseif (is_dir($filePath)) {
					exploreDirectory($directoryPath, $argTwo, $argThree, $filePath, $relativePath);
				}
			}
		}
		closedir($directory);
	} else {
		echo "Cannot open directory: $directoryPath\n";
	}
}

$startTime = microtime(true);

exploreDirectory($directoryPath, $argTwo, $argThree);

$endTime = microtime(true);
$executionTime = round($endTime - $startTime, 2);

echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";
if (isset($argTwo) && $argTwo !== '-noEdit') {
	echo "\n\033[31mPlease check all modified files in case of a potential error during replacement\033[0m\n\n";
}
echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";
/**
 * 	Creator
 *  https://github.com/SkaikruNashoba
 */
