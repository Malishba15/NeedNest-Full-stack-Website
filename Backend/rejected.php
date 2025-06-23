<?php
session_start();

include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['first_name'];
// Query to select rejected donations for the specific user
$sql = "SELECT username, item_name, category, `condition`, description, reason, created_at 
        FROM rejecteddonations 
        WHERE username = '$username'"; 

// Execute the query
$result = mysqli_query($conn, $sql);

// Check for query errors
if (!$result) {
    echo "Error executing query: " . mysqli_error($conn);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Rejected Donations</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Roboto&display=swap" rel="stylesheet">
  <style>
    body {
        background-color: #f0fdf4;
        color: #f8f9fa;
        font-family: 'Roboto', sans-serif;
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
        background-color:rgb(255, 255, 255);
        padding: 40px;
        border-radius: 15px;
        margin: 0 auto;
        box-shadow: 0 0 15px rgba(0,0,0,0.6);
    }

    h2 {
        margin-bottom: 30px;
        font-weight: bold;
        color: #14532d;
        text-align: center;
    }

    .table {
        background-color: #1e5128;
        color: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 0 10px rgba(0,0,0,0.4);
    }

    .table th {
        background-color: #14532d;
        color: #d1fae5;
    }

    .table tbody tr:hover {
        background-color: #276749;
    }

    .alert-info {
        background-color: #155724;
        color: #c3e6cb;
        border: none;
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
  </style>
</head>
<body>

<div class="header">
    <h1>NeedNest</h1>
</div>

<div class="container">
    <h2>Rejected Donations</h2>

    <?php if (mysqli_num_rows($result) > 0): ?>
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>Username</th>
                <th>Item Name</th>
                <th>Category</th>
                <th>Condition</th>
                <th>Description</th>
                <th>Reason</th>
                <th>Donated On</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= htmlspecialchars($row['item_name']) ?></td>
                <td><?= htmlspecialchars($row['category']) ?></td>
                <td><?= htmlspecialchars($row['condition']) ?></td>
                <td><?= htmlspecialchars($row['description']) ?></td>
                <td><?= htmlspecialchars($row['reason']) ?></td>
                <td><?= htmlspecialchars($row['created_at']) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
        <div class="alert alert-info">No rejected donations found.</div>
    <?php endif; ?>

    <div class="text-center mt-4">
        <a href="dashboard.php" class="btn btn-secondary px-4 py-2">Back to Dashboard</a>
    </div>
</div>

</body>
</html>
