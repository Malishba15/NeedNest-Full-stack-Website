<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}
include 'db.php';

$success_msg = $error_msg = $item_success_msg = $item_error_msg = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['donate_money'])) {
        // Process money donation
        $amount = $_POST['amount'];
        $credit_card = $_POST['credit_card'];
        $username = $_SESSION['first_name'];

        // Assume you have a function to insert into the donatemoney table
        include 'db.php';
        $sql = "INSERT INTO donatemoney (username, amount, credit_card) VALUES ('$username', '$amount', '$credit_card')";
        if (mysqli_query($conn, $sql)) {
            $success_msg = "Donation of $amount has been successfully processed!";
        } else {
            $error_msg = "Error processing donation.";
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['donate_item'])) {
        $item_name = trim($_POST['item_name']);
        $category = $_POST['category'];
        $condition = trim($_POST['condition']);
        $description = trim($_POST['description']);
        $created_at = date('Y-m-d H:i:s');
        $updated_at = $created_at;
        $status = "pending";
        $username = $_SESSION['first_name'];

        $check_sql = "SELECT * FROM donateitem WHERE username = '$username' AND item_name = '$item_name'";
        $check_result = mysqli_query($conn, $check_sql);
        if (mysqli_num_rows($check_result) > 0) {
            $reason = 'Replicate donations are not allowed.';
            $reject_sql = "INSERT INTO rejecteddonations (username, item_name, category, `condition`, description, reason, created_at)
                           VALUES ('$username', '$item_name', '$category', '$condition', '$description', '$reason', '$created_at')";
            if (mysqli_query($conn, $reject_sql)) {
                $item_error_msg = "Replicate donations are not allowed. The item has been rejected.";
            } else {
                $item_error_msg = "Error saving rejected donation: " . mysqli_error($conn);
            }
        } else {
            $target_dir = "Backend/uploads/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }

            $image = $_FILES["item_image"];
            $image_name = basename($image["name"]);
            $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
            $allowed_extensions = ['png', 'jpg', 'jpeg'];

            if (in_array($image_ext, $allowed_extensions)) {
                $new_filename = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", $image_name);
                $target_file = $target_dir . $new_filename;

                if (move_uploaded_file($image["tmp_name"], $target_file)) {
                    $sql = "INSERT INTO donateitem (username, item_name, description, status, category, `condition`, picture_url, created_at, updated_at) 
                            VALUES ('$username', '$item_name', '$description', '$status', '$category', '$condition', '$target_file', '$created_at', '$updated_at')";
                    if (mysqli_query($conn, $sql)) {
                        $item_success_msg = "Your item donation is pending approval. You will be notified once it is approved by our admin.";
                    } else {
                        $item_error_msg = "Error submitting item donation: " . mysqli_error($conn);
                    }
                } else {
                    $item_error_msg = "Failed to upload image.";
                }
            } else {
                $item_error_msg = "Only .png and .jpg images are allowed.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Make a New Donation</title>
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
    .form-section {
        background-color: #fff;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        margin-top: 30px;
    }
    .btn-primary {
        background-color: #28a745;
        border: none;
    }
    .btn-primary:hover {
        background-color: #218838;
    }
    .alert {
        margin-top: 20px;
    }
  </style>
</head>
<body>

<div class="header">
    <h1>NeedNest</h1>
</div>

<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-7 form-section">

        <!-- Donation Type Selector -->
        <form action="newdonation.php" method="POST">
          <div class="mb-4">
            <label for="donateOption" class="form-label">Select Donation Type</label>
            <select class="form-select" id="donateOption" name="donate_option" required>
              <option value="money">Donate Money</option>
              <option value="item">Donate Item</option>
            </select>
          </div>
          <button type="submit" class="btn btn-primary w-100" name="select_donation">Proceed</button>
        </form>

        <!-- Conditional Forms -->
        <?php if (isset($_POST['select_donation'])): ?>
            <?php if ($_POST['donate_option'] == 'money'): ?>
                <!-- Money Donation Form -->
                <form action="newdonation.php" method="POST" class="mt-4">
                    <h4 class="mb-3">Money Donation</h4>
                    <div class="mb-3">
                        <label for="amount" class="form-label">Donation Amount</label>
                        <input type="number" class="form-control" id="amount" name="amount" required>
                    </div>
                    <div class="mb-3">
                        <label for="credit_card" class="form-label">Credit Card Details</label>
                        <input type="text" class="form-control" id="credit_card" name="credit_card" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100" name="donate_money">Donate Money</button>
                </form>
            <?php elseif ($_POST['donate_option'] == 'item'): ?>
                <!-- Item Donation Form -->
                <form action="newdonation.php" method="POST" enctype="multipart/form-data" class="mt-4">
                    <h4 class="mb-3">Item Donation</h4>
                    <div class="mb-3">
                        <label for="item_name" class="form-label">Item Name</label>
                        <input type="text" class="form-control" id="item_name" name="item_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select" id="category" name="category" required>
                            <option value="Books">Books</option>
                            <option value="Clothes">Clothes</option>
                            <option value="Shoes">Shoes</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="condition" class="form-label">Condition</label>
                        <input type="text" class="form-control" id="condition" name="condition" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description (optional)</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="item_image" class="form-label">Upload Image</label>
                        <input type="file" class="form-control" id="item_image" name="item_image" accept="image/*" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100" name="donate_item">Donate Item</button>
                </form>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Messages -->
        <?php if ($success_msg): ?>
            <div class="alert alert-success"><?php echo $success_msg; ?></div>
        <?php elseif ($error_msg): ?>
            <div class="alert alert-danger"><?php echo $error_msg; ?></div>
        <?php elseif ($item_success_msg): ?>
            <div class="alert alert-success"><?php echo $item_success_msg; ?></div>
        <?php elseif ($item_error_msg): ?>
            <div class="alert alert-danger"><?php echo $item_error_msg; ?></div>
        <?php endif; ?>

        <div class="text-center mt-4">
            <a href="dashboard.php" class="btn btn-secondary">Return to Dashboard</a>
        </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
