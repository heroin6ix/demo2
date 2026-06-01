<?php
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $transport = $_POST['transport'];
    $start_date = $_POST['start_date'];
    $payment = $_POST['payment'];

    $stmt = $conn->prepare("INSERT INTO applications (user_id, transport_type, start_date, payment_method) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $transport, $start_date, $payment);
    $stmt->execute();
    $stmt->close();

    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Новая заявка</title>
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
    <div class="logo">
    <img src="logo.svg" alt="Водить.РФ">
    <span>Водить.РФ</span>
    <header class="header">
    <div class="header-container">
        <div class="logo">
            <img src="logo.svg" alt="Водить.РФ">
            <span>Водить.РФ</span>
        </div>
        
        <nav class="nav-menu">
            <ul class="nav-list">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <li><a href="admin.php" class="nav-link">📋 Панель управления</a></li>
                    <?php else: ?>
                        <li><a href="dashboard.php" class="nav-link">👤 Личный кабинет</a></li>
                        <li><a href="apply.php" class="nav-link">📝 Новая заявка</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php" class="nav-link logout-btn">🚪 Выйти</a></li>
                <?php else: ?>
                    <li><a href="index.php" class="nav-link">🔑 Вход</a></li>
                    <li><a href="register.php" class="nav-link">📝 Регистрация</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        
        <div class="burger">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
</header>
</div>
<div class="container">
    <h2>Заявка на обучение</h2>
    <form method="POST">
        <select name="transport" required>
            <option value="катер">Катер</option>
            <option value="круизный лайнер">Круизный лайнер</option>
            <option value="яхта">Яхта</option>
        </select>
        <input type="date" name="start_date" required>
        <select name="payment" required>
            <option value="предоплата по QR-коду">Предоплата по QR-коду</option>
            <option value="оплата картой МИР">Оплата картой МИР</option>
            <option value="постоплата в офисе">Постоплата в офисе</option>
        </select>
        <button type="submit">Отправить заявку</button>
    </form>
    <a href="dashboard.php">Назад в личный кабинет</a>
</div>
</body>
</html>