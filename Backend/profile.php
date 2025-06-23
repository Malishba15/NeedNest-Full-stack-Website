<?php
session_start();
include 'db.php';

if (!isset($_SESSION['email'])) {
    header("Location: signup.php");
    exit();
}

$email = $_SESSION['email'];
$user_query = "SELECT * FROM users WHERE email = '$email'";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);

$update_msg = $password_msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Update Profile Info
    if (isset($_POST['update_profile'])) {
        $first_name = trim($_POST['first_name']);
        $last_name = trim($_POST['last_name']);
        $email_update = trim($_POST['email']);

        $update_sql = "UPDATE users SET first_name='$first_name', last_name='$last_name', email='$email_update' WHERE email='$email'";
        if (mysqli_query($conn, $update_sql)) {
            $_SESSION['email'] = $email_update; // Update session
            $update_msg = "Profile updated successfully.";
        } else {
            $update_msg = "Error updating profile: " . mysqli_error($conn);
        }
    }

    // Change Password
    if (isset($_POST['change_password'])) {
        $current_pass = $_POST['current_password'];
        $new_pass = $_POST['new_password'];
        $confirm_pass = $_POST['confirm_password'];

        if ($new_pass !== $confirm_pass) {
            $password_msg = "New passwords do not match.";
        } else {
            $check_sql = "SELECT * FROM users WHERE email = '$email' AND password = '$current_pass'";
            $check_result = mysqli_query($conn, $check_sql);

            if (mysqli_num_rows($check_result) > 0) {
                $update_pass_sql = "UPDATE users SET password='$new_pass' WHERE email='$email'";
                if (mysqli_query($conn, $update_pass_sql)) {
                    $password_msg = "Password changed successfully.";
                } else {
                    $password_msg = "Failed to update password.";
                }
            } else {
                $password_msg = "Current password is incorrect.";
            }
        }
    }

    // Re-fetch updated user data
    $user_result = mysqli_query($conn, $user_query);
    $user = mysqli_fetch_assoc($user_result);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Profile</title>
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
        .profile-box {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
            padding: 30px;
        }
        .form-control:focus {
            border-color: #28a745;
            box-shadow: none;
        }
        .section-title {
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
<div class="header">
    <h1>NeedNest</h1>
</div>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 profile-box">
            <h2 class="text-center mb-4">Welcome, <?php echo htmlspecialchars($user['first_name']); ?>!</h2>

            <?php if ($update_msg): ?>
                <div class="alert alert-info"><?php echo $update_msg; ?></div>
            <?php endif; ?>

            <!-- Profile Info Form -->
            <form method="POST">
                <div class="section-title">Your Information</div>
                <div class="row mb-3">
                    <div class="col">
                        <label>First Name</label>
                        <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                    </div>
                    <div class="col">
                        <label>Last Name</label>
                        <input type="text" name="last_name" class="form-control" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label>Email Address</label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                <button type="submit" name="update_profile" class="btn btn-success">Update Profile</button>
            </form>

            <!-- Change Password -->
            <form method="POST" class="mt-5">
                <div class="section-title">Change Password</div>

                <?php if ($password_msg): ?>
                    <div class="alert alert-warning"><?php echo $password_msg; ?></div>
                <?php endif; ?>

                <div class="mb-3">
                    <label>Current Password</label>
                    <input type="password" name="current_password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>New Password</label>
                    <input type="password" name="new_password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
                <button type="submit" name="change_password" class="btn btn-warning">Change Password</button>
            </form>

            <div class="mt-4 text-end">
                <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>
