<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>UV Index Graph</title>
  <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
</head>
<body>

<?php
putenv('PYTHONPATH=/usr/local/lib/python3.10/dist-packages');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set up the connection to the database
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "Sun";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Handle user input (suburb) and query the database to retrieve lat and lng
$suburb = isset($_POST['suburb']) ? $_POST['suburb'] : '';
$suburb = $conn->real_escape_string($suburb);

$sql = "SELECT ROUND(latitude, 1) as latitude, ROUND(longitude, 1) as longitude FROM postcodes_geo WHERE suburb = '$suburb'";
$result = $conn->query($sql);

// Run the python script with lat and lng as parameters
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $latitude = $row["latitude"];
    $longitude = $row["longitude"];

    $pythonScriptPath = '/home/ubuntu/uv.py';
    $command = "/usr/bin/python3 " . escapeshellarg($pythonScriptPath) . ' ' . escapeshellarg($latitude) . ' ' . escapeshellarg($longitude) . ' 2>&1';

    $output = [];
    $returnCode = -1;
    exec($command, $output, $returnCode);

    $json_data = json_decode($output[0], true);

    echo '<div id="uv-graph"></div>';
    echo '<div class="uv-guidelines">';
    echo "<p><strong>Low (UV index 0-2):</strong> Wear sunglasses on bright days. If you burn easily, cover up and use broad-spectrum SPF 30+ sunscreen. You can comfortably wear short sleeves and shorts, but it's wise to start considering sun protection if you'll be outside for extended periods.</p>";
    echo "<p><strong>Moderate (UV index 3-5):</strong> Cover up, wear a hat, and use broad-spectrum SPF 30+ sunscreen, especially if you’ll be outside for more than 20 minutes. Sunglasses are important for protecting your eyes. Wearing long sleeves and pants made from lightweight materials can help protect your skin.</p>";
    echo "<p><strong>High (UV index 6-7):</strong> Protection against sun damage is needed. Wear a wide-brimmed hat, sunglasses, and protective clothing such as long-sleeved shirts and long pants. Apply broad-spectrum SPF 30+ sunscreen every two hours, more often if you're swimming or sweating. Seek shade between 10 a.m. and 4 p.m. when the sun's rays are strongest.</p>";
    echo "<p><strong>Very High (UV index 8-10):</strong> Extra precautions are necessary. Stay indoors during midday hours. Wear protective clothing, a wide-brimmed hat, and sunglasses. Apply broad-spectrum SPF 30+ sunscreen every two hours, and immediately after swimming or sweating. Avoid outdoor activities during peak sunlight hours.</p>";
    echo "<p><strong>Extreme (UV index 11+):</strong> Take all precautions because unprotected skin and eyes can burn in minutes. Try to stay indoors between 10 a.m. and 4 p.m. Make sure to wear a long-sleeved shirt, long pants, a wide-brimmed hat, and UV-blocking sunglasses. Apply broad-spectrum SPF 30+ sunscreen every two hours, and after swimming or sweating.</p>";
    echo "</div>";

    echo '<script>';
    echo 'var uvGraph = ' . json_encode($json_data) . ';';
    echo 'Plotly.newPlot("uv-graph", uvGraph);';
    echo '</script>';
} else {
    echo json_encode("Suburb not found.");
}

$conn->close();
?>

</body>
</html>
