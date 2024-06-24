<?php
    session_start();
    require_once __DIR__ . '/../server/db_connection.php';

    $error = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email AND password = :password");
        $stmt->execute([':email' => $email, ':password' => $password]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $_SESSION['user'] = ['id' => $user['id'], 'username' => $user['username'], 'email' => $user['email']];

            header('Location: main.php');
            exit;
        } else {
            $error = "Invalid email or password.";
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
        <div id="login-form">
            <h2>Вхід</h2>
            <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
            <form action="login.php" method="POST">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required><br>

                <label for="password">Пароль:</label>
                <input type="password" id="password" name="password" required><br>

                <button class="btn btn-primary" type="submit">Увійти</button><br>
                <p><a href="register.php" style='color:black; text-decoration: none'>Зареєструватися</a></p>
            </form>
        </div>
    </div>
</body>

</html>