<?php
include 'db.php';

// Fetch item donations
$itemDonations = mysqli_query($conn, "SELECT * FROM donateitem");

// Fetch money donations
$moneyDonations = mysqli_query($conn, "SELECT * FROM donatemoney");


?>

<!DOCTYPE html>
<html>
<head>
    <title>All Donations - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            margin-top: 40px;
        }
        .table-container {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-bottom: 40px;
        }
        h2 {
            color: #2e7d32;
        }
        .table-success th {
            background-color: #a5d6a7 !important;
        }
          .btn-secondary {
        background-color: #28a745;
        border: none;
    }
    .btn-secondary:hover {
        background-color: #218838;
    }
    </style>
</head>
<body>
<div class="header">
    <h1>NeedNest</h1>
   
</div>
<div class="container">

    <div class="table-container">
        <h2 class="text-center mb-4">Item Donations</h2>
        <table class="table table-bordered table-success text-center align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Donor Name</th>
                    <th>Category</th>
                    <th>Item</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($itemDonations)) : ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['category']) ?></td>
                    
                        <td><?= htmlspecialchars($row['item_name']) ?></td>
                        
                        <td><?= $row['created_at'] ?? '-' ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div class="table-container">
        <h2 class="text-center mb-4">Money Donations</h2>
        <table class="table table-bordered table-success text-center align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Donor Name</th>

                    <th>Amount</th>

                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($moneyDonations)) : ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                      
                        <td>PKR <?= number_format($row['amount'], 0) ?></td>
                       
                        <td><?= $row['donation_date'] ?? '-' ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</div>
     <div class="text-center mt-4">
            <a href="admindashboard.php" class="btn btn-secondary">Return to Dashboard</a>
        </div>
</body>
</html>
