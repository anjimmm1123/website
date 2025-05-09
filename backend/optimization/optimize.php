<?php
// Enable output buffering
ob_start();

// Set default timezone
date_default_timezone_set('Asia/Jakarta');

// Enable error reporting in development
if (defined('DEBUG_MODE') && DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Set memory limit
ini_set('memory_limit', '256M');

// Set maximum execution time
ini_set('max_execution_time', 30);

// Enable compression
if (extension_loaded('zlib')) {
    ini_set('zlib.output_compression', 'On');
    ini_set('zlib.output_compression_level', '5');
}

// Set cache control headers
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

// Function to optimize database queries
function optimizeQuery($query) {
    // Add index hints if needed
    // Add query caching if needed
    return $query;
}

// Function to compress output
function compressOutput($buffer) {
    // Remove comments
    $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
    // Remove space after colons
    $buffer = str_replace(': ', ':', $buffer);
    // Remove whitespace
    $buffer = str_replace(["\r\n", "\r", "\n", "\t", '  ', '    ', '    '], '', $buffer);
    return $buffer;
}

// Register output compression
if (!DEBUG_MODE) {
    ob_start('compressOutput');
}
?> 