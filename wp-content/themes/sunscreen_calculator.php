<?php
header('Content-Type: application/json'); 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $gender = escapeshellarg($data['gender']);
    $height = escapeshellarg($data['height']);
    $weight = escapeshellarg($data['weight']);
    $hat = escapeshellarg($data['hat']);
    $cloth_upper = escapeshellarg($data['cloth_upper']);
    $cloth_lower = escapeshellarg($data['cloth_lower']);
    $shoes = escapeshellarg($data['shoes']);
    $current_uv = escapeshellarg($data['current_uv']);

    $command = "python3 /home/ubuntu/sunscreen_calculator.py $gender $height $weight $hat $cloth_upper $cloth_lower $shoes $current_uv";

    $output = shell_exec($command);

    echo json_encode(array('result' => $output));

} else {

    echo json_encode(array('error' => 'Only POST requests are accepted'));
}
