<?php
$conn = new mysqli("localhost", "root", "", "donations");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$message = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = (int)$_POST["id"];
    $content = $conn->real_escape_string($_POST["content"]);
    $sql = "UPDATE mission_vision SET content='$content' WHERE id=$id";
    $message = $conn->query($sql) ? "✅ Updated successfully" : "❌ Error: " . $conn->error;
}

$results = $conn->query("SELECT * FROM mission_vision");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Edit Mission & Vision | NeedNest</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Roboto&display=swap" rel="stylesheet" />
    <style>
        body {
            background-color: #f0fdf4;
            font-family: 'Roboto', sans-serif;
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            margin:20px;
        }

        .header {
            background-color: #14532d;
            padding: 25px 15px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.25);
            border-bottom: 4px solid #1e7e34;
            margin-bottom: 40px;
        }

        .header h1 {
            font-family: 'Pacifico', cursive;
            font-size: 3rem;
            color: #d4edda;
            margin: 0;
            letter-spacing: 3px;
        }

        .container {
            flex-grow: 1;
            max-width: 700px;
            background: white;
            padding: 40px 30px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        h2 {
            font-weight: 700;
            margin-bottom: 30px;
            text-align: center;
            color: #14532d;
        }

        form {
            border: 1px solid #d1e7dd;
            padding: 25px 20px;
            margin-bottom: 30px;
            border-radius: 8px;
            background-color: #e9f7ef;
            transition: box-shadow 0.3s ease;
        }

        form:hover {
            box-shadow: 0 0 12px #1e7e34aa;
        }

        label.form-label {
            font-size: 1.15rem;
            color: #14532d;
            margin-bottom: 10px;
            display: block;
        }

        textarea.form-control {
            resize: vertical;
            font-size: 1rem;
            border: 1.5px solid #14532d;
            transition: border-color 0.3s ease;
        }

        textarea.form-control:focus {
            border-color: #1e7e34;
            box-shadow: 0 0 6px #1e7e34aa;
        }

        button.btn-success {
            background-color: #14532d;
            border: none;
            padding: 10px 25px;
            font-size: 1rem;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        button.btn-success:hover {
            background-color: #1e7e34;
        }

        .alert {
            max-width: 700px;
            margin: 0 auto 40px;
            font-size: 1.1rem;
            border-radius: 8px;
        }

        .back-btn-container {
            text-align: center;
            margin: 30px 0 50px;
        }

        .btn-secondary {
            background-color: #14532d;
            border: none;
            padding: 12px 40px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 6px;
            transition: background-color 0.3s ease;
            color: #d4edda;
        }

        .btn-secondary:hover {
            background-color: #1e7e34;
            color: #fff;
            text-decoration: none;
        }

        @media (max-width: 768px) {
            .container {
                padding: 30px 20px;
                margin: 0 10px;
            }
            button.btn-success, .btn-secondary {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>NeedNest</h1>
    </div>

    <div class="container">
        <h2>Edit Mission & Vision</h2>

        <?php if ($message): ?>
            <div class="alert alert-info text-center"><?= $message ?></div>
        <?php endif; ?>

        <?php while ($row = $results->fetch_assoc()): ?>
            <form method="POST" autocomplete="off">
                <input type="hidden" name="id" value="<?= $row['id'] ?>" />
                <label class="form-label"><strong><?= htmlspecialchars($row['title']) ?></strong></label>
                <textarea name="content" rows="5" class="form-control" required><?= htmlspecialchars($row['content']) ?></textarea>
                <button type="submit" class="btn btn-success mt-3">Update</button>
            </form>
        <?php endwhile; ?>
    </div>

    <div class="back-btn-container">
        <a href="admindashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
    </div>
</body>
</html>
