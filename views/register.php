<?php
session_start();
require_once __DIR__ . '/../server/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Password validation
    if (strlen($password) < 8 || !preg_match('/[0-9]/', $password)) {
        $error = "Пароль повинен мати довжину не менше 8 символів і містити хоча б одну цифру.";
    } else {
        try {
            // Check if username already exists in the database
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
            $stmt->execute([':username' => $username]);
            $existing_user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing_user) {
                $error = "Користувач з таким логіном вже існує.";
            } else {
                // Check if email already exists in the database
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
                $stmt->execute([':email' => $email]);
                $existing_user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($existing_user) {
                    $error = "Користувач з таким email вже існує.";
                } else {
                    // Insert user into database
                    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
                    $stmt->execute([':username' => $username, ':email' => $email, ':password' => $password]);

                    // Get the ID of the newly created user
                    $user_id = $pdo->lastInsertId();

                    // Store user info in session
                    $_SESSION['user'] = ['id' => $user_id, 'username' => $username, 'email' => $email];

                    header('Location: main.php');
                    exit;
                }
            }
        } catch (PDOException $e) {
            echo "Database Error: " . $e->getMessage();
            exit();
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include __DIR__ . '/template/header.html'; ?>
</head>

<body class="loginBody">
    <div id="auth-container">
        <div id="register-form">
            <h2>Реєстрація</h2>
            <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
            <form action="register.php" method="POST">
                <label for="username">Логін:</label>
                <input type="text" id="username" name="username" required><br>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required><br>

                <label for="password">Пароль:</label>
                <input type="password" id="password" name="password" required><br>

                <button class="btn btn-primary" type="submit">Зареєструватися</button><br>
                <p><a href="login.php" style="color:black; text-decoration: none">Увійти</a></p>
            </form>
        </div>
    </div>
</body>

</html>