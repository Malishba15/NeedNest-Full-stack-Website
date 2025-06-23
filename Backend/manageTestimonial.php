<?php
$conn = new mysqli("localhost", "root", "", "donations");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"];
    $id = isset($_POST["id"]) ? (int)$_POST["id"] : 0;
    $text = $conn->real_escape_string($_POST["text"]);
    $author = $conn->real_escape_string($_POST["author"]);
    $role = $conn->real_escape_string($_POST["role"]);

    if ($action === "add") {
        $sql = "INSERT INTO testimonials (text, author, role) VALUES ('$text', '$author', '$role')";
        $message = $conn->query($sql) ? "✅ Testimonial added!" : "❌ Error: " . $conn->error;
    } elseif ($action === "update" && $id > 0) {
        $sql = "UPDATE testimonials SET text='$text', author='$author', role='$role' WHERE id=$id";
        $message = $conn->query($sql) ? "✅ Testimonial updated!" : "❌ Error: " . $conn->error;
    } elseif ($action === "delete" && $id > 0) {
        $sql = "DELETE FROM testimonials WHERE id=$id";
        $message = $conn->query($sql) ? "✅ Testimonial deleted!" : "❌ Error: " . $conn->error;
    }
}

$testimonials = $conn->query("SELECT * FROM testimonials");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Testimonials</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Roboto&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f0fdf4;
            font-family: 'Roboto', sans-serif;
            color: #212529;
            margin:20px;
        }
        .header {
            background-color: #14532d;
            padding: 30px;
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
        .container {
            max-width: 900px;
            margin: auto;
        }
        .msg {
            margin-bottom: 20px;
            padding: 10px 20px;
            border-radius: 5px;
            background-color: #d4edda;
            color: #155724;
        }
        .form-section {
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 40px;
        }
        textarea {
            resize: vertical;
        }
        .btn-secondary {
            background-color: #14532d;
            border: none;
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

    <h2 class="mb-4 text-center">Manage Testimonials</h2>

    <?php if ($message): ?>
        <div class="msg"><?= $message ?></div>
    <?php endif; ?>

    <div class="form-section">
        <form method="POST" action="">
            <input type="hidden" name="id" id="testimonialId">

            <div class="mb-3">
                <label for="text" class="form-label">Text</label>
                <textarea class="form-control" name="text" id="text" rows="3" required></textarea>
            </div>

            <div class="mb-3">
                <label for="author" class="form-label">Author</label>
                <input type="text" class="form-control" name="author" id="author" required>
            </div>

            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <input type="text" class="form-control" name="role" id="role" required>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" name="action" value="add" class="btn btn-success">Add</button>
                <button type="submit" name="action" value="update" class="btn btn-warning">Update</button>
                <button type="submit" name="action" value="delete" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this testimonial?')">Delete</button>
            </div>
        </form>
    </div>

    <h3 class="mb-3">Existing Testimonials</h3>
    <div class="table-responsive">
        <table class="table table-bordered table-hover bg-white">
            <thead class="table-success">
                <tr>
                    <th>ID</th>
                    <th>Text</th>
                    <th>Author</th>
                    <th>Role</th>
                    <th>Edit</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $testimonials->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['text']) ?></td>
                    <td><?= $row['author'] ?></td>
                    <td><?= $row['role'] ?></td>
                    <td>
                        <button class="btn btn-sm btn-secondary"
                            onclick="fillForm(<?= $row['id'] ?>, `<?= htmlspecialchars($row['text'], ENT_QUOTES) ?>`, `<?= $row['author'] ?>`, `<?= $row['role'] ?>`)">
                            Edit
                        </button>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function fillForm(id, text, author, role) {
        document.getElementById("testimonialId").value = id;
        document.getElementById("text").value = text;
        document.getElementById("author").value = author;
        document.getElementById("role").value = role;
    }
</script>

</body>
<div class="text-center mt-4">
        <a href="admindashboard.php" class="btn btn-secondary px-4 py-2">Back to Dashboard</a>
    </div>
</html>
