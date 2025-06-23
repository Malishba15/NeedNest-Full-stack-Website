<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_email'])) {
    header("Location: adminlogin.php");
    exit();
}

$admin_email = $_SESSION['admin_email'];

$message = '';
$error = '';

// Fetch current admin info using email
$stmt = $conn->prepare("SELECT username, email FROM admins WHERE email = ?");
$stmt->bind_param("s", $admin_email);
$stmt->execute();
$stmt->bind_result($current_username, $current_email);
$stmt->fetch();
$stmt->close();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Update profile (username, email)
    if (isset($_POST['update_profile'])) {
        $new_username = trim($_POST['username']);
        $new_email = trim($_POST['email']);

        if (filter_var($new_email, FILTER_VALIDATE_EMAIL) && !empty($new_username)) {
            $update = $conn->prepare("UPDATE admins SET username = ?, email = ? WHERE email = ?");
            $update->bind_param("sss", $new_username, $new_email, $admin_email);
            if ($update->execute()) {
                $message = "Profile updated successfully.";
                $current_username = $new_username;
                $current_email = $new_email;
                $_SESSION['admin_email'] = $new_email;  // Update session email if changed
                $admin_email = $new_email; // Update current email variable
            } else {
                $error = "Error updating profile.";
            }
            $update->close();
        } else {
            $error = "Please enter a valid username and email.";
        }
    }

    // Change password
    if (isset($_POST['change_password'])) {
        $current_pass = $_POST['current_password'];
        $new_pass = $_POST['new_password'];
        $confirm_pass = $_POST['confirm_password'];

        if (empty($current_pass) || empty($new_pass) || empty($confirm_pass)) {
            $error = "Please fill all password fields.";
        } elseif ($new_pass !== $confirm_pass) {
            $error = "New passwords do not match.";
        } else {
            // Fetch current password hash using email
            $pass_stmt = $conn->prepare("SELECT password FROM admin WHERE email = ?");
            $pass_stmt->bind_param("s", $admin_email);
            $pass_stmt->execute();
            $pass_stmt->bind_result($hashed_password);
            $pass_stmt->fetch();
            $pass_stmt->close();

            if (password_verify($current_pass, $hashed_password)) {
                $new_hashed = password_hash($new_pass, PASSWORD_DEFAULT);
                $update_pass = $conn->prepare("UPDATE admin SET password = ? WHERE email = ?");
                $update_pass->bind_param("ss", $new_hashed, $admin_email);
                if ($update_pass->execute()) {
                    $message = "Password changed successfully.";
                } else {
                    $error = "Error updating password.";
                }
                $update_pass->close();
            } else {
                $error = "Current password is incorrect.";
            }
        }
    }

    // Add new admin (no change needed, uses username and email from form)
    if (isset($_POST['add_admin'])) {
        $new_admin_username = trim($_POST['new_admin_username']);
        $new_admin_email = trim($_POST['new_admin_email']);
        $new_admin_password = $_POST['new_admin_password'];
        $confirm_admin_password = $_POST['confirm_admin_password'];

        if (empty($new_admin_username) || empty($new_admin_email) || empty($new_admin_password) || empty($confirm_admin_password)) {
            $error = "Please fill all fields for new admin.";
        } elseif (!filter_var($new_admin_email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email format for new admin.";
        } elseif ($new_admin_password !== $confirm_admin_password) {
            $error = "New admin passwords do not match.";
        } else {
            // Check if username or email already exists
            $check_stmt = $conn->prepare("SELECT id FROM admins WHERE username = ? OR email = ?");
            $check_stmt->bind_param("ss", $new_admin_username, $new_admin_email);
            $check_stmt->execute();
            $check_stmt->store_result();
            if ($check_stmt->num_rows > 0) {
                $error = "Username or email already exists.";
            } else {
                $hashed_new_admin_pass = password_hash($new_admin_password, PASSWORD_DEFAULT);
                $insert_stmt = $conn->prepare("INSERT INTO admins (username, email, password) VALUES (?, ?, ?)");
                $insert_stmt->bind_param("sss", $new_admin_username, $new_admin_email, $hashed_new_admin_pass);
                if ($insert_stmt->execute()) {
                    $message = "New admin added successfully.";
                } else {
                    $error = "Error adding new admin.";
                }
                $insert_stmt->close();
            }
            $check_stmt->close();
        }
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Admin Settings</title>
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Roboto&display=swap" rel="stylesheet">
 <style>
  body {
    background: linear-gradient(135deg, #d4edda, #f0fdf4);
    font-family: 'Roboto', sans-serif;
    margin: 0;
    padding: 0;
    color: #333;
    margin:20px;
}

.header {
    background-color: #14532d;
    padding: 20px;
    text-align: center;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    margin-bottom: 40px;
    border-bottom: 4px solid #1e7e34;
}

.header h1 {
    font-family: 'Pacifico', cursive;
    font-size: 40px;
    color: #fff;
    margin: 0;
    letter-spacing: 1.5px;
}

.container {
    max-width: 750px;
    margin: 40px auto;
    background-color: #ffffff;
    padding: 35px;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

h2, h4 {
    color: #14532d;
    font-weight: 700;
    margin-bottom: 20px;
}

form {
    margin-bottom: 40px;
    padding-bottom: 20px;
    border-bottom: 1px solid #ccc;
}

form:last-of-type {
    border-bottom: none;
}

label {
    font-weight: 600;
    margin-bottom: 5px;
    display: block;
    color: #333;
}

input.form-control {
    background-color: #f6fff6;
    color: #333;
    border: 1px solid #c8e6c9;
    border-radius: 6px;
    padding: 10px;
    transition: all 0.3s ease;
}

input.form-control:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    background-color: #fff;
}

.btn-primary, .btn-success {
    background-color: #28a745;
    border: none;
    padding: 10px 20px;
    font-weight: 600;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

.btn-primary:hover, .btn-success:hover {
    background-color: #1e7e34;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
    padding: 12px;
    border-radius: 5px;
    margin-bottom: 20px;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
    padding: 12px;
    border-radius: 5px;
    margin-bottom: 20px;
}

@media (max-width: 768px) {
    .container {
        margin: 20px;
        padding: 20px;
    }

    h2, h4 {
        font-size: 20px;
    }
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
    <h2>Admin Profile Settings</h2>

    <?php if ($message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Update Profile Form -->
    <form method="POST" class="mb-4">
        <h4>Update Profile</h4>
        <div class="mb-3">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" class="form-control" value="<?= htmlspecialchars($current_username) ?>" required />
        </div>
        <div class="mb-3">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($current_email) ?>" required />
        </div>
        <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
    </form>

    <!-- Change Password Form -->
    <form method="POST" class="mb-4">
        <h4>Change Password</h4>
        <div class="mb-3">
            <label for="current_password">Current Password:</label>
            <input type="password" name="current_password" id="current_password" class="form-control" required />
        </div>
        <div class="mb-3">
            <label for="new_password">New Password:</label>
            <input type="password" name="new_password" id="new_password" class="form-control" required />
        </div>
        <div class="mb-3">
            <label for="confirm_password">Confirm New Password:</label>
            <input type="password" name="confirm_password" id="confirm_password" class="form-control" required />
        </div>
        <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
    </form>

    <!-- Add New Admin Form -->
    <form method="POST">
        <h4>Add New Admin</h4>
        <div class="mb-3">
            <label for="new_admin_username">Username:</label>
            <input type="text" name="new_admin_username" id="new_admin_username" class="form-control" required />
        </div>
        <div class="mb-3">
            <label for="new_admin_email">Email:</label>
            <input type="email" name="new_admin_email" id="new_admin_email" class="form-control" required />
        </div>
        <div class="mb-3">
            <label for="new_admin_password">Password:</label>
            <input type="password" name="new_admin_password" id="new_admin_password" class="form-control" required />
        </div>
        <div class="mb-3">
            <label for="confirm_admin_password">Confirm Password:</label>
            <input type="password" name="confirm_admin_password" id="confirm_admin_password" class="form-control" required />
        </div>
        <button type="submit" name="add_admin" class="btn btn-success">Add Admin</button>
    </form>
</div>

</body>
<div class="text-center mt-4">
        <a href="admindashboard.php" class="btn btn-secondary px-4 py-2">Return to Dashboard</a>
    </div>
</html>
