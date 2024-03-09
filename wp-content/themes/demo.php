<?php
putenv('PYTHONPATH=/usr/local/lib/python3.10/dist-packages');

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Sample latitude and longitude values
$latitude = -35.22;
$longitude = 148.22;

// Command to execute Python script with latitude and longitude as arguments
$pythonScriptPath = '/home/ubuntu/uv.py'; // Update this with the correct path
$command = "/usr/bin/python3 " . escapeshellarg($pythonScriptPath) . ' ' . escapeshellarg($latitude) . ' ' . escapeshellarg($longitude) . ' 2>&1';

// Execute the command and capture both standard output and standard error
$output = array();
$returnCode = -1;
exec($command, $output, $returnCode);

// Display the return code and standard output for debugging
echo "<pre>Return Code: $returnCode</pre>";
echo "<pre>Output: " . implode("\n", $output) . "</pre>";

// Display the image in HTML
echo "<img src='data:image/png;base64," . implode("\n", $output) . "' alt='UV Index Plot'>";
echo "Command: $command <br>";
echo "Output: " . shell_exec($command) . "<br>";
echo "Environment Variables: " . shell_exec('env') . "<br>";

?>
