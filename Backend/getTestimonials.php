<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "donations");

if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "Connection failed"]);
    exit;
}

$sql = "SELECT * FROM testimonials ORDER BY id DESC";
$result = $conn->query($sql);

$testimonials = [];

while ($row = $result->fetch_assoc()) {
    $testimonials[] = $row;
}

echo json_encode(["success" => true, "testimonials" => $testimonials]);
?>
