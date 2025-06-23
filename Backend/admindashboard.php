<?php
session_start();

// Optional login check
if (!isset($_SESSION['admin_email'])) {
    header("Location: adminlogin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Roboto&display=swap" rel="stylesheet">
   <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  
</div>

  <style>
    body {
        background-color: #f0fdf4;
        font-family: 'Roboto', sans-serif;
        color: #333;
        margin: 30px
    
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

        /* .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            transition: transform 0.2s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-title {
            font-size: 1.25rem;
            color: #3ca55c;
            font-weight: bold;
        } */
.card {
  border: none;
  border-radius: 20px;
  background: linear-gradient(145deg, #e8f5e9, #c8e6c9);
  box-shadow:
    4px 4px 15px rgba(0, 0, 0, 0.12),
    -4px -4px 15px rgba(255, 255, 255, 0.7);
  padding: 2rem;
  transition: all 0.4s ease;
  cursor: pointer;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  color: #2e7d32;
}

.card:hover {
  background: linear-gradient(145deg, #c8e6c9, #a5d6a7);
  box-shadow:
    8px 8px 25px rgba(0, 0, 0, 0.18),
    -8px -8px 25px rgba(255, 255, 255, 0.9);
  transform: translateY(-8px);
}

.card-title {
  font-size: 1.2rem;
  font-weight: 700;
  letter-spacing: 0.05em;
  margin-bottom: 1rem;
  color: #1b5e20;
  text-shadow: 1px 1px 1px rgba(255,255,255,0.6);
  /* font-family: 'Pacifico', cursive; */
}

.card-text {
  color: #4a7c4a;
  font-weight: 300;
  font-size: 1rem;
  line-height: 1.5;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .card {
    padding: 1.5rem;
  }

  .card-title {
    font-size: 1.3rem;
  }
}
    
        .dashboard-heading {
            margin-top: 30px;
            color: #3ca55c;
            text-align: center;
            font-weight: bold;
        }

         #donationChart {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        padding: 20px;
    }

    .dropdown {
        position: absolute;
        top: 25px;
        right: 25px;
    }

   /* Sidebar styles */
.sidebar {
  height: 100vh;
  width: 0;
  position: fixed;
  z-index: 1050;
  top: 0;
  left: 0;
  background-color: #14532d;
  overflow-x: hidden;
  transition: width 0.3s ease;
  padding-top: 60px;
  color: white;
  box-shadow: 2px 0 5px rgba(0,0,0,0.5);
}

.sidebar.open {
  width: 250px;
}

.sidebar-menu li {
  padding: 15px 25px;
}

.sidebar-menu li a {
  color: #d4edda;
  text-decoration: none;
  font-weight: 500;
  display: block;
}

.sidebar-menu li a:hover {
  background-color: #1e7e34;
  border-radius: 5px;
}

.close-btn {
  position: absolute;
  top: 15px;
  right: 20px;
  font-size: 30px;
  background: none;
  border: none;
  color: white;
  cursor: pointer;
}

/* Main content container */
#mainContent {
  transition: margin-left 0.3s ease;
  margin-left: 0;
  padding: 20px;
}

/* When sidebar open, push content */
#mainContent.shifted {
  margin-left: 250px;
}


    </style>
</head>
<body>


<!-- Include Bootstrap and Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">


<div class="header d-flex justify-content-between align-items-center p-3" style="color: white;">
    <button id="openSidebar" class="btn btn-success me-3" style="font-size: 1.5rem; padding: 5px 10px;">&#9776;</button>
  
    <h1 class="m-0" style="margin: 25px;">NeedNest</h1>

    <!-- Three-dot Dropdown Menu -->
    <div class="dropdown">
        <i class="bi bi-three-dots-vertical" id="adminMenu" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 1.5rem; cursor: pointer;"></i>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminMenu">
            <li><a class="dropdown-item" href="adminsetting.php">Admin Settings</a></li>
            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
        </ul>
    </div>
</div>

<!-- Sidebar -->
<div id="sidebar" class="sidebar">
  <button id="closeSidebar" class="close-btn">&times;</button>
   <div class="sidebar-heading text-center py-3">
    <h4 class="m-0">Admin Dashboard</h4>
  </div>
  <ul class="sidebar-menu list-unstyled ps-3">
    <li><a href="manageusers.php">Manage Users</a></li>
    <li><a href="admindonations.php">Total Donations</a></li>
    <li><a href="report.php">Reports</a></li>
    <li><a href="adminpending.php">Pending Donations</a></li>
    <li><a href="adminapprove.php">Approved Donations</a></li>
    <li><a href="adminbadge.php">Give Badge</a></li>
    <li><a href="changecontact.php">Change Contact Info</a></li>
    <li><a href="manageTestimonial.php">Edit Testimonials</a></li>
    <li><a href="manage_mission_vision.php">Edit Mission & Vision</a></li>
  </ul>
</div>


<!-- Add Bootstrap JS for dropdown to work -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


<!-- Dashboard -->
 <div id="mainContent">
<div class="container mt-4">
    <h2 class="dashboard-heading">Welcome to the Admin Dashboard</h2>

    <div class="row mt-5">
        <div class="col-md-4 mb-4">
            <div class="card p-4">
                <div class="card-body">
                    <h5 class="card-title">Manage Users</h5>
                    <p class="card-text">Click to view, edit, or delete user accounts.</p>
                    <a href="manageusers.php" class="btn btn-success">Go to Users</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card p-4">
                <div class="card-body">
                    <h5 class="card-title">Total Donations</h5>
                    <p class="card-text">Click to view total donations of NeedNest</p>
                    <a href="admindonations.php" class="btn btn-success">Review Items</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card p-4">
                <div class="card-body">
                    <h5 class="card-title">Reports</h5>
                    <p class="card-text">View reports and analytics about platform activity.</p>
                    <a href="report.php" class="btn btn-success">View Reports</a>
                </div>
            </div>
        </div>

       <div class="col-md-4 mb-4">
            <div class="card p-4">
                <div class="card-body">
                    <h5 class="card-title">Pending Donations</h5>
                    <p class="card-text">Review and approve pending item listings..</p>
                    <a href="adminpending.php" class="btn btn-success">View Donations</a>
                </div>
            </div>
        </div> <div class="col-md-4 mb-4">
            <div class="card p-4">
                <div class="card-body">
                    <h5 class="card-title">Approved Donations</h5>
                    <p class="card-text">View reports and analytics about platform activity.</p>
                    <a href="adminapprove.php" class="btn btn-success">View Reports</a>
                </div>
            </div>
        </div> <div class="col-md-4 mb-4">
            <div class="card p-4">
                <div class="card-body">
                    <h5 class="card-title">Give Badge</h5>
                    <p class="card-text">Assign users badge according to their donations.</p>
                    <a href="adminbadge.php" class="btn btn-success">View Badges</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card p-4">
                <div class="card-body">
                    <h5 class="card-title">Change Contact Info</h5>
                    <p class="card-text">View and Change Website Contact Us Information.</p>
                    <a href="changecontact.php" class="btn btn-success">Change Contact Info</a>
                </div>
            </div>
        </div> <div class="col-md-4 mb-4">
            <div class="card p-4">
                <div class="card-body">
                    <h5 class="card-title">Edit Testimonials</h5>
                    <p class="card-text">View and update the testimonials on the website.</p>
                    <a href="manageTestimonial.php" class="btn btn-success">View Testimonials</a>
                </div>
            </div>
        </div> <div class="col-md-4 mb-4">
            <div class="card p-4">
                <div class="card-body">
                    <h5 class="card-title">Edit Mission & Vision</h5>
                    <p class="card-text">Update the mission and vision statements of the organization.</p>
                    <a href="manage_mission_vision.php" class="btn btn-success">Edit Mission & Vision</a>
                </div>
            </div>
        </div>

       

<script>
  const sidebar = document.getElementById('sidebar');
  const mainContent = document.getElementById('mainContent');
  const openBtn = document.getElementById('openSidebar');
  const closeBtn = document.getElementById('closeSidebar');

  openBtn.addEventListener('click', () => {
    sidebar.classList.add('open');
    mainContent.classList.add('shifted');
  });

  closeBtn.addEventListener('click', () => {
    sidebar.classList.remove('open');
    mainContent.classList.remove('shifted');
  });

  // Optional: Close sidebar if clicking outside
  window.addEventListener('click', (e) => {
    if (!sidebar.contains(e.target) && !openBtn.contains(e.target)) {
      sidebar.classList.remove('open');
      mainContent.classList.remove('shifted');
    }
  });
</script>
        </div>
</body>
</html>

