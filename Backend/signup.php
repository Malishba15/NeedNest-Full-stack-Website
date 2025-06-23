<?php
session_start();
$host = "localhost";
$db = "donations";
$user = "root";
$pass = "";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

// Signup
if (isset($_POST['signup'])) {
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if (!isset($_POST['terms'])) {
        $message = "You must agree to the terms and conditions.";
    } elseif ($password !== $confirmPassword) {
        $message = "Passwords do not match.";
    } else {
        // Don't hash password, store as plain text
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $firstName, $lastName, $email, $password);

        if ($stmt->execute()) {
            $message = "Signup successful. You can now log in.";
        } else {
            $message = "Signup failed: " . $stmt->error;
        }

        $stmt->close();
    }
}

// Login
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT password, first_name FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($storedPassword, $firstName);
        $stmt->fetch();

        // Direct password comparison (no hashing)
        if ($password === $storedPassword) {
            // Optional: start session and store user info
            $_SESSION['first_name'] = $firstName;
            $_SESSION['email'] = $email;

            header("Location: dashboard.php");
            exit(); // always call exit after header redirect
        } else {
            $message = "Incorrect password.";
        }

    } else {
        $message = "User not found.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login/Signup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #0d2f23;
            color: #fff;
        }
        .form-container {
            background-color: #14532d;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.4);
            margin-top: 80px;
        }
        .btn-primary {
            background-color: #1a4d2e;
            border-color: #1a4d2e;
        }
        .btn-primary:hover {
            background-color: #276749;
        }
        .toggle-btn {
            background: none;
            border: none;
            color: #bbf7d0;
            margin-top: 15px;
        }
    </style>
</head>
<body>
<div class="container d-flex justify-content-center">
    <div class="form-container col-md-6">
        <h2 class="text-center mb-4" id="form-title">Login</h2>

        <?php
        if (!empty($message)) {
            echo '<div class="alert alert-info">' . $message . '</div>';
        }
        ?>

        <!-- Login Form -->
        <form method="post" id="login-form">
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required />
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required />
            </div>
            <button type="submit" name="login" class="btn btn-primary w-100">Log In</button>
            <div class="text-center">
                <button type="button" class="toggle-btn" onclick="toggleForms()">Don't have an account? Sign up now</button>
            </div>
        </form>

        <!-- Signup Form -->
        <form method="post" id="signup-form" style="display: none;">
            <div class="mb-3">
                <label>First Name</label>
                <input type="text" name="first_name" class="form-control" required />
            </div>
            <div class="mb-3">
                <label>Last Name</label>
                <input type="text" name="last_name" class="form-control" required />
            </div>
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required />
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required />
            </div>
            <div class="mb-3">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" required />
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" name="terms" class="form-check-input" id="termsCheck" />
                <label class="form-check-label" for="termsCheck">I agree to the Terms and Conditions</label>
            </div>
            <button type="submit" name="signup" class="btn btn-primary w-100">Sign Up</button>
            <div class="text-center">
                <button type="button" class="toggle-btn" onclick="toggleForms()">Already have an account? Log in</button>
            </div>
        </form>
        <div class="d-flex justify-content-center mt-3">
    <a href="http://localhost:3000" class="btn btn-outline-light px-4">Back to Home</a>
</div>

    </div>
</div>

<script>
    function toggleForms() {
        const loginForm = document.getElementById('login-form');
        const signupForm = document.getElementById('signup-form');
        const title = document.getElementById('form-title');

        if (loginForm.style.display === "none") {
            loginForm.style.display = "block";
            signupForm.style.display = "none";
            title.innerText = "Login";
        } else {
            loginForm.style.display = "none";
            signupForm.style.display = "block";
            title.innerText = "Sign Up";
        }
    }
</script>
</body>
</html>
