# Project synchronizer
Adds and upgrades dependencies and files from a master repository.

## Installation
composer require helsingborg-stad/project-synchronizer

```
Usage: php vendor/helsingborg-stad/project-synchronizer/ps.php

    --config <file|url>            Configuration file or URL
    --source <folder|url>          Source repository path
    --overwrite                    Overwrite existing files when copying whole files
    --help                         Display this help message
```

## Configuration
The composition of the configuration is quite straight forward. 
Simply list the repo relative path of the file to align and specify which items
to synchronize. If the item list is empty, the whole file will be downloaded.

The synchronization is non-destructive:
- Additions will always be processed (e.g a dependency is missing in the local project).
- Updates will be applied IF a field contains a semver compatible value AND the range of the
source value is higher than the same value in the destination file.
- Local files will only be overwritten if the --overwrite flag is activated and it is only applicable
for file copy operations (item list is empty). 

## An example of configuration
Note that the last file /vite.config.mjs is not a JSON file; hence it cannot be transformed. Instead it will be copied as is
if it doesnt exist already.

````
{
	"/package.json": [
		"license",
		"dependencies",
		"devDependencies",
		"scripts",
		"engines",
		"jest"
	],
	"/composer.json": ["require", "require-dev", "scripts"],
	"/tsconfig.json": ["compilerOptions"],
	"/.vscode/settings.json": [
		"editor.formatOnSave",
		"editor.defaultFormatter",
		"editor.codeActionsOnSave"
	],
	"/.vscode/extensions.json": ["recommendations"],
	"/vite.config.mjs": []
}
````

## Synchronizing with other repos
By default, the repo and config are remotely fetched from this source repository.
This behaviour can be changed to include your own config and other repos, local or remote.

Example:
````
php vendor/helsingborg-stad/project-synchronizer/ps.php --config ./myconfig.json --source ../my-other-repo
````
