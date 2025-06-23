<?php
session_start();
include 'db.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = "SELECT * FROM admins WHERE email='$email'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) == 1) {
        $admin = mysqli_fetch_assoc($result);
        if ($admin['password'] === $password) {
            $_SESSION['admin_email'] = $admin['email'];
            header("Location: admindashboard.php");
            exit();
        } else {
            $error = "Invalid email or password";
        }
    } else {
        $error = "Admin not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #0d1f1e;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            font-family: 'Segoe UI', sans-serif;
        }
        .card {
            background-color: #1a3c34;
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.2);
            color: #e0f2f1;
        }
        .card-header {
            background: #14532d;
            color: #ffffff;
            font-size: 1.5rem;
            text-align: center;
            border-radius: 12px 12px 0 0;
            padding: 1rem;
            font-weight: bold;
        }
        .form-label {
            color: #d1fae5;
        }
        .form-control {
            background-color: #16332c;
            border: 1px solid #388e3c;
            color: #e0f2f1;
        }
        .form-control:focus {
            background-color: #16332c;
            color: #ffffff;
            border-color: #66bb6a;
            box-shadow: none;
        }
        .btn-login {
            background-color: #2e7d32;
            border: none;
            color: white;
            font-weight: bold;
            width: 100%;
            transition: background-color 0.3s ease;
        }
        .btn-login:hover {
            background-color: #1b5e20;
        }
        .error {
            color: #ffbaba;
            background-color: #5f2120;
            padding: 10px;
            margin-top: 15px;
            text-align: center;
            border-radius: 8px;
        }
    </style>
</head>
<body>

<div class="card" style="width: 400px;">
    <div class="card-header">
        Admin Login
    </div>
    <form method="POST" class="p-3">
        <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <input required type="email" name="email" class="form-control" id="email">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input required type="password" name="password" class="form-control" id="password">
        </div>
        <button type="submit" class="btn btn-login">Login</button>
        <?php if ($error): ?>
            <div class="error mt-3"><?php echo $error; ?></div>
        <?php endif; ?>
        
    </form>
    <div class="text-center mt-3">
    <a href="http://localhost:3000" class="btn btn-outline-light">Back to Home</a>
</div>

</div>

</body>
</html>
