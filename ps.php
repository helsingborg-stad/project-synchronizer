<?php

// The default path of the source repository having the master files
// This will not likely ever change, but if it does, you can update it here
define('SOURCE_PATH', 'https://raw.githubusercontent.com/helsingborg-stad/project-synchronizer/refs/heads/main');
define('TARGET_PATH', getcwd());
define('CONFIG_PATH', TARGET_PATH . '/ps-config.json');

// Autoload dependencies and classes
require TARGET_PATH . '/vendor/autoload.php';

// Parse command line arguments with defaults
$cmd = (object) array_merge(
    [
        'source' => SOURCE_PATH,
        'config' => CONFIG_PATH,
        'target' => TARGET_PATH,
        'force' => true,
    ],
    getopt('', [
        'source:',
        'config:',
        'force',
        'help',
    ]),
);

// Remove trailing slash from source path if present
$cmd->source = rtrim($cmd->source, '/');

// Display help if requested
if (isset($cmd->help)) {
    echo <<<TEXT
        Usage: php ps.php
            --config <file|url>            Configuration file or URL
            --source <folder|url>          Source repository path
            --force                        Overwrite existing files and property values
            --help                         Display this help message
        TEXT;
    exit(1);
}

// Run the module
App\Module::exec($cmd);
