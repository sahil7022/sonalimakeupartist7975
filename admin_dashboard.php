<?php
session_start();
if (empty($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/db.php';

// Fetch services
$services_stmt = $mysqli->prepare('SELECT id, title, description, image_url, is_active, sort_order FROM services ORDER BY sort_order, id');
$services_stmt->execute();
$services = $services_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch contact settings
$contact_stmt = $mysqli->prepare('SELECT * FROM contact_settings ORDER BY id LIMIT 1');
$contact_stmt->execute();
$contact = $contact_stmt->get_result()->fetch_assoc();

// Fetch recent enquiries
$users_stmt = $mysqli->prepare('SELECT id, name, email, phone, preferred_service, message, created_at FROM users ORDER BY created_at DESC LIMIT 20');
$users_stmt->execute();
$users = $users_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch site users and their latest login status
$site_users_stmt = $mysqli->prepare('
    SELECT su.id,
           su.username,
           su.created_at,
           ul.login_time,
           ul.logout_time
    FROM site_users su
    LEFT JOIN user_logins ul
        ON ul.id = (
            SELECT id FROM user_logins
            WHERE user_id = su.id
            ORDER BY login_time DESC
            LIMIT 1
        )
    ORDER BY su.created_at DESC
');
$site_users_stmt->execute();
$site_users = $site_users_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard · Sonali Makeup Artist</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="dashboard-body">
    <header class="dashboard-header">
        <div class="container dash-header-inner">
            <div class="brand">
                <span class="brand-mark">S</span>
                <div class="brand-text">
                    <div class="brand-name">Sonali Makeup Artist</div>
                    <div class="brand-tagline">Admin Dashboard</div>
                </div>
            </div>
            <div class="dash-right">
                <span class="dash-user">Hello, <?= htmlspecialchars($_SESSION['admin_username']) ?></span>
                <a href="index.php" class="nav-link">View site</a>
                <a href="logout.php" class="nav-link">Logout</a>
            </div>
        </div>
    </header>

    <main class="dashboard-main container">
        <section class="dash-section">
            <h2>Homepage services (cards)</h2>
            <p>Edit, add, or remove cards shown on the main page. All changes update the database immediately.</p>
            <form action="update_services.php" method="post" class="dash-card" enctype="multipart/form-data">
                <?php foreach ($services as $service): ?>
                    <div class="dash-service-row">
                        <input type="hidden" name="services[<?= (int)$service['id'] ?>][id]" value="<?= (int)$service['id'] ?>">
                        <div class="field-grid">
                            <div class="field">
                                <label>Title</label>
                                <input type="text" name="services[<?= (int)$service['id'] ?>][title]" value="<?= htmlspecialchars($service['title']) ?>">
                            </div>
                            <div class="field">
                                <label>Sort order</label>
                                <input type="number" name="services[<?= (int)$service['id'] ?>][sort_order]" value="<?= (int)$service['sort_order'] ?>">
                            </div>
                        </div>
                        <div class="field">
                            <label>Description</label>
                            <textarea name="services[<?= (int)$service['id'] ?>][description]" rows="2"><?= htmlspecialchars($service['description']) ?></textarea>
                        </div>
                        <div class="field-grid">
                            <div class="field">
                                <label>Current image</label>
                                <?php if (!empty($service['image_url'])): ?>
                                    <div class="service-thumb">
                                        <img src="<?= htmlspecialchars($service['image_url']) ?>" alt="" />
                                    </div>
                                <?php else: ?>
                                    <small>No image set</small>
                                <?php endif; ?>
                            </div>
                            <div class="field">
                                <label>Image URL (optional)</label>
                                <input type="text" name="services[<?= (int)$service['id'] ?>][image_url]" value="<?= htmlspecialchars($service['image_url']) ?>" placeholder="Or paste an image URL">
                            </div>
                        </div>
                        <div class="field-grid">
                            <div class="field">
                                <label>Upload new image</label>
                                <input type="file" name="image_file_<?= (int)$service['id'] ?>" accept="image/*">
                                <small>Choose a file to replace the image above.</small>
                            </div>
                            <div class="field field-checkbox">
                                <label>
                                    <input type="checkbox" name="services[<?= (int)$service['id'] ?>][is_active]" <?= $service['is_active'] ? 'checked' : '' ?>>
                                    Active
                                </label>
                            </div>
                            <div class="field field-delete">
                                <button type="submit"
                                        formaction="delete_service.php"
                                        formmethod="post"
                                        name="id"
                                        value="<?= (int)$service['id'] ?>"
                                        class="btn-danger">
                                    Delete
                                </button>
                            </div>
                        </div>
                        <hr>
                    </div>
                <?php endforeach; ?>
                <button type="submit" class="btn-primary">Save services</button>
            </form>

            <div class="dash-card" style="margin-top: 12px;">
                <h3>Add new service card</h3>
                <form action="add_service.php" method="post" enctype="multipart/form-data">
                    <div class="field-grid">
                        <div class="field">
                            <label for="new_title">Title</label>
                            <input type="text" id="new_title" name="title" placeholder="e.g. Party Makeup" required>
                        </div>
                        <div class="field">
                            <label for="new_sort_order">Sort order</label>
                            <input type="number" id="new_sort_order" name="sort_order" value="0">
                        </div>
                    </div>
                    <div class="field">
                        <label for="new_description">Description</label>
                        <textarea id="new_description" name="description" rows="2" placeholder="Short description of the service"></textarea>
                    </div>
                    <div class="field">
                        <label for="new_image_url">Image URL (relative or full)</label>
                        <input type="text" id="new_image_url" name="image_url" placeholder="e.g. images/party.jpg">
                    </div>
                    <div class="field">
                        <label for="new_image_file">Or upload image</label>
                        <input type="file" id="new_image_file" name="image_file" accept="image/*">
                    </div>
                    <button type="submit" class="btn-primary">Add service</button>
                </form>
            </div>
        </section>

        <section class="dash-section">
            <h2>Contact section content</h2>
            <p>Update the information shown in the “Contact Us” section on the homepage.</p>
            <form action="update_contact.php" method="post" class="dash-card">
                <input type="hidden" name="id" value="<?= isset($contact['id']) ? (int)$contact['id'] : 0 ?>">
                <div class="field">
                    <label>Headline</label>
                    <input type="text" name="headline" value="<?= htmlspecialchars($contact['headline'] ?? '') ?>">
                </div>
                <div class="field">
                    <label>Description</label>
                    <textarea name="description" rows="3"><?= htmlspecialchars($contact['description'] ?? '') ?></textarea>
                </div>
                <div class="field-grid">
                    <div class="field">
                        <label>Phone</label>
                        <input type="text" name="phone" value="<?= htmlspecialchars($contact['phone'] ?? '') ?>">
                    </div>
                    <div class="field">
                        <label>Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($contact['email'] ?? '') ?>">
                    </div>
                </div>
                <div class="field">
                    <label>Studio address</label>
                    <input type="text" name="address" value="<?= htmlspecialchars($contact['address'] ?? '') ?>">
                </div>
                <div class="field">
                    <label>Instagram URL</label>
                    <input type="text" name="instagram" value="<?= htmlspecialchars($contact['instagram'] ?? '') ?>">
                </div>
                <button type="submit" class="btn-primary">Save contact section</button>
            </form>
        </section>

        <section class="dash-section">
            <h2>Recent enquiries / bookings</h2>
            <div class="dash-card table-wrap">
                <?php if (empty($users)): ?>
                    <p>No enquiries yet.</p>
                <?php else: ?>
                    <table class="dash-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Name</th>
                                <th>Contact</th>
                                <th>Service</th>
                                <th>Message</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= htmlspecialchars($user['created_at']) ?></td>
                                    <td><?= htmlspecialchars($user['name']) ?></td>
                                    <td>
                                        <?php if (!empty($user['phone'])): ?>
                                            <?= htmlspecialchars($user['phone']) ?><br>
                                        <?php endif; ?>
                                        <?php if (!empty($user['email'])): ?>
                                            <small><?= htmlspecialchars($user['email']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($user['preferred_service']) ?></td>
                                    <td><?= nl2br(htmlspecialchars($user['message'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </section>

        <section class="dash-section">
            <h2>Site users & login status</h2>
            <p>List of user accounts that can log in to view the site, with their latest login.</p>
            <div class="dash-card table-wrap">
                <?php if (empty($site_users)): ?>
                    <p>No site users yet.</p>
                <?php else: ?>
                    <table class="dash-table">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Created at</th>
                                <th>Last login</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($site_users as $su): ?>
                                <tr>
                                    <td><?= htmlspecialchars($su['username']) ?></td>
                                    <td><?= htmlspecialchars($su['created_at']) ?></td>
                                    <td><?= $su['login_time'] ? htmlspecialchars($su['login_time']) : '-' ?></td>
                                    <td>
                                        <?php if ($su['login_time'] && empty($su['logout_time'])): ?>
                                            <strong>Online</strong>
                                        <?php elseif ($su['login_time']): ?>
                                            Last logout: <?= htmlspecialchars($su['logout_time']) ?>
                                        <?php else: ?>
                                            Never logged in
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </section>
    </main>
</body>
</html>

