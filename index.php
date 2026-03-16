<?php
session_start();

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

$error = "";

// 2. LOGIN LOGIC
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST["email"];
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT * FROM admins WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Check if password matches (Supports both PHP Hash and Plain Text for your 'sap' user)
        if (password_verify($password, $user["password"]) || $password === $user["password"]) {
            
            session_regenerate_id(true);
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["email"] = $user["email"];

            // 3. ADMIN CHECK: Does the email contain '@admin'?
            if (str_contains($user['email'], '@admin')) {
                $_SESSION["role"] = "admin";
            } else {
                $_SESSION["role"] = "user";
            }

            header("Location: klant.php");
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No user found with that email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h2>Login</h2>

    <?php if($error): ?>
        <p style="color:red"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="post">
        <div class="field">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>

        <div class="field">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>

        <div class="footer">
            <button type="submit" class="btn green">Login</button>
        </div>
    </form>
</div>

</body>
</html>