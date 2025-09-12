<?php
# Sets the base path for the application to avoid tricky relative drilling
define('BASE_PATH', __DIR__);

# Autoload dependencies and classes using Composer
require BASE_PATH . '/vendor/autoload.php';

# Run the application
App\Module::exec();