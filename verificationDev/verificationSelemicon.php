<?php

/**
 * The exploreDirectory function is the base of this script. It recursively explores a directory and performs a command on each file.
 * 
 * @param string $directoryPath The path of the directory to explore.
 * @param string $command The command to perform on each file.
 * @param int $fileCount A reference to the total file count.
 * @param string $parentPath The relative path of the parent directory (used for recursion).
 */

function exploreDirectory($directoryPath, $argTwo, $argThree, &$fileCount, &$lineError, $parentPath = '') {
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
						$lineError = 0;
						if ($argThree === "-withoutExplain") {
							$listOfLine = [];
							foreach ($fileContents as $line) {
								$trimmedLine = rtrim($line);
								if (
									in_array(substr($trimmedLine, -1), ['}', ')', '\'', '"'])
									&& substr($trimmedLine, -2) !== '}}'
									&& !preg_match("/\{[\w]+\}*/", $trimmedLine)
									&& !preg_match("/return(?! \()/", $trimmedLine)
								) {
									$listOfLine[] = $lineNumber;
									$lineError++;
								}
								$lineNumber++;
							}
							echo "Lines affected:\n[ ";
							foreach ($listOfLine as $line) {
								echo " \033[31m$line\033[0m,";
							}
							echo " ]\n";
						} else {
							foreach ($fileContents as $line) {
								echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";
								echo "\033[32mActual line\033[0m : " . ($lineNumber) . "\n";
								echo "\033[32mActual file\033[0m : $filePath\n";
								$trimmedLine = rtrim($line);
								if (
									in_array(substr($trimmedLine, -1), ['}', ')', '\'', '"'])
									&& substr($trimmedLine, -2) !== '}}'
									&& !preg_match("/\{[\w]+\}*/", $trimmedLine)
									&& !preg_match("/return(?! \()/", $trimmedLine)
								) {
									if (isset($argTwo) && $argTwo === '-noEdit') {
										echo "\033[31mPotential missing a selemicon at this line (line " . ($lineNumber - 1) . ")\033[0m\n";
										$lineError++;
									} else {
										$trimmedLine .= ';';
										echo "Modified line $lineNumber of file $filePath\n";
										$lineError++;
									}
								}
								echo "\033[32mActual line content\033[0m : $line\n";
								$newContents .= $trimmedLine . PHP_EOL;
								$lineNumber++;
								echo "\033[33mNext line...\033[0m\n";
								echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";
							}
							file_put_contents($filePath, $newContents);
						}
					} else {
						echo "\033[31mFile $filePath is not a js or php file.\033[0m\n";
					}
				} elseif (is_dir($filePath)) {
					exploreDirectory($argTwo, $argThree, $filePath, $fileCount, $lineError, $relativePath);
				}
			}
		}
		closedir($directory);
	} else {
		echo "Cannot open directory: $directoryPath\n";
	}
}

$startTime = microtime(true);

$directoryPath = $argv[1];
$argTwo = $argv[2];
$argThree = $argv[3];
$fileCount = 0;
$lineError = 0;

exploreDirectory($directoryPath, $argTwo, $argThree, $fileCount, $lineError);

$endTime = microtime(true);
$executionTime = round($endTime - $startTime, 2);

echo "\n~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";
echo "Total files rewrite: \033[32m$fileCount\033[0m\n";
echo ($lineError === 0) ? ("Total lines where a selemicon was missing: \033[32m$lineError\033[0m\n") : ("Total lines where a selemicon was missing: \033[31m$lineError\033[0m\n");
echo "Execution time: \033[32m$executionTime\033[0m seconds\n";
echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";

/**
 * 	Creator :
 *  https://github.com/SkaikruNashoba
 * 
 * 	Helper :
 * 	https://github.com/Baptiste-R-epi
 */
