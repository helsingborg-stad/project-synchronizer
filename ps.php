<?php
# Sets the base path for the application to avoid tricky relative drilling
define('BASE_PATH', getcwd());

// The base path of the remote repository containing the master files
// This will not likely ever change, but if it does, you can update it here
define('REPO_PATH', 'https://raw.githubusercontent.com/helsingborg-stad/project-synchronizer/refs/heads/main');
define('CONFIG_PATH', REPO_PATH . '/ps.json');

# Autoload dependencies and classes using Composer
require BASE_PATH . '/vendor/autoload.php';

# Run the application
$cmd = (object) array_merge([
    "config" => CONFIG_PATH,
    "source" => REPO_PATH,
    "overwrite" => true,
], getopt("", [
    "config:",
    "source:",
    "overwrite",
    "help",
]));

# Display help if requested
if (isset($cmd->help)) {
    echo <<<TEXT
        Usage: php ps.php
            --config <file|url>            Configuration file or URL
            --source <folder|url>          Source repository path
            --overwrite                    Overwrite existing files when copying whole files
            --help                         Display this help message
        TEXT;
    exit(1);
}

# Run the module
App\Module::exec($cmd);