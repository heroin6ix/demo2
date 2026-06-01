<?php
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// Смена статуса
if (isset($_POST['change_status'])) {
    $app_id = (int)$_POST['app_id'];
    $new_status = $_POST['new_status'];
    $stmt = $conn->prepare("UPDATE applications SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $app_id);
    $stmt->execute();
    $stmt->close();
}

$filter = isset($_GET['status']) ? $_GET['status'] : '';
$sql = "SELECT a.*, u.fullname FROM applications a JOIN users u ON a.user_id = u.id";
if (!empty($filter)) {
    $sql .= " WHERE a.status = '$filter'";
}
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Админ-панель</title>
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
    <h2>Панель администратора</h2>
    <div class="filters">
        <a href="?">Все</a>
        <a href="?status=Новая">Новая</a>
        <a href="?status=Идет обучение">Идет обучение</a>
        <a href="?status=Обучение завершено">Завершено</a>
    </div>
    <table border="1">
        <tr><th>ФИО</th><th>Транспорт</th><th>Дата</th><th>Оплата</th><th>Статус</th><th>Действие</th></tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['fullname']) ?></td>
            <td><?= htmlspecialchars($row['transport_type']) ?></td>
            <td><?= htmlspecialchars($row['start_date']) ?></td>
            <td><?= htmlspecialchars($row['payment_method']) ?></td>
            <td><?= htmlspecialchars($row['status']) ?></td>
            <td>
                <form method="POST">
                    <input type="hidden" name="app_id" value="<?= $row['id'] ?>">
                    <select name="new_status">
                        <option value="Новая">Новая</option>
                        <option value="Идет обучение">Идет обучение</option>
                        <option value="Обучение завершено">Обучение завершено</option>
                    </select>
                    <button type="submit" name="change_status">Изменить</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    <a href="logout.php">Выход</a>
</div>
</body>
</html>