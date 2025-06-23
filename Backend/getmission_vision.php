<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
$conn = new mysqli("localhost", "root", "", "donations");
if ($conn->connect_error) {
    die(json_encode(["error" => "DB connection failed"]));
}
$res = $conn->query("SELECT * FROM mission_vision");
$data = [];
while ($row = $res->fetch_assoc()) {
    $data[] = $row;
}
header('Content-Type: application/json');
echo json_encode($data);
?>
