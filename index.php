<?php
require_once 'config.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'];
    $password = $_POST['password'];
    
    // Экранируем для безопасности
    $login = $conn->real_escape_string($login);
    
    $sql = "SELECT id, login, password, role FROM users WHERE login = '$login'";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Проверяем пароль
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['login'] = $user['login'];
            
            if ($user['role'] === 'admin') {
                header('Location: admin.php');
            } else {
                header('Location: dashboard.php');
            }
            exit;
        } else {
            $error = 'Неверный логин или пароль';
        }
    } else {
        $error = 'Неверный логин или пароль';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход - Водить.РФ</title>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>

<div class="container auth">
    <div style="text-align: center; margin-bottom: 30px;">
        <h1>Добро пожаловать</h1>
        <p style="font-size: 18px; color: #6C757D;">Вход в систему обучения вождению речного транспорта</p>
    </div>
    
    <?php if ($error): ?>
        <div class="error-msg">❌ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <input type="text" name="login" placeholder="Логин" required autofocus>
        <input type="password" name="password" placeholder="Пароль" required>
        <button type="submit">Войти →</button>
    </form>
    
    <p style="text-align: center; margin-top: 25px;">
        <a href="register.php" style="font-size: 15px;">📝 Еще не зарегистрированы? Регистрация</a>
    </p>
    
    <!-- Подсказка для администратора -->
    <div style="margin-top: 30px; padding: 15px; background: #e8f4fd; border-radius: 16px; text-align: center;">
        <p style="margin: 0; font-size: 13px; color: #007BFF;">
            🔐 <strong>Тестовые данные:</strong><br>
            Админ: <strong>Admin26</strong> / <strong>Demo20</strong><br>
            Пользователь: test1111 / test1111
        </p>
    </div>
</div>

<div class="contacts">
    <p>📍 г. Москва, ул. Большая Ордынка, д. 15 | 📞 +7 (495) 123-45-67</p>
    <p>💳 Оплата: предоплата по QR-коду | карта МИР | постоплата в офисе</p>
</div>

<script>
    const burger = document.querySelector('.burger');
    const navMenu = document.querySelector('.nav-menu');
    if (burger && navMenu) {
        burger.addEventListener('click', () => {
            burger.classList.toggle('active');
            navMenu.classList.toggle('active');
        });
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                burger.classList.remove('active');
                navMenu.classList.remove('active');
            });
        });
    }
</script>
<script src="header.js"></script>
</body>
</html>