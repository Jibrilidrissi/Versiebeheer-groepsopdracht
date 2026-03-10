<?php
// 1. DATABASE CONNECTION
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "versiebeheer";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// 2. SAVE LOGIC (Handles the 'Create' form submission)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sql = "INSERT INTO customers (Name, Email, Phone) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$_POST['name'], $_POST['email'], $_POST['mobile']]);
    header("Location: index.php"); // Go back to the grid
    exit();
}

$view = isset($_GET['action']) ? $_GET['action'] : 'list';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PHP CRUD</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

<div class="container">
    <?php if ($view == 'create'): ?>
        <h2>Create a Customer</h2>
        <form method="post">
            <div class="field"><label>Name</label><input name="name" required></div>
            <div class="field"><label>Email</label><input name="email" type="email" required></div>
            <div class="field"><label>Mobile</label><input name="mobile" required></div>
            <div class="footer">
                <button type="submit" class="btn green">Create</button>
                <a href="index.php" class="btn">Back</a>
            </div>
        </form>

    <?php else: ?>
        <h2>PHP CRUD Grid</h2>
        <a href="index.php?action=create" class="btn green">Create</a>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email Address</th>
                    <th>Mobile Number</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $data = $conn->query("SELECT * FROM customers")->fetchAll();
                foreach ($data as $row): 
                ?>
                <tr>
                    <td><?= htmlspecialchars($row['Name']) ?></td>
                    <td><?= htmlspecialchars($row['Email']) ?></td>
                    <td><?= htmlspecialchars($row['Phone']) ?></td>
                    <td><a href="#" class="btn-s">Read</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</body>
</html>