<?php
include 'db.php';

$successMsg = "";

// Handle Approve
if (isset($_POST['approve'])) {
    $id = $_POST['id'];
    $updatedAt = date('Y-m-d H:i:s');
    $approveQuery = "UPDATE donateitem SET status='approved', updated_at='$updatedAt' WHERE id=$id";
    mysqli_query($conn, $approveQuery);
    $successMsg = "Donation approved successfully.";
}

// Handle Reject

if (isset($_POST['reject']) && isset($_POST['reason']) && !empty($_POST['reason'])) {
    $id = $_POST['id'];
    $reason = mysqli_real_escape_string($conn, $_POST['reason']);

    // Get item details
    $getItemQuery = "SELECT * FROM donateitem WHERE id=$id";
    $itemResult = mysqli_query($conn, $getItemQuery);
    $item = mysqli_fetch_assoc($itemResult);

    if ($item) {
        $username = mysqli_real_escape_string($conn, $item['username']);
        $itemName = mysqli_real_escape_string($conn, $item['item_name']);
        $category = mysqli_real_escape_string($conn, $item['category']);
        $condition = mysqli_real_escape_string($conn, $item['condition']);
        $description = mysqli_real_escape_string($conn, $item['description']);
        $createdAt = $item['created_at'];

        // Insert into rejecteddonations table
        $insertRejectQuery = "INSERT INTO rejecteddonations (username, item_name, category, `condition`, description, reason, created_at) 
                              VALUES ('$username', '$itemName', '$category', '$condition', '$description', '$reason', '$createdAt')";
        if (mysqli_query($conn, $insertRejectQuery)) {
    // Delete from donateitem
    mysqli_query($conn, "DELETE FROM donateitem WHERE id=$id");
    $successMsg = "Donation rejected and moved to the rejected donations table.";
} else {
    $error = mysqli_error($conn); // ← Get error
    $successMsg = "Error inserting into rejecteddonations table: " . $error; // ← Show error
}

    }
}

// Fetch all pending items
$pendingQuery = "SELECT * FROM donateitem WHERE status='pending' ORDER BY created_at DESC";
$pendingResult = mysqli_query($conn, $pendingQuery);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pending Donations - Admin</title>
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

        .table th, .table td {
            vertical-align: middle;
        }
          .btn-secondary {
        background-color: #28a745;
        border: none;
    }
    .btn-secondary:hover {
        background-color: #218838;
    }
        </style>
    <script>
        function rejectWithReason(id) {
            let reason = prompt("Enter rejection reason:");
            if (reason && reason.trim() !== "") {
                // Set values and submit hidden form
                document.getElementById('reject-id').value = id;
                document.getElementById('reject-reason').value = reason;
                document.getElementById('reject-form').submit();
            } else {
                alert("Rejection cancelled. Reason is required.");
            }
        }
    </script>
</head>
<body>
    <div class="header">
    <h1>NeedNest</h1>
   
</div>
<div class="container mt-5">
    <h2 class="text-center mb-4">Pending Donation Approvals</h2>

    <?php if (!empty($successMsg)) : ?>
        <div class="alert alert-success"><?= $successMsg ?></div>
    <?php endif; ?>

    <table class="table table-bordered text-center align-middle">
        <thead class="table-success">
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Item Name</th>
            <th>Description</th>
            <th>Category</th>
            <th>Condition</th>
            <th>Picture</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = mysqli_fetch_assoc($pendingResult)) : ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= htmlspecialchars($row['item_name']) ?></td>
                <td><?= htmlspecialchars($row['description']) ?></td>
                <td><?= htmlspecialchars($row['category']) ?></td>
                <td><?= htmlspecialchars($row['condition']) ?></td>
                <td>
                    <?php if (!empty($row['picture_url'])) : ?>
                        <img src="<?= htmlspecialchars($row['picture_url']) ?>" alt="Item Image" width="60">
                    <?php else : ?>
                        N/A
                    <?php endif; ?>
                </td>
                <td><?= $row['created_at'] ?></td>
                <td>
                    <!-- Approve Form -->
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <button type="submit" name="approve" class="btn btn-success btn-sm">Approve</button>
                    </form>

                    <!-- Reject Button triggers JS -->
                    <button onclick="rejectWithReason(<?= $row['id'] ?>)" class="btn btn-danger btn-sm mt-2">Reject</button>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Hidden Form to handle rejection -->
    <form method="post" id="reject-form" style="display:none;">
        <input type="hidden" name="id" id="reject-id">
        <input type="hidden" name="reason" id="reject-reason">
        <input type="hidden" name="reject" value="1">
    </form>
</div>

        <div class="text-center mt-4">
            <a href="admindashboard.php" class="btn btn-secondary">Return to Dashboard</a>
        </div>
</body>
</html>
