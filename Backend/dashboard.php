<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php"); // Redirect to login if not logged in
    exit();
}
include 'db.php';

$user = $_SESSION['first_name'];

// Total donations (money + items)
$totalMoney = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM donatemoney WHERE username = '$user'"));
$totalItems = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM donateitem WHERE username = '$user'"));
$totalDonations = $totalMoney + $totalItems;

// Approved items
$approved = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM donateitem WHERE username = '$user' AND status = 'approved'"));

// Pending items
$pending = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM donateitem WHERE username = '$user' AND status = 'pending'"));

// Rejected items
$rejected = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM rejecteddonations WHERE username = '$user'"));

$total = $totalMoney + $approved + $pending + $rejected;

// Avoid divide-by-zero
if ($total == 0) {
    $moneyPercent = $approvedPercent = $pendingPercent = $rejectedPercent = 0;
} else {
    $moneyPercent = round(($totalMoney / $total) * 100);
    $approvedPercent = round(($approved / $total) * 100);
    $pendingPercent = round(($pending / $total) * 100);
    $rejectedPercent = 100 - ($moneyPercent + $approvedPercent + $pendingPercent); // remainder
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
     .sidebar a {
    color: white;
    text-decoration: none;
    padding: 12px;
    display: block;
    margin-bottom: 10px;
    border-radius: 8px;
    font-size: 17px;
    transition: all 0.3s ease;
    position: relative;
}
.sidebar a::after {
    content: '';
    position: absolute;
    height: 2px;
    width: 0;
    bottom: 6px;
    left: 15px;
    background-color: white;
    transition: width 0.3s ease;
}

.sidebar a:hover::after {
    width: 50%;
}
    body {
        background: linear-gradient(to right, #f1fdf1, #ffffff);
        font-family: 'Segoe UI', sans-serif;
        color: #333;
        margin: 0;
        padding: 0;
    }

    .sidebar {
        background: linear-gradient(to bottom, #14532d, #198754);
        color: white;
        height: 100vh;
        padding-top: 20px;
        width: 250px;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 999;
        padding-left: 20px;
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    }

    .sidebar h3 {
        font-weight: bold;
        text-align: center;
        margin-bottom: 30px;
    }

     .sidebar a {
        color: white;
        text-decoration: none;
        padding: 12px;
        display: block;
        margin-bottom: 10px;
        border-radius: 8px;
        font-size: 17px;
        transition: all 0.3s ease;
    } 

    .sidebar a:hover {
        background-color: #0f5132;
        transform: translateX(2px);
    } 

    .main-content {
        margin-left: 270px;
        padding: 20px 20px;
    }

    .card {
        background: white;
        border: 2px solid rgb(5, 60, 27);
        border-radius: 15px;
        box-shadow: 0 8px 20px rgba(14, 66, 15, 0.49);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        margin:10px;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.14);
    }

    .card-header {
        background: linear-gradient(to right, #198754, #14532d);
        color: white;
        font-weight: bold;
        border-radius: 25px;
        font-size: 20px;
        padding: 15px 20px;
    }

    .card-body {
        padding: 20px;
        font-size: 16px;
        color: #444;
    }

    .btn-primary,
    .btn-success {
        background: #198754;
        border: none;
        transition: background 0.3s ease, transform 0.3s ease;
    }

    .btn-primary:hover,
    .btn-success:hover {
        background: #14532d;
        transform: scale(1.05);
    }

    .dropdown-menu {
        background-color: #198754;
        border: none;
        border-radius: 10px;
    }

    .dropdown-item {
        color: white;
        padding: 10px 15px;
    }

    .dropdown-item:hover {
        background-color: #14532d;
        color: white;
    }

    @media (max-width: 768px) {
        .sidebar {
            width: 100%;
            height: auto;
            position: relative;
            padding-left: 10px;
            padding-right: 10px;
        }

        .sidebar h3 {
            margin-bottom: 15px;
            font-size: 1.5rem;
        }

        .sidebar a {
            font-size: 1rem;
            padding: 8px;
            margin-bottom: 5px;
        }
        .sidebar a::after {
            left: 8px;
            bottom: 3px;
        }

        .sidebar a:hover::after {
            width: 30%;
        }

        .main-content {
            margin-left: 0;
            padding: 10px;
        }

        .card-header {
            font-size: 1.2rem;
            padding: 10px;
        }
        .card-body {
            padding: 10px;
            font-size: 1rem;
        }
        .btn-primary,
        .btn-success {
            font-size: 0.9rem;
            padding: 8px 12px;
        }

        .col-md-3, .col-md-4, .col-md-12 {
            margin-bottom: 15px;
        }
    }

    @media (max-width: 576px) {
        .sidebar a {
            font-size: 0.9rem;
            padding: 6px;
        }
        .sidebar h3 {
            font-size: 1.3rem;
        }
        .card-header {
            font-size: 1.1rem;
        }
        .card-body {
            font-size: 0.9rem;
        }
        .btn-primary,
        .btn-success {
            font-size: 0.8rem;
            padding: 6px 10px;
        }
    } 
</style>

</head>
<body>

<!-- Sidebar -->
<div class="sidebar position-fixed top-0 left-0">
    <h3 class="text-white mb-4">Dashboard</h3>
    <a href="dashboard.php"><i class="bi bi-house-door me-2"></i> Home</a>

    <!-- <a href="dashboard.php">Home</a> -->
    <a href="profile.php">Profile</a> 

    <!-- Donations Dropdown -->
    <div class="dropdown">
        <a href="#" role="button" id="donationsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            Donations 🔽
        </a>
        <ul class="dropdown-menu w-100" aria-labelledby="donationsDropdown">
            <li><a class="dropdown-item" href="totaldonations.php?action=view_all">View Total Donations</a></li>
            <li><a class="dropdown-item" href="newdonation.php?action=make_new">Make a New Donation</a></li>
            <li><a class="dropdown-item" href="pending.php?action=pending">Pending Donations</a></li>
            <li><a class="dropdown-item" href="approved.php?action=approved">Approved Donations</a></li>
            <li><a class="dropdown-item" href="rejected.php?action=rejected">Rejected Donations</a></li>
        </ul>
    </div>
    <a href="userbadge.php">My Badge</a>

<a href="#" onclick="confirmLogout()" >Logout</a>

</div>
<!-- Main Content -->
<div class="main-content">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h2>Welcome, <?php echo $user; ?>!</h2>
                    </div>
                    <div class="card-body">
                        <p>Explore your dashboard to manage donations, profile, and more.</p>
                    </div>
                </div>
                <!-- Dashboard Overview -->
                <div class="row">
                    <!-- Quick Stats -->
<div class="row md-12">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-success"> Total Donations</h5>
                <p class="display-6 fw-bold"><?= $totalDonations ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-primary"><i class="bi bi-check-circle-fill"></i> Approved</h5>
                <p class="display-6 fw-bold"><?= $approved ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-warning"><i class="bi bi-hourglass-split"></i> Pending</h5>
                <p class="display-6 fw-bold"><?= $pending ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-danger"><i class="bi bi-x-circle-fill"></i> Rejected</h5>
                <p class="display-6 fw-bold"><?= $rejected ?></p>
            </div>
        </div>
    </div>
</div>
              <!-- User Profile Card -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">User Profile</div>
                            <div class="card-body text-center">
                                <p><strong>Email:</strong> <?php echo $_SESSION['email']; ?></p>
                                <p><a href="profile.php" class="btn btn-primary">View Profile</a></p>
                            </div>
                        </div>
                    </div>

                    <!-- Donations Card -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">Recent Donations</div>
                            <div class="card-body text-center">
                                <p>Recent Donations.</p>
                                <p><a href="recent.php?action=make_new" class="btn btn-success">Click to see</a></p>
                            </div>
                        </div>
                    </div> 
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">My Badge</div>
                            <div class="card-body text-center">
                                <p>Click to view my badge.</p>
                                <p><a href="userbadge.php" class="btn btn-success">View My Badge</a></p>
                            </div>
                        </div>
                    </div>
                <!-- Content for Donations -->
                <div class="row">
                    <?php
                    if (isset($_GET['action'])) {
                        $action = $_GET['action'];

                        if ($action == 'view_all') {
                            echo '<div class="col-12"><div class="card"><div class="card-body"><h4>Total Donations</h4><p>Displaying total donations here.</p></div></div></div>';
                        } elseif ($action == 'make_new') {
                            echo '<div class="col-12"><div class="card"><div class="card-body"><h4>Make a New Donation</h4><p>Form to make a donation will go here.</p></div></div></div>';
                        } elseif ($action == 'pending') {
                            echo '<div class="col-12"><div class="card"><div class="card-body"><h4>Pending Donations</h4><p>List of pending donations here.</p></div></div></div>';
                        } elseif ($action == 'approved') {
                            echo '<div class="col-12"><div class="card"><div class="card-body"><h4>Approved Donations</h4><p>List of approved donations here.</p></div></div></div>';
                        }
                    }
                    ?>
                </div>
                <!-- Donation Goal Progress -->
<div class="col-md-12 mt-4">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-3">Donation Activity Overview</h5>

            <div class="progress" style="height: 24px; font-size: 13px;">
                <div class="progress-bar bg-primary" role="progressbar" style="width: <?= $moneyPercent ?>%" aria-valuenow="<?= $moneyPercent ?>" aria-valuemin="0" aria-valuemax="100">
                    <?= $moneyPercent ?>% Money
                </div>
                <div class="progress-bar bg-success" role="progressbar" style="width: <?= $approvedPercent ?>%" aria-valuenow="<?= $approvedPercent ?>">
                    <?= $approvedPercent ?>% Approved
                </div>
                <div class="progress-bar bg-dark" role="progressbar" style="width: <?= $pendingPercent ?>%" aria-valuenow="<?= $pendingPercent ?>">
                    <?= $pendingPercent ?>% Pending
                </div>
                <div class="progress-bar bg-danger" role="progressbar" style="width: <?= $rejectedPercent ?>%" aria-valuenow="<?= $rejectedPercent ?>">
                    <?= $rejectedPercent ?>% Rejected
                </div>
            </div>

            <div class="mt-2 text-muted" style="font-size: 14px;">
                <i class="bi bi-info-circle"></i> Based on <?= $total ?> total donation activities by you.
            </div>
        </div>
    </div>
</div>
                <!-- Motivation or Info Card -->
<div class="col-md-12">
    <div class="card bg-light mt-4">
        <div class="card-body text-center">
            <h5 class="card-title text-success"><i class="bi bi-lightbulb-fill"></i> Did you know?</h5>
            <p class="card-text fst-italic"> "The best way to find yourself is to lose yourself in the service of others" </p>
        </div>
    </div>
</div>
            </div>
        </div>
    </div>
</div>
<footer class="text-center mt-5 py-3 text-muted">
    <small>© <?php echo date("Y"); ?> NeedNest. All rights reserved.</small>
</footer>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  function confirmLogout() {
    if (confirm("Are you sure you want to logout?")) {
      window.location.href = "logout.php";
    }
  }
</script>
</body>
</html>
