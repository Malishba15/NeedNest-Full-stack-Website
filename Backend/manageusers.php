<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $id = $_POST['id'];

        if ($_POST['action'] == 'delete') {
            mysqli_query($conn, "DELETE FROM users WHERE id = $id");
            echo "deleted";
            exit();
        }

        if ($_POST['action'] == 'update') {
            $firstname = $_POST['firstname'];
            $email = $_POST['email'];
            mysqli_query($conn, "UPDATE users SET firstname='$firstname', email='$email' WHERE id=$id");
            echo "updated";
            exit();
        }
    }
}

$users = mysqli_query($conn, "SELECT * FROM users");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
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

        .dots {
            cursor: pointer;
            font-size: 24px;
            position: relative;
        }
        .dropdown {
            position: absolute;
            right: 0;
            top: 35px;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: none;
            min-width: 150px;
            z-index: 999;
        }
        .dropdown a {
            display: block;
            padding: 10px 15px;
            color: black;
            text-decoration: none;
        }
        .dropdown a:hover {
            background-color: #f0f0f0;
        }
        .table-container {
            margin: 30px auto;
            width: 90%;
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .btn-green {
            background: green;
            color: white;
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

<div class="table-container">
    <h2 class="mb-4 text-center">Manage Users</h2>
    <table class="table table-bordered align-middle text-center">
        <thead class="table-success">
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Email</th>
                <th>Password (hidden)</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="userTable">
            <?php while ($row = mysqli_fetch_assoc($users)) : ?>
                <tr data-id="<?= $row['id'] ?>">
                    <td><?= $row['id'] ?></td>
                    <td class="firstname"><?= htmlspecialchars($row['first_name']) ?></td>
                    <td class="email"><?= htmlspecialchars($row['email']) ?></td>
                    <td>********</td>
                    <td>
                        <button class="btn btn-sm btn-green editBtn">Edit</button>
                        <button class="btn btn-sm btn-danger deleteBtn">Delete</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
    document.querySelectorAll('.editBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            const row = this.closest('tr');
            const firstnameTd = row.querySelector('.firstname');
            const emailTd = row.querySelector('.email');

            if (this.textContent === 'Edit') {
                const firstname = firstnameTd.textContent;
                const email = emailTd.textContent;
                firstnameTd.innerHTML = `<input type="text" class="form-control" value="${firstname}">`;
                emailTd.innerHTML = `<input type="email" class="form-control" value="${email}">`;
                this.textContent = 'Save';
                this.classList.remove('btn-green');
                this.classList.add('btn-success');
            } else {
                const id = row.getAttribute('data-id');
                const newFirstname = firstnameTd.querySelector('input').value;
                const newEmail = emailTd.querySelector('input').value;

                fetch('', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=update&id=${id}&firstname=${encodeURIComponent(newFirstname)}&email=${encodeURIComponent(newEmail)}`
                }).then(() => {
                    firstnameTd.textContent = newFirstname;
                    emailTd.textContent = newEmail;
                    this.textContent = 'Edit';
                    this.classList.remove('btn-success');
                    this.classList.add('btn-green');
                });
            }
        });
    });

    document.querySelectorAll('.deleteBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            if (confirm("Are you sure you want to delete this user?")) {
                const row = this.closest('tr');
                const id = row.getAttribute('data-id');

                fetch('', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=delete&id=${id}`
                }).then(() => {
                    row.remove();
                });
            }
        });
    });
</script>
 
        <div class="text-center mt-4">
            <a href="admindashboard.php" class="btn btn-secondary">Return to Dashboard</a>
        </div>
</body>
</html>