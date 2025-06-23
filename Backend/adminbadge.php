<?php
session_start();

// Optional login check
if (!isset($_SESSION['admin_email'])) {
    header("Location: adminlogin.php");
    exit();
}
include 'db.php';

// Default badge thresholds
$defaultThresholds = [
    'Platinum' => 7,
    'Gold' => 5,
    'Silver' => 3,
    'Bronze' => 1
];

// If admin submitted new thresholds
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['set_thresholds'])) {
    foreach ($defaultThresholds as $badge => $value) {
        if (isset($_POST[$badge])) {
            $defaultThresholds[$badge] = (int) $_POST[$badge];
        }
    }
}

// Fetch all unique users from both donation tables
$query = "
    SELECT username FROM donatemoney
    UNION
    SELECT username FROM donateitem
";
$result = mysqli_query($conn, $query);

$users = [];
while ($row = mysqli_fetch_assoc($result)) {
    $username = $row['username'];

    // Count total donations
    $moneyQuery = "SELECT COUNT(*) as count FROM donatemoney WHERE username = '$username'";
    $itemQuery = "SELECT COUNT(*) as count FROM donateitem WHERE username = '$username'";

    $moneyCount = mysqli_fetch_assoc(mysqli_query($conn, $moneyQuery))['count'];
    $itemCount = mysqli_fetch_assoc(mysqli_query($conn, $itemQuery))['count'];

    $total = $moneyCount + $itemCount;

    // Assign badge
    $badge = 'None';
    foreach ($defaultThresholds as $label => $minCount) {
        if ($total >= $minCount) {
            $badge = $label;
            break;
        }
    }

    // Save info
    $users[] = [
        'username' => $username,
        'total' => $total,
        'badge' => $badge
    ];

    // Insert/update user badge
    mysqli_query($conn, "REPLACE INTO user_badges (username, badge) VALUES ('$username', '$badge')");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Assign Badges</title>
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
      .btn-secondary {
        background-color: #28a745;
        border: none;
    }
    .btn-secondary:hover {
        background-color: #218838;
    }
        .container { margin-top: 40px; }
    </style>
</head>
<body>
    <div class="header">
    <h1>NeedNest</h1>
</div>
<div class="container">
    <h3>Admin Panel: Assign Badges Based on Donations</h3>

    <form method="POST" class="mb-4">
        <h5>Set Badge Thresholds</h5>
        <div class="row g-3">
            <?php foreach ($defaultThresholds as $badge => $value): ?>
                <div class="col-md-3">
                    <label class="form-label"><?= $badge ?> Badge (≥ donations)</label>
                    <input type="number" name="<?= $badge ?>" class="form-control" value="<?= $value ?>" required>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="submit" name="set_thresholds" class="btn btn-primary mt-3">Update Thresholds</button>
    </form>

    <h5>Users and Assigned Badges</h5>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Username</th>
                <th>Total Donations</th>
                <th>Assigned Badge</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= $user['username'] ?></td>
                    <td><?= $user['total'] ?></td>
                    <td><?= $user['badge'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
  <div class="text-center mt-4">
            <a href="admindashboard.php" class="btn btn-secondary">Return to Dashboard</a>
        </div>
</body>
</html>
