<?php
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Получение заявок
$applications = [];
$stmt = $conn->prepare("SELECT * FROM applications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $applications[] = $row;
}
$stmt->close();

// Добавление отзыва
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_review'])) {
    $app_id = (int)$_POST['app_id'];
    $review = trim($_POST['review']);

    $check = $conn->prepare("SELECT status FROM applications WHERE id = ? AND user_id = ?");
    $check->bind_param("ii", $app_id, $user_id);
    $check->execute();
    $checkResult = $check->get_result();
    $app = $checkResult->fetch_assoc();

    if ($app && $app['status'] === 'Обучение завершено') {
        $ins = $conn->prepare("INSERT INTO reviews (user_id, application_id, review_text) VALUES (?, ?, ?)");
        $ins->bind_param("iis", $user_id, $app_id, $review);
        $ins->execute();
        $ins->close();
        header('Location: dashboard.php');
        exit;
    }
    $check->close();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Личный кабинет</title>
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
    <h2>Личный кабинет</h2>
    <div class="slider">
        <button id="prev">❮</button>
        <img id="slide-img" src="https://picsum.photos/300/150?random=1" alt="слайд">
        <button id="next">❯</button>
    </div>
    <a href="apply.php" class="btn">Новая заявка</a>

    <h3>Мои заявки</h3>
    <table border="1">
        <tr><th>Транспорт</th><th>Дата</th><th>Оплата</th><th>Статус</th><th>Отзыв</th></tr>
        <?php foreach ($applications as $app): ?>
        <tr>
            <td><?= htmlspecialchars($app['transport_type']) ?></td>
            <td><?= htmlspecialchars($app['start_date']) ?></td>
            <td><?= htmlspecialchars($app['payment_method']) ?></td>
            <td><?= htmlspecialchars($app['status']) ?></td>
            <td>
                <?php
                $revStmt = $conn->prepare("SELECT review_text FROM reviews WHERE application_id = ?");
                $revStmt->bind_param("i", $app['id']);
                $revStmt->execute();
                $revResult = $revStmt->get_result();
                $review = $revResult->fetch_assoc();
                if ($review):
                    echo htmlspecialchars($review['review_text']);
                elseif ($app['status'] === 'Обучение завершено'):
                ?>
                <form method="POST">
                    <input type="hidden" name="app_id" value="<?= $app['id'] ?>">
                    <textarea name="review" rows="2" cols="30" placeholder="Ваш отзыв"></textarea>
                    <button type="submit" name="add_review">Оставить отзыв</button>
                </form>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <a href="logout.php">Выйти</a>
</div>
<script>
const images = [
    'https://picsum.photos/id/11/300/150',
    'https://picsum.photos/id/12/300/150',
    'https://picsum.photos/id/13/300/150',
    'https://picsum.photos/id/14/300/150'
];
let idx = 0;
const img = document.getElementById('slide-img');
function update() { img.src = images[idx]; }
document.getElementById('prev').onclick = () => { idx = (idx - 1 + images.length) % images.length; update(); };
document.getElementById('next').onclick = () => { idx = (idx + 1) % images.length; update(); };
setInterval(() => { idx = (idx + 1) % images.length; update(); }, 3000);
</script>
</body>
</html>