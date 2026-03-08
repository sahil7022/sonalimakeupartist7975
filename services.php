<?php
session_start();

if (empty($_SESSION['user_id'])) {
    header('Location: user_login.php');
    exit;
}

require_once __DIR__ . '/db.php';

$query = isset($_GET['q']) ? trim($_GET['q']) : '';

if ($query !== '') {
    $like = '%' . $query . '%';
    $stmt = $mysqli->prepare('SELECT id, title, description, image_url FROM services WHERE is_active = 1 AND (title LIKE ? OR description LIKE ?) ORDER BY sort_order, id');
    $stmt->bind_param('ss', $like, $like);
} else {
    $stmt = $mysqli->prepare('SELECT id, title, description, image_url FROM services WHERE is_active = 1 ORDER BY sort_order, id');
}

$stmt->execute();
$result = $stmt->get_result();
$services = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services · Sonali Makeup Artist</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="site-body">
    <header class="site-header">
        <div class="container">
            <div class="brand">
                <span class="brand-mark">S</span>
                <div class="brand-text">
                    <div class="brand-name">Sonali Makeup Artist</div>
                    <div class="brand-tagline">Bridal · Glam · Classes</div>
                </div>
            </div>
            <nav class="nav">
                <a href="index.php"><i class="fa-solid fa-house nav-icon"></i> Home</a>
                <a href="services.php" class="nav-active"><i class="fa-solid fa-wand-magic-sparkles nav-icon"></i> Services</a>
                <a href="index.php#book"><i class="fa-regular fa-calendar-check nav-icon"></i> Book</a>
                <a href="index.php#contact"><i class="fa-regular fa-envelope nav-icon"></i> Contact</a>
                <span class="nav-user"><i class="fa-regular fa-user"></i> <?= htmlspecialchars($_SESSION['user_username'] ?? '') ?></span>
                <a href="user_logout.php" class="nav-admin"><i class="fa-solid fa-right-from-bracket"></i></a>
            </nav>
        </div>
    </header>

    <main>
        <section class="section">
            <div class="container">
                <div class="section-header">
                    <h2>All services</h2>
                    <p>Search and explore every glam service Sonali offers.</p>
                </div>

                <form class="service-search" method="get" action="services.php">
                    <div class="search-input-wrap">
                        <i class="fa-solid fa-magnifying-glass search-icon"></i>
                        <input
                            type="text"
                            name="q"
                            value="<?= htmlspecialchars($query) ?>"
                            placeholder="Search by service name or look (e.g. bridal, HD, party)"
                        />
                        <?php if ($query !== ''): ?>
                            <a href="services.php" class="search-clear"><i class="fa-solid fa-xmark"></i></a>
                        <?php endif; ?>
                    </div>
                    <button type="submit" class="btn-ghost search-btn"><i class="fa-solid fa-search"></i> Search</button>
                </form>

                <div class="card-grid">
                    <?php if (empty($services)): ?>
                        <p>No services match your search.</p>
                    <?php else: ?>
                        <?php foreach ($services as $service): ?>
                            <article class="service-card service-card-detailed">
                                <div class="service-image" style="background-image: url('<?=
                                    htmlspecialchars($service['image_url'] ?: 'images/placeholder.jpg', ENT_QUOTES)
                                ?>');"></div>
                                <div class="service-body">
                                    <h3><i class="fa-solid fa-star-half-stroke"></i> <?= htmlspecialchars($service['title']) ?></h3>
                                    <p><?= htmlspecialchars($service['description']) ?></p>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <div class="container">
            <span>© <?= date('Y') ?> Sonali Makeup Artist. All rights reserved.</span>
        </div>
    </footer>
</body>
</html>

