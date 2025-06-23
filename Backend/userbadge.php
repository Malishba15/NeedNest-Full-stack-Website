<?php
session_start();
include 'db.php';

if (!isset($_SESSION['email'])) {
    header("Location: signup.php");
    exit();
}

$username = $_SESSION['first_name'];

// Fetch user's donation count
$query = "
    SELECT 
        (SELECT COUNT(*) FROM donatemoney WHERE username = ?) +
        (SELECT COUNT(*) FROM donateitem WHERE username = ?) AS total_donations
";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $username, $username);
$stmt->execute();
$stmt->bind_result($total_donations);
$stmt->fetch();
$stmt->close();

// Define badge
$badge = "No Badge";
if ($total_donations >= 7) $badge = "Platinum";
elseif ($total_donations >= 5) $badge = "Silver";
elseif ($total_donations >= 3) $badge = "Gold";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Badge</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Roboto&display=swap" rel="stylesheet">
    <style>
        .header {
            background-color: #14532d;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.3);
            margin-bottom: 40px;
            margin-top: 0;
            border-bottom: 4px solid #1e7e34;
        }

        .btn-secondary {
            background-color: #14532d;
            border: none;
            transition: 0.3s;
            margin-top: 30px;
        }

        .btn-secondary:hover {
            background-color: #1e7e34;
        }

        body {
            background-color: #0f2e0f;
            color: #d4f8d4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: 'Segoe UI', sans-serif;
            flex-direction: column;
        }

        .badge-box {
            background-color: #1c4d1c;
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 0 30px #28a745aa;
            animation: glow 2s ease-in-out infinite alternate;
        }

        @keyframes glow {
            from { box-shadow: 0 0 15px #28a745; }
            to { box-shadow: 0 0 40px #28a745, 0 0 60px #28a745; }
        }

        .badge-title {
            font-size: 2.2rem;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .badge-icon {
            font-size: 6rem;
            margin-bottom: 20px;
            color: #fff;
        }

        .badge-platinum { color: #e5e4e2; }
        .badge-gold { color: #ffd700; }
        .badge-silver { color: #c0c0c0; }
        .badge-none { color: #6c757d; }

        .donation-info {
            font-size: 1.1rem;
            color: #cdeccd;
        }

        .donate-btn {
            background-color: #198754;
            color: white;
            border: none;
            padding: 10px 20px;
            margin-top: 20px;
            border-radius: 10px;
            font-size: 1rem;
        }

        .donate-btn:hover {
            background-color: #157347;
        }
    </style>
</head>
<body>
<div class="badge-box">
    <?php if ($total_donations > 0): ?>
        <div class="badge-title">Congratulations, <?= htmlspecialchars($username) ?>!</div>

        <div class="badge-icon 
            <?= $badge === 'Platinum' ? 'badge-platinum' : 
                 ($badge === 'Gold' ? 'badge-gold' : 
                 ($badge === 'Silver' ? 'badge-silver' : 'badge-none')) ?>">
            🏅
        </div>

        <h3><?= $badge ?> Badge</h3>
        <p class="donation-info">You have donated <?= $total_donations ?> time(s).</p>
    
    <?php else: ?>
        <div class="badge-title">No Badge Yet</div>

        <div class="badge-icon badge-none">🚫</div>

        <h3>No Badge Assigned</h3>
        <p class="donation-info">
            You haven’t made any donations yet.<br>
            Start donating to earn badges and make an impact!
        </p>
        <a href="newdonation.php" class="donate-btn">Click to Donate</a>
    <?php endif; ?>
</div>



<div class="text-center mt-4">
    <a href="dashboard.php" class="btn btn-secondary">Return to Dashboard</a>
</div>

</body>
</html>
