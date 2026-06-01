<header class="header">
    <div class="header-container">
        <div class="logo">
            <img src="/logo/logo.png" alt="Водить.РФ" >
            <span>Водить.РФ</span>
        </div>

        <nav class="nav-menu">
            <ul class="nav-list">

                <?php if (isset($_SESSION['user_id'])): ?>

                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <li>
                            <a href="admin.php" class="nav-link">
                                📋 Панель управления
                            </a>
                        </li>
                    <?php else: ?>
                        <li>
                            <a href="dashboard.php" class="nav-link">
                                👤 Личный кабинет
                            </a>
                        </li>

                        <li>
                            <a href="apply.php" class="nav-link">
                                📝 Новая заявка
                            </a>
                        </li>
                    <?php endif; ?>

                    <li>
                        <a href="logout.php" class="nav-link logout-btn">
                            🚪 Выйти
                        </a>
                    </li>

                <?php else: ?>

                    <li>
                        <a href="index.php" class="nav-link">
                            🔑 Вход
                        </a>
                    </li>

                    <li>
                        <a href="register.php" class="nav-link">
                            📝 Регистрация
                        </a>
                    </li>

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