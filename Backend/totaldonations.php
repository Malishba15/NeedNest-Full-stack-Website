<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

include 'db.php';

$username = $_SESSION['first_name'];

$sql_money = "SELECT 'Money' AS type, amount AS donation, donation_date AS timestamp FROM donatemoney WHERE username = '$username' ORDER BY donation_date DESC";
$sql_item = "SELECT 'Item' AS type, item_name AS donation, created_at AS timestamp FROM donateitem WHERE username = '$username' ORDER BY created_at DESC";

$result_money = mysqli_query($conn, $sql_money);
if (!$result_money) {
    die('Error executing query (donatemoney): ' . mysqli_error($conn));
}

$result_item = mysqli_query($conn, $sql_item);
if (!$result_item) {
    die('Error executing query (donateitem): ' . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Total Donations</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Roboto&display=swap" rel="stylesheet">
  <style>
    body {
        background-color: #f0fdf4;
        font-family: 'Roboto', sans-serif;
        color: #333;
        margin:20px;
    }

    .header {
        background-color: #14532d;
        padding: 20px;
        text-align: center;
        box-shadow: 0 4px 6px rgba(0,0,0,0.3);
        margin-bottom: 40px;
        border-bottom: 4px solid #1e7e34;
    }

    .header h1 {
        font-family: 'Pacifico', cursive;
        font-size: 40px;
        color: #d4edda;
        margin: 0;
        letter-spacing: 2px;
    }

    .container {
        max-width: 900px;
        margin: auto;
        background-color: #ffffff;
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }

    h2 {
       margin-bottom: 30px;
        font-weight: bold;
        color: #14532d;
        text-align: center;
    }

    .table th {
        background-color: #28a745;
        color: white;
        text-align: center;
    }

    .table td {
        text-align: center;
    }

    .btn-secondary {
        background-color: #14532d;
        border: none;
        transition: 0.3s;
    }

    .btn-secondary:hover {
        background-color: #1e7e34;
    }

    .no-donations {
        text-align: center;
        color: #6c757d;
        font-style: italic;
        padding: 20px 0;
    }
  </style>
</head>
<body>

<div class="header">
    <h1>NeedNest</h1>
</div>

<div class="container">
    <h2>Total Donations - <?php echo htmlspecialchars($username); ?></h2>

    <table class="table table-bordered mt-4">
        <thead>
            <tr>
                <th>Donation Type</th>
                <th>Amount / Item</th>
                <th>Donation Date</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $hasDonations = false;

            if (mysqli_num_rows($result_money) > 0) {
                $hasDonations = true;
                while ($row = mysqli_fetch_assoc($result_money)) {
                    echo "<tr>";
                    echo "<td>" . $row['type'] . "</td>";
                    echo "<td>" . $row['donation'] . "</td>";
                    echo "<td>" . $row['timestamp'] . "</td>";
                    echo "</tr>";
                }
            }

            if (mysqli_num_rows($result_item) > 0) {
                $hasDonations = true;
                while ($row = mysqli_fetch_assoc($result_item)) {
                    echo "<tr>";
                    echo "<td>" . $row['type'] . "</td>";
                    echo "<td>" . $row['donation'] . "</td>";
                    echo "<td>" . $row['timestamp'] . "</td>";
                    echo "</tr>";
                }
            }

            if (!$hasDonations) {
                echo "<tr><td colspan='3' class='no-donations'>No donations available.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <div class="text-center mt-4">
        <a href="dashboard.php" class="btn btn-secondary px-4 py-2">Return to Dashboard</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
