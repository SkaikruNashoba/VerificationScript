# VerificationScript

This repository is used to create scripts verifying projects according to user requirements

## How to use the script:
Please ensure that you have PHP installed and configured on your machine to run these scripts.

(if you don't want `[option]` you can replace `[option]` by a "-")

### verificationLine.php
- Run the command `php verificationLine.php [path] [option] [option]` in your terminal.
- Replace `[path]` with the path you want to verify.
- Replace `[option]` with `-noExplain` if you do not want the script to explain how it navigates through folders/subfolders/files.
- Replace the second [option] with a number to select the number of lines that a file must not exceed

### verificationPascalOrCamelPersonal.php
- Run the command `php verificationPascalOrCamelPersonal.php [path]` in your terminal.
- Replace `[path]` with the path you want to verify.

### verificationSelemicon.php
- Run the command `php verificationSelemicon.php [path] [option] [option]` in your terminal.
- Replace `[path]` with the path you want to verify.
- Replace `[option]` with `-noEdit` if you do not want the script rewrite the file.
- Replace the second `[option]` with `-noExplain` if you don't want explication and you want only number of line affected

### verificationPrefix.php
- Run the command `php verificationPrefix.php [path] [option] [option] [option]` in your terminal.
- Replace `[path]` with the path you want to verify.
- Replace `[option]` with `-noEdit` if you do not want the script rewrite the file.
- Replace the second `[option]` with `-noExplain` if you don't want explication and you want only number of line affected
- Replace third `[option]` by a prefix you want

## Developer

Hello Developer, if you're planning to create a new script, please write it in English and adhere to the Lower Camel Case (LCC) naming convention. You can refer to `Base.php` for guidance.

Please indicate your github in a comment at the bottom of each file you create.

Please do not touch the file already present without the agreement of the file creator.