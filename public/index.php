<?php

use CodeIgniter\Boot;
use Config\Paths;

/*
 *---------------------------------------------------------------
 * CHECK PHP VERSION
 *---------------------------------------------------------------
 */

$minPhpVersion = '8.1'; // If you update this, don't forget to update `spark`.
if (version_compare(PHP_VERSION, $minPhpVersion, '<')) {
    $message = sprintf(
        'Your PHP version must be %s or higher to run CodeIgniter. Current version: %s',
        $minPhpVersion,
        PHP_VERSION,
    );

    header('HTTP/1.1 503 Service Unavailable.', true, 503);
    echo $message;

    exit(1);
}

/*
 *---------------------------------------------------------------
 * SET THE CURRENT DIRECTORY
 *---------------------------------------------------------------
 */

// Path to the front controller (this file)
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

// Ensure the current directory is pointing to the front controller's directory
if (getcwd() . DIRECTORY_SEPARATOR !== FCPATH) {
    chdir(FCPATH);
}

/*
 *---------------------------------------------------------------
 * BOOTSTRAP THE APPLICATION
 *---------------------------------------------------------------
 * This process sets up the path constants, loads and registers
 * our autoloader, along with Composer's, loads our constants
 * and fires up an environment-specific bootstrapping.
 */

// LOAD OUR PATHS CONFIG FILE
// This is the line that might need to be changed, depending on your folder structure.
// Resolve the expected paths file and fail with a clear message if it's missing.
$expectedPaths = FCPATH . '../app/Config/Paths.php';
$resolvedPaths = realpath($expectedPaths);
if ($resolvedPaths === false || ! is_file($resolvedPaths)) {
    // Provide a helpful error to make debugging easier on Windows/XAMPP setups
    header('HTTP/1.1 500 Internal Server Error', true, 500);
    echo "Required file not found: \n";
    echo "Tried (relative): " . $expectedPaths . "\n";
    echo "realpath -> " . var_export($resolvedPaths, true) . "\n";
    echo "Front controller (FCPATH) -> " . FCPATH . "\n";
    echo "Please ensure the file exists and that your DocumentRoot points to the 'public' folder.\n";
    exit(1);
}

require $resolvedPaths;
// ^^^ Change this line if you move your application folder

$paths = new Paths();

// LOAD THE FRAMEWORK BOOTSTRAP FILE
require $paths->systemDirectory . '/Boot.php';

exit(Boot::bootWeb($paths));
