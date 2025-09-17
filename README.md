# Project synchronizer
Adds and upgrades dependencies and files from a master repository.

## Installation
composer require helsingborg-stad/project-synchronizer

```
Usage: php vendor/helsingborg-stad/project-synchronizer/ps.php

	--config <file|url>            Configuration file or URL
	--source <folder|url>          Source repository path
	--overwrite                    Overwrite existing files and property values
	--help                         Display this help message
```

## Configuration
The composition of the configuration is quite straight forward. 
Simply list the repo relative path of the file to align and specify which items
to synchronize. If the item list is empty, e.g "/myfile.txt": [], the complete file will 
be transfered (see constraints of 'target files' below).

The synchronization is non-destructive by default:
- Additions will always be processed (e.g a dependency is missing in the target project).
- A target property will be replaced IF it contains a semver compatible value AND the range of the
source value is higher than the same value in the target property.
- Arrays will be merged and existing values preserved.
- Existing target files will be preserved 

Using the --overwrite flag will have the following implications:
- Existing target properties will be overwritten with values of the source.
- Target arrays will be replaced with the values of the source.
- Existing target files will be replaced.

## An example of configuration
Note that the last file /vite.config.mjs is not a JSON file; hence it cannot be transformed. Instead it will be copied as is
if it didnt exist already.

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
