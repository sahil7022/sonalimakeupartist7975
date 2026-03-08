<?php
session_start();

// Require user to be logged in before viewing homepage
if (empty($_SESSION['user_id'])) {
    header('Location: user_login.php');
    exit;
}

require_once __DIR__ . '/db.php';

// Fetch active services
$services_stmt = $mysqli->prepare('SELECT id, title, description, image_url FROM services WHERE is_active = 1 ORDER BY sort_order, id');
$services_stmt->execute();
$services_result = $services_stmt->get_result();
$services = $services_result->fetch_all(MYSQLI_ASSOC);

// Fetch contact settings (single row)
$contact_stmt = $mysqli->prepare('SELECT * FROM contact_settings ORDER BY id LIMIT 1');
$contact_stmt->execute();
$contact = $contact_stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sonali Makeup Artist</title>
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
                <a href="index.php" class="nav-active"><i class="fa-solid fa-house nav-icon"></i> Home</a>
                <a href="services.php"><i class="fa-solid fa-wand-magic-sparkles nav-icon"></i> Services</a>
                <a href="#book"><i class="fa-regular fa-calendar-check nav-icon"></i> Book</a>
                <a href="#contact"><i class="fa-regular fa-envelope nav-icon"></i> Contact</a>
                <span class="nav-user"><i class="fa-regular fa-user"></i> <?= htmlspecialchars($_SESSION['user_username'] ?? '') ?></span>
                <a href="user_logout.php" class="nav-admin"><i class="fa-solid fa-right-from-bracket"></i></a>
                <a href="login.php" class="nav-admin"><i class="fa-solid fa-gear"></i></a>
            </nav>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="container hero-grid">
                <div class="hero-text">
                    <h1>Elevate your beauty with <span>Sonali</span></h1>
                    <p>
                        Luxury bridal and occasion makeup with a soft, flawless finish.
                        From engagement to reception, we craft looks that feel like you—at your most radiant.
                    </p>
                    <div class="hero-actions">
                        <a href="#book" class="btn-primary">Book an appointment</a>
                        <a href="#services" class="btn-ghost">Explore services</a>
                    </div>
                </div>
                <div class="hero-visual">
                    <div class="hero-card hero-card-main"></div>
                    <div class="hero-card hero-card-secondary"></div>
                </div>
            </div>
        </section>

        <section id="services" class="section">
            <div class="container">
                <div class="section-header">
                    <h2>What we offer</h2>
                    <p>Curated services designed for brides, grooms, and beauty enthusiasts.</p>
                </div>

                <div class="card-grid">
                    <?php foreach ($services as $service): ?>
                        <article class="service-card">
                            <div class="service-image" style="background-image: url('<?=
                                htmlspecialchars($service['image_url'] ?: 'images/placeholder.jpg', ENT_QUOTES)
                            ?>');"></div>
                            <div class="service-body">
                                <h3><?= htmlspecialchars($service['title']) ?></h3>
                                <p><?= htmlspecialchars($service['description']) ?></p>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section id="book" class="section section-alt">
            <div class="container two-column">
                <div>
                    <h2>Book your glam session</h2>
                    <p>
                        Fill in your details and we’ll get back to you with availability and packages.
                        Limited seats available for makeup classes.
                    </p>
                </div>
                <form class="card form-card" method="post" action="save_user.php">
                    <div class="field">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" required placeholder="Your full name">
                    </div>
                    <div class="field-grid">
                        <div class="field">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" placeholder="you@example.com">
                        </div>
                        <div class="field">
                            <label for="phone">Phone</label>
                            <input type="text" id="phone" name="phone" placeholder="+91-">
                        </div>
                    </div>
                    <div class="field">
                        <label for="preferred_service">Interested in</label>
                        <select id="preferred_service" name="preferred_service">
                            <option value="">Select a service</option>
                            <option>Bridal Looks</option>
                            <option>Makeup Classes</option>
                            <option>Nail Design</option>
                            <option>Groom Makeup</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div class="field">
                        <label for="message">Tell us about your event / goal</label>
                        <textarea id="message" name="message" rows="3" placeholder="Dates, venue, preferred style, etc."></textarea>
                    </div>
                    <button type="submit" class="btn-primary full-width">Submit details</button>
                </form>
            </div>
        </section>

        <section id="contact" class="section">
            <div class="container two-column">
                <div>
                    <h2><?= htmlspecialchars($contact['headline'] ?? 'Contact Sonali') ?></h2>
                    <p><?= htmlspecialchars($contact['description'] ?? 'Admin can update this section from the dashboard.') ?></p>
                </div>
                <div class="contact-details card">
                    <?php if (!empty($contact['phone'])): ?>
                        <div><span>Phone:</span> <?= htmlspecialchars($contact['phone']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($contact['email'])): ?>
                        <div><span>Email:</span> <?= htmlspecialchars($contact['email']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($contact['address'])): ?>
                        <div><span>Studio:</span> <?= htmlspecialchars($contact['address']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($contact['instagram'])): ?>
                        <div><span>Instagram:</span> <a href="<?= htmlspecialchars($contact['instagram']) ?>" target="_blank" rel="noopener">Visit profile</a></div>
                    <?php endif; ?>
                    <small>These details can be updated anytime from the admin dashboard.</small>
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
