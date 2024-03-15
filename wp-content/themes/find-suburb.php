<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UV Index Graph</title>
    <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }

        .accordion {
            width: 100%;
            margin: 20px auto;
        }

        .accordion-button {
            width: 100%;
            padding: 15px;
            border: none;
            text-align: left;
            outline: none;
            font-size: 18px;
            transition: 0.4s;
            background-color: #fff;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .active, .accordion-button:hover {
            background-color: #f0f0f0;
        }

        .accordion-content {
            padding: 15px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }

        .active + .accordion-content {
            max-height: 500px;
            transition: max-height 0.5s ease-in;
        }
    </style>
</head>
<body>


<?php
putenv("PYTHONPATH=/usr/local/lib/python3.10/dist-packages");
error_reporting(E_ALL);
ini_set("display_errors", 1);

// Set up the connection to the database
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "Sun";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
    $input = isset($_POST["suburb_or_postcode"])
        ? $_POST["suburb_or_postcode"]
        : "";
    $input = trim($input);
    $input = htmlspecialchars($input);
    $response = array();

    if (preg_match("/^[a-zA-Z\s]+$/", $input)) {
        $column = "suburb";
    } elseif (preg_match("/^\d+$/", $input)) {
        $column = "postcode";
    } else {
        $response = array("result" => "Invalid input. Please enter a valid suburb or postcode.");
        echo '<p>' . $response["result"] . '</p>';
        echo '<a href="#" onclick="history.back();">Go Back</a>';
        exit();
    }

    $stmt = $conn->prepare(
        "SELECT latitude, longitude FROM postcodes_geo WHERE $column = ?"
    );
    $stmt->bind_param("s", $input);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0 && $row = $result->fetch_assoc()) {
        $latitude = $row["latitude"];
        $longitude = $row["longitude"];
        $pythonScriptPath = "/home/ubuntu/uv.py";
        $command =
            "/usr/bin/python3 " .
            escapeshellarg($pythonScriptPath) .
            " " .
            escapeshellarg($latitude) .
            " " .
            escapeshellarg($longitude) .
            " 2>&1";

        $output = [];
        $returnCode = -1;
        exec($command, $output, $returnCode);
        $json_data = json_decode($output[0], true);
        $response = array("result" => $json_data);

        echo '<div id="uv-graph"></div>';
        echo '<div class="accordion">';
        echo '    <button class="accordion-button"><span>Low (UV index 0-2) </span><span>👕+🩳</span></button>';
        echo '    <div class="accordion-content">';
        echo '        <p>Wear sunglasses on bright days. If you burn easily, cover up and use broad-spectrum SPF 30+ sunscreen. You can comfortably wear short sleeves and shorts, but it\'s wise to start considering sun protection if you\'ll be outside for extended periods.</p>';
        echo '    </div>';
        echo '    <button class="accordion-button"><span>Moderate (UV index 3-5)</span> <span>🧥+👖+🕶️+🧢+🧴</span></button>';
        echo '    <div class="accordion-content">';
        echo '        <p>Cover up, wear a hat, and use broad-spectrum SPF 30+ sunscreen, especially if you’ll be outside for more than 20 minutes. Sunglasses are important for protecting your eyes. Wearing long sleeves and pants made from lightweight materials can help protect your skin.</p>';
        echo '    </div>';
        echo '    <button class="accordion-button"><span>High (UV index 6-7)</span> <span>🧥+👖+🕶️+🧢+🧴🧴+⛱️</span></button>';
        echo '    <div class="accordion-content">';
        echo '        <p>Protection against sun damage is needed. Wear a wide-brimmed hat, sunglasses, and protective clothing such as long-sleeved shirts and long pants. Apply broad-spectrum SPF 30+ sunscreen every two hours, more often if you\'re swimming or sweating. Seek shade between 10 a.m. and 4 p.m. when the sun\'s rays are strongest.</p>';
        echo '    </div>';
        echo '    <button class="accordion-button"><span>Very High (UV index 8-10)</span> <span>🧥+👖+🕶️+🧢+🧴🧴🧴+🏠</span></button>';
        echo '    <div class="accordion-content">';
        echo '        <p>Extra precautions are necessary. Stay indoors during midday hours. Wear protective clothing, a wide-brimmed hat, and sunglasses. Apply broad-spectrum SPF 30+ sunscreen every two hours, and immediately after swimming or sweating. Avoid outdoor activities during peak sunlight hours.</p>';
        echo '    </div>';
        echo '    <button class="accordion-button"><span>Extreme (UV index 11+)</span> <span>🧥+👖+🕶️+🧢+🧴🧴🧴🧴🧴+🏠</span></button>';
        echo '    <div class="accordion-content">';
        echo '        <p>Take all precautions because unprotected skin and eyes can burn in minutes. Try to stay indoors between 10 a.m. and 4 p.m. Make sure to wear a long-sleeved shirt, long pants, a wide-brimmed hat, and UV-blocking sunglasses. Apply broad-spectrum SPF 30+ sunscreen every two hours, and after swimming or sweating.</p>';
        echo '    </div>';
        echo '</div>';
        echo '<a href="#" onclick="history.back();">Go Back</a>';
        echo '<script>';
        echo '    window.onload = function () {';
        echo '        var acc = document.getElementsByClassName("accordion-button");';
        echo '        var i;';
        echo '';
        echo '        for (i = 0; i < acc.length; i++) {';
        echo '            acc[i].addEventListener("click", function () {';
        echo '                this.classList.toggle("active");';
        echo '                var panel = this.nextElementSibling;';
        echo '                if (panel.style.maxHeight) {';
        echo '                    panel.style.maxHeight = null;';
        echo '                } else {';
        echo '                    panel.style.maxHeight = panel.scrollHeight + "px";';
        echo '                }';
        echo '            });';
        echo '        }';
        echo '    }';
        echo '</script>';
        echo "<script>";
        echo "    var uvGraph = " . json_encode($json_data) . ";";
        echo '    Plotly.newPlot("uv-graph", uvGraph);';
        echo "</script>";
    } else {
        echo '<a href="#" onclick="history.back();">Go Back</a>';
        echo "No results found for the provided input.";
    }

    $stmt->close();
    $result->close();
} else {
    $response = array("result" => "Invalid request.");
    echo '<p>' . $response["result"] . '</p>';
    echo '<a href="#" onclick="history.back();">Go Back</a>';
}
?>

</body>
</html>
