<?php
include 'db.php';

// Fetch approved donations
$query = "SELECT * FROM donateitem WHERE status = 'Approved' ORDER BY updated_at DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Approved Donations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Roboto&display=swap" rel="stylesheet">
  <style>
    body {
        background-color: #f0fdf4;
        font-family: 'Roboto', sans-serif;
        color: #333;
        margin: 30px;
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
        .container {
            margin-top: 40px;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .card-header {
            background-color:rgb(0, 65, 32);
            color: white;
            font-weight: bold;
        }
        h2 {
            font-weight: bold;
            color:rgb(0, 65, 32);
        }
        .table img {
            width: 60px;
            height: auto;
            border-radius: 6px;
        }
    </style>
</head>
<body>
<div class="header">
    <h1>NeedNest</h1>
</div>
<div class="container">
    <h2 class="text-center mb-4">Approved Donations</h2>

    <div class="card">
        <div class="card-header text-center">List of Approved Donated Items</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered text-center align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Item Name</th>
                            <th>Description</th>
                            <th>Category</th>
                            <th>Condition</th>
                            <th>Picture</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['username']) ?></td>
                                <td><?= htmlspecialchars($row['item_name']) ?></td>
                                <td><?= htmlspecialchars($row['description']) ?></td>
                                <td><?= htmlspecialchars($row['category']) ?></td>
                                <td><?= htmlspecialchars($row['condition']) ?></td>
                                <td>
                                    <?php if (!empty($row['picture_url'])) : ?>
                                        <img src="<?= htmlspecialchars($row['picture_url']) ?>" alt="Item Image">
                                    <?php else : ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                                <td><?= $row['created_at'] ?></td>
                                <td><?= $row['updated_at'] ?></td>
                            </tr>
                        <?php endwhile; ?>
                        <?php if (mysqli_num_rows($result) == 0): ?>
                            <tr>
                                <td colspan="9">No approved donations found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
  <div class="text-center mt-4">
            <a href="admindashboard.php" class="btn btn-secondary">Return to Dashboard</a>
        </div>
</body>
</html>
