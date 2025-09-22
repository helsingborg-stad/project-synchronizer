# Project synchronizer
Adds and upgrades dependencies and files from a master repository.

## Installation
Install using composer:
```composer require helsingborg-stad/project-synchronizer --dev```

## Usage
```
Usage: php vendor/helsingborg-stad/project-synchronizer/ps.php

	--config <file|url>            	Configuration file or URL
	--source <folder|url>          	Source repository path
	--force							Overwrite existing files and property values
	--help                         	Display this help message
```

## Configuration
A project specific configuration file is used to define which file and optionally which properties of a file that should be synchronized. The composition of the configuration is quite straight forward. 

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
Note that only json files can be transformed. Other file types will be be copied "as is".

By default the application is looking for a ps-config.json file in the running path of the project 
but the path could be altered with the --config parameter.

Target is always the current directory.

The location of the source files should be set using the --source parameter, either https or filesystem. (It will default to this Github project).

To configure which files to be synched, simply list the project relative path of the file 
and optionally which items to synchronize. If the item list is empty, e.g "/myfile.txt": [], the complete file will be transfered (see constraints of 'target files' below).

The synchronization is non-destructive by default:
- Additions will always be processed (e.g a property is missing in the target project).
- A target property will be replaced IF it contains a semver compatible value AND the range of the
source value is higher than the same value in the target property.
- Arrays will be merged and existing values preserved.
- Existing target files will be preserved 

Using the --force flag will have the following implications:
- Existing target properties will be overwritten with values of source.
- Target arrays will be replaced with the values of the source.
- Existing target files will be replaced.
