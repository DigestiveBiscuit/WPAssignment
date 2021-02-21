<header>
    <a class="logo" href="<?php echo BASE_URL . '/index.php'; ?>">
        <h1 class="logo-text"><span>WP</span>Assignment</h1>
    </a>
    <ul class="nav">
        <?php if (isset($_SESSION['username'])): ?>
            <li>
                <a href="#">
                <img src=<?php echo BASE_URL . '/assets/images/user_icon.png'?> width='15' height='15'></img>
                    <?php echo $_SESSION['username']; ?>
                    <img src=<?php echo BASE_URL . '/assets/images/down_chevron.png'?> width='12' height='12' ></img>
                </a>
                <ul>
                    <li><a href="<?php echo BASE_URL . '/logout.php'; ?>" class="logout">Logout</a></li>
                </ul>
            </li>
        <?php endif; ?>
    </ul>
</header>