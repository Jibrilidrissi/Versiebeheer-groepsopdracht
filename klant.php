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


// CREATE (Add new customer)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sql = "INSERT INTO customers (name, email, phone) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        $_POST['name'],
        $_POST['email'],
        $_POST['mobile']
    ]);

    header("Location: klant.php");
    exit();
}


// DELETE (Remove customer)
if (isset($_GET['delete'])) {
    $stmt = $conn->prepare("DELETE FROM customers WHERE id = ?");
    $stmt->execute([$_GET['delete']]);

    header("Location: klant.php");
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

<div class="field">
<label>Name</label>
<input name="name" required>
</div>

<div class="field">
<label>Email</label>
<input name="email" type="email" required>
</div>

<div class="field">
<label>Mobile</label>
<input name="mobile" required>
</div>

<div class="footer">
<button type="submit" class="btn green">Create</button>
<a href="klant.php" class="btn">Back</a>
</div>

</form>

<?php else: ?>

<h2>PHP CRUD Grid</h2>

<a href="klant.php?action=create" class="btn green">Create</a>

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
$data = $conn->query("SELECT * FROM customers")->fetchAll(PDO::FETCH_ASSOC);

foreach ($data as $row):
?>

<tr>

<td><?= htmlspecialchars($row['name']) ?></td>
<td><?= htmlspecialchars($row['email']) ?></td>
<td><?= htmlspecialchars($row['phone']) ?></td>

<td>
<a 
href="klant.php?delete=<?= $row['id'] ?>" 
class="btn-s"
onclick="return confirm('Delete this customer?')"
>
Delete
</a>
</td>

</tr>

<?php endforeach; ?>

</tbody>
</table>

<?php endif; ?>

</div>

</body>
</html>