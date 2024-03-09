<?php
putenv('PYTHONPATH=/usr/local/lib/python3.10/dist-packages');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Hardcoding for testing
$latitude = -35.22;
$longitude = 148.22;

// Prepare the command used for execution
$pythonScriptPath = '/home/ubuntu/uv.py';
$command = "/usr/bin/python3 " . escapeshellarg($pythonScriptPath) . ' ' . escapeshellarg($latitude) . ' ' . escapeshellarg($longitude) . ' 2>&1';

// Execute the command in the shell
$output = array();
$returnCode = -1;
exec($command, $output, $returnCode);

// Display the command and output for debugging
echo "Command: $command <br>";
echo "Output: " . shell_exec($command) . "<br>";

// Display the image in HTML
echo "<img src='data:image/png;base64," . implode("\n", $output) . "' alt='UV Index Plot'>";
?>
