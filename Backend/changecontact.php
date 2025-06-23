<?php
$conn = new mysqli("localhost", "root", "", "donations");
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $email = $_POST["email"];
  $address = $_POST["address"];
  $instagram = $_POST["instagram"];
  $facebook = $_POST["facebook"];
  $linkedin = $_POST["linkedin"];

  $sql = "UPDATE contact_info SET 
          email = ?, address = ?, instagram = ?, facebook = ?, linkedin = ? 
          WHERE id = 1";

  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sssss", $email, $address, $instagram, $facebook, $linkedin);
  $stmt->execute();
  $success = "Contact info updated!";
}

$row = $conn->query("SELECT * FROM contact_info LIMIT 1")->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Edit Contact Info</title>
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Roboto&display=swap" rel="stylesheet">
  <style>
    body {
        background-color: #f0fdf4;
        color: #f8f9fa;
        font-family: 'Roboto', sans-serif;
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
    }.btn-secondary {
        background-color: #14532d;
        border: none;
        transition: 0.3s;
    }

    .btn-secondary:hover {
        background-color: #1e7e34;
    }

    </style>
</head>
<div class="header">
    <h1>NeedNest</h1>
</div>

<body>
  <div class="container mt-5" style="color: #14532d">
    <h2>Edit Contact Info</h2>
    <?php if ($success): ?>
      <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    <form method="POST">
      <div class="mb-3">
        <label>Email</label>
        <input type="text" name="email" class="form-control" value="<?php echo $row['email']; ?>">
      </div>
      <div class="mb-3">
        <label>Address</label>
        <textarea name="address" class="form-control"><?php echo $row['address']; ?></textarea>
      </div>
      <div class="mb-3">
        <label>Instagram URL</label>
        <input type="text" name="instagram" class="form-control" value="<?php echo $row['instagram']; ?>">
      </div>
      <div class="mb-3">
        <label>Facebook URL</label>
        <input type="text" name="facebook" class="form-control" value="<?php echo $row['facebook']; ?>">
      </div>
      <div class="mb-3">
        <label>LinkedIn URL</label>
        <input type="text" name="linkedin" class="form-control" value="<?php echo $row['linkedin']; ?>">
      </div>
      <button type="submit" class="btn btn-success">Update</button>
    </form>
  </div>
      <div class="text-center mt-4">
        <a href="admindashboard.php" class="btn btn-secondary px-4 py-2">Back to Dashboard</a>
    </div>
</body>
</html>
