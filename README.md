# VerificationScript

This repository is used to create scripts verifying projects according to user requirements

## How to use the script:

### verificationLine.php
- Run the command `php verificationLine.php [path] [option] [option]` in your terminal.
- Replace `[path]` with the path you want to verify.
- Replace `[option]` with `-noExplain` if you do not want the script to explain how it navigates through folders/subfolders/files. (if you want the explanation, just write "`-`" for [option])
- Replace the second [option] with a number to select the number of lines that a file must not exceed

### verificationPascalOrCamelPersonal.php
- Run the command `php verificationPascalOrCamelPersonal.php [path] [option]` in your terminal.
- Replace `[path]` with the path you want to verify.
- Replace `[option]` with `-noExplain` if you do not want the script to explain

### verificationSelemicon.php
- Run the command `php verificationSelemicon.php [path] [option] [option]` in your terminal.
- Replace `[path]` with the path you want to verify.
- Replace `[option]` with `-noEdit` if you do not want the script rewrite the file.
- Replace the second `[option]` with `-withoutExplain` if you don't want explication and you want only number of line affected

if you don't want [option], write a "`-`" instead of [option]
Please ensure that you have PHP installed and configured on your machine to run these scripts.

## Developer

Hello Developer, if you're planning to create a new script, please write it in English and adhere to the Lower Camel Case (LCC) naming convention. You can refer to `Base.php` for guidance.

Please indicate your github in a comment at the bottom of each file you create.

Please do not touch the file already present without the agreement of the file creator.
