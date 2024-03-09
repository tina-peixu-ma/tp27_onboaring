<?php
putenv('PYTHONPATH=/usr/local/lib/python3.10/dist-packages');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "Sun";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$suburb = isset($_POST['suburb']) ? $_POST['suburb'] : '';

$suburb = $conn->real_escape_string($suburb);

$sql = "SELECT ROUND(latitude, 1) as latitude, ROUND(longitude, 1) as longitude FROM postcodes_geo WHERE suburb = '$suburb'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $latitude = $row["latitude"];
    $longitude = $row["longitude"];

    $pythonScriptPath = '/home/ubuntu/uv.py'; // Update this with the correct path
    $command = "/usr/bin/python3 " . escapeshellarg($pythonScriptPath) . ' ' . escapeshellarg($latitude) . ' ' . escapeshellarg($longitude) . ' 2>&1';

    // Execute the command and capture both standard output and standard error
    $output = array();
    $returnCode = -1;
    exec($command, $output, $returnCode);

    // Display the image in HTML
    echo "Command: $command <br>";
    echo "<img src='data:image/png;base64," . implode("\n", $output) . "' alt='UV Index Plot'>";
} else {
    echo json_encode("Suburb not found.");
}

$conn->close();
?>
