<?php
include 'db.php';

// Fetch item donations
$itemDonations = mysqli_query($conn, "SELECT * FROM donateitem");
$itemDonationCount = mysqli_num_rows($itemDonations); // Count of item donations

// Fetch money donations
$moneyDonations = mysqli_query($conn, "SELECT * FROM donatemoney");
$moneyDonationCount = mysqli_num_rows($moneyDonations); // Count of money donations



// Total money donated
$moneyResult = mysqli_query($conn, "SELECT SUM(amount) AS totalMoney FROM donatemoney");
$moneyRow = mysqli_fetch_assoc($moneyResult);
$totalMoney = $moneyRow['totalMoney'] ?? 0;

// Total items donated (using count instead of max ID for accuracy)
$itemTotalResult = mysqli_query($conn, "SELECT COUNT(*) as total_items FROM donateitem");
$itemTotalRow = mysqli_fetch_assoc($itemTotalResult);
$totalItems = $itemTotalRow['total_items'] ?? 0;

// All donated items
$itemResult = mysqli_query($conn, "SELECT * FROM donateitem ORDER BY created_at DESC");

// Category-wise breakdown
$breakdownResult = mysqli_query($conn, "SELECT category, COUNT(*) AS total FROM donateitem GROUP BY category");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Donation Report</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Roboto&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
   <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        .container {
            margin-top: 40px;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .card-header {
            background-color: #2e7d32;
            color: white;
            font-weight: bold;
        }
        h2 {
            font-weight: bold;
            color: #2e7d32;
        }
        .table thead {
            background-color: #a5d6a7;
        }
        .table img {
            width: 60px;
            height: auto;
            border-radius: 6px;
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
    <h2 class="text-center mb-4">Donation Summary Report</h2>

    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card text-center">
                <div class="card-header">Total Money Donated</div>
                <div class="card-body">
                    <h3 class="text-success">PKR <?= number_format($totalMoney, 0) ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <div class="card text-center">
                <div class="card-header">Total Items Donated</div>
                <div class="card-body">
                    <h3 class="text-success"><?= number_format($totalItems) ?> Items</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Breakdown Table -->
    <div class="card mb-4">
        <div class="card-header text-center">Category-wise Breakdown</div>
        <div class="card-body">
            <table class="table table-bordered text-center align-middle">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Total Donated Items</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($breakdownResult)) : ?>
                        <tr>
                            <td><?= htmlspecialchars($row['category']) ?></td>
                            <td><?= $row['total'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Full Donated Items Table -->
    <div class="card">
        <div class="card-header text-center">All Donated Items</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered text-center align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Item Name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Category</th>
                            <th>Condition</th>
                            <th>Picture</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($itemResult)) : ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['username']) ?></td>
                                <td><?= htmlspecialchars($row['item_name']) ?></td>
                                <td><?= htmlspecialchars($row['description']) ?></td>
                                <td><?= htmlspecialchars($row['status']) ?></td>
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
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
 <div>
             <div class="container mt-5 mb-5 d-flex justify-content-center">
  <div style="width: 500px;">
   <h2 class="text-center mb-4">Donation Graph Report</h2>
    <canvas id="donationChart"></canvas>
  </div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const ctx = document.getElementById('donationChart').getContext('2d');

  const itemDonations = <?= $itemDonationCount ?>;
  const moneyDonations = <?= $moneyDonationCount ?>;

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Item Donations', 'Money Donations'],
      datasets: [{
        label: 'Number of Donations',
        data: [itemDonations, moneyDonations],
        backgroundColor: ['#007bff', '#ffc107']
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
          precision: 0
        }
      }
    }
  });
</script>

<div class="text-center mt-4">
            <a href="admindashboard.php" class="btn btn-secondary">Return to Dashboard</a>
        </div>
</body>
</html>
