<?php
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);
    $password = $_POST['password'];
    $fullname = trim($_POST['fullname']);
    $birthdate = $_POST['birthdate'];
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);

    if (!preg_match('/^[a-zA-Z0-9]{6,}$/', $login)) {
        $error = 'Логин должен содержать латинские буквы и цифры, минимум 6 символов';
    } elseif (strlen($password) < 8) {
        $error = 'Пароль должен быть не менее 8 символов';
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE login = ?");
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = 'Логин уже занят';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (login, password, fullname, birthdate, phone, email) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $login, $hashed, $fullname, $birthdate, $phone, $email);

            if ($stmt->execute()) {
                header('Location: index.php');
                exit;
            } else {
                $error = 'Ошибка регистрации';
            }
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация - Водить.РФ</title>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<script>
    // Бургер-меню
    const burger = document.querySelector('.burger');
    const navMenu = document.querySelector('.nav-menu');
    
    if (burger && navMenu) {
        burger.addEventListener('click', () => {
            burger.classList.toggle('active');
            navMenu.classList.toggle('active');
        });
        
        // Закрыть меню при клике на ссылку
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                burger.classList.remove('active');
                navMenu.classList.remove('active');
            });
        });
    }
    
    // Анимация шапки при скролле
    let lastScroll = 0;
    const header = document.querySelector('.header');
    
    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;
        if (currentScroll > 100) {
            header.style.background = 'rgba(255, 255, 255, 0.98)';
            header.style.backdropFilter = 'blur(30px)';
            header.style.boxShadow = '0 8px 32px rgba(0, 123, 255, 0.15)';
        } else {
            header.style.background = 'rgba(255, 255, 255, 0.95)';
            header.style.backdropFilter = 'blur(20px)';
            header.style.boxShadow = '0 4px 20px rgba(0, 123, 255, 0.1)';
        }
        lastScroll = currentScroll;
    });
</script>
<body>
    <?php include 'header.php'; ?>
<div class="container">
    <div style="text-align: center; margin-bottom: 30px;">
        <h1>Регистрация</h1>
    </div>
    <?php if ($error): ?>
        <div class="error-msg"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST">
        <input type="text" name="login" placeholder="Логин (лат+цифры, мин 6)" required>
        <input type="password" name="password" placeholder="Пароль (мин 8 символов)" required>
        <input type="text" name="fullname" placeholder="ФИО" required>
        <input type="date" name="birthdate" required>
        <input type="text" name="phone" placeholder="Телефон" required>
        <input type="email" name="email" placeholder="Email" required>
        <button type="submit">Зарегистрироваться</button>
    </form>
    <a href="index.php">Уже есть аккаунт? Войти</a>
</div>
</body>
</html>