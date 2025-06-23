<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "donations");

if ($conn->connect_error) {
  echo json_encode(["error" => "Connection failed"]);
  exit();
}

$result = $conn->query("SELECT * FROM contact_info LIMIT 1");

if ($result->num_rows > 0) {
  echo json_encode($result->fetch_assoc());
} else {
  echo json_encode(["error" => "No data found"]);
}

$conn->close();
?>
