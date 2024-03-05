<?php

$path = $argv[1];
$argTwo = $argv[2];

function explorePath($path, &$argTwo) {
    if (!isset($path)) {
        echo "\033[31mPlease indicate the path of the folder or file to analyze.\033[0m\n";
        echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";
        exit;
    };

    if (is_file($path)) {
        exploreFile($path, $argTwo);
    } elseif (is_dir($path)) {
        $dirs = scandir($path);
        foreach ($dirs as $dir) {
            if (
                $dir === "node_modules"
                || $dir === "vendor"
                || $dir === "build"
                || $dir === "public"
                || $dir === "Utils"
                || $dir === "Scripts"
                || $dir === "Regrouper"
                || $dir === "Components"
                || $dir === "Assets"
            ) {
                echo "\033[35mFolder \"$dir\" was skip automatically.\033[0m\n";
                continue;
            };
            if ($dir !== "." && $dir !== "..") {
                $dirPath = $path . '/' . $dir;
                explorePath($dirPath, $argTwo, $path);
            };
        };
    };
};


function exploreFile($filePath, $argTwo) {
    $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
    $nameFile = pathinfo($filePath, PATHINFO_BASENAME);
    $folder = pathinfo($filePath, PATHINFO_DIRNAME);
    if ($fileExtension === 'js' || $fileExtension === 'php') {
        $fileContents = file($filePath);

        $lineCount = 0;
        $newContents = '';
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

        $fileStringContents = implode("", file($filePath));
        if (!preg_match("/.*(fetchData(NoLocal|NoLocalWithToken|WithToken)?(\s|\().*)/m", $fileStringContents, $matches)) {
            // echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n\n";
            // echo "\033[35m?\033[0m \033[93mFile\033[0m '$filePath'\033[35m on folder \033[0m\033[36m$folder\033[0m \033[35mwas skip automaticaly, cause: No match found\033[0m\n";
            return;
        } else {
            echo "\n\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";
            echo "Folder : \033[36m$folder\033[0m:\n";
            echo "\033[33m------------------------------\033[0m\n";
            foreach ($fileContents as $line) {
                $trimmedLine = rtrim($line);
                $lineModified = false;
                $lineCount++;
                switch ((string) $argTwo) {
                    case "local":
                        $fetchDataNoLocal = preg_match("/^.*(fetchDataNoLocal)\s.*$/U", $trimmedLine, $matches);
                        if ($fetchDataNoLocal) {
                            $newContents .= str_replace($matches[1], "fetchData", $trimmedLine) . PHP_EOL;
                            $lineModified = true;
                            echo "Text \"\033[32m$matches[1]\033[0m\" has been replaced by \"\033[32mfetchData\033[0m\" at line n°\033[93m$lineCount\033[0m in \033[35m$nameFile\033[0m\n";
                        };
                        $fetchDataNoLocalRequest = preg_match("/^.*(fetchDataNoLocal\().*$/U", $trimmedLine, $matches);
                        if ($fetchDataNoLocalRequest) {
                            $newContents .= str_replace($matches[1], "fetchData(", $trimmedLine) . PHP_EOL;
                            $lineModified = true;
                            echo "Text \"\033[32m$matches[1]\033[0m\" has been replaced by \"\033[32mfetchData(\033[0m\" at line n°\033[93m$lineCount\033[0m in \033[35m$nameFile\033[0m\n";
                        };
                        $fetchDataNoLocalWithToken = preg_match("/^.*(fetchDataNoLocalWithToken)\s.*$/U", $trimmedLine, $matches);
                        if ($fetchDataNoLocalWithToken) {
                            $newContents .= str_replace($matches[1], "fetchDataWithToken", $trimmedLine) . PHP_EOL;
                            $lineModified = true;
                            echo "Text \"\033[32m$matches[1]\033[0m\" has been replaced by \"\033[32mfetchDataWithToken\033[0m\" at line n°\033[93m$lineCount\033[0m in \033[35m$nameFile\033[0m\n";
                        };
                        $fetchDataNoLocalWithTokenRequest = preg_match("/^.*(fetchDataNoLocalWithToken\().*$/U", $trimmedLine, $matches);
                        if ($fetchDataNoLocalWithTokenRequest) {
                            $newContents .= str_replace($matches[1], "fetchDataWithToken(", $trimmedLine) . PHP_EOL;
                            $lineModified = true;
                            echo "Text \"\033[32m$matches[1]\033[0m\" has been replaced by \"\033[32mfetchDataWithToken(\033[0m\" at line n°\033[93m$lineCount\033[0m in \033[35m$nameFile\033[0m\n";
                        };
                        break;
                    case "prod":
                        $fetchData = preg_match("/^.*(fetchData)\s.*$/U", $trimmedLine, $matches);
                        if ($fetchData) {
                            $newContents .= str_replace($matches[1], "fetchDataNoLocal", $trimmedLine) . PHP_EOL;
                            $lineModified = true;
                            echo "Text \"\033[32m$matches[1]\033[0m\" has been replaced by \"\033[32mfetchDataNoLocal\033[0m\" at line n°\033[93m$lineCount\033[0m in \033[35m$nameFile\033[0m\n";
                        };
                        $fetchDataRequest = preg_match("/^.*(fetchData\().*$/U", $trimmedLine, $matches);
                        if ($fetchDataRequest) {
                            $newContents .= str_replace($matches[1], "fetchDataNoLocal(", $trimmedLine) . PHP_EOL;
                            $lineModified = true;
                            echo "Text \"\033[32m$matches[1]\033[0m\" has been replaced by \"\033[32mfetchDataNoLocal(\033[0m\" at line n°\033[93m$lineCount\033[0m in \033[35m$nameFile\033[0m\n";
                        };
                        $fetchDataWithToken = preg_match("/^.*(fetchDataWithToken)\s.*$/U", $trimmedLine, $matches);
                        if ($fetchDataWithToken) {
                            $newContents .= str_replace($matches[1], "fetchDataNoLocalWithToken", $trimmedLine) . PHP_EOL;
                            $lineModified = true;
                            echo "Text \"\033[32m$matches[1]\033[0m\" has been replaced by \"\033[32mfetchDataNoLocalWithToken\033[0m\" at line n°\033[93m$lineCount\033[0m in \033[35m$nameFile\033[0m\n";
                        };
                        $fetchDataWithTokenRequest = preg_match("/^.*(fetchDataWithToken\().*$/U", $trimmedLine, $matches);
                        if ($fetchDataWithTokenRequest) {
                            $newContents .= str_replace($matches[1], "fetchDataNoLocalWithToken(", $trimmedLine) . PHP_EOL;
                            $lineModified = true;
                            echo "Text \"\033[32m$matches[1]\033[0m\" has been replaced by \"\033[32mfetchDataNoLocalWithToken(\033[0m\" at line n°\033[93m$lineCount\033[0m in \033[35m$nameFile\033[0m\n";
                        };
                        break;
                };
                if (!$lineModified) {
                    $newContents .= $trimmedLine . PHP_EOL;
                };
            };
            file_put_contents($filePath, $newContents);
        }
    };
};

explorePath($path, $argTwo);

echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";
echo "\033[31mPlease check all modified files in case of a potential error during replacement\033[0m\n";
echo "\033[33m~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\033[0m\n";

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
