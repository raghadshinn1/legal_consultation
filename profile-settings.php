<?php
declare(strict_types=1);
session_start();

if (!isset($_SESSION['user_id'])) {
    header('location: login.php');
    exit();
}

require_once 'db.php';

$database = new Database();
$db = $database->connect();

$userId = (int) $_SESSION['user_id'];
$role = $_SESSION['role'] ?? 'client';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if (!empty($name) && !empty($email)) {
        $sql = 'UPDATE users SET name = :name, email = :email WHERE id = :id';
        $stmt = $db->prepare($sql);
        if ($stmt->execute([':name' => $name, ':email' => $email, ':id' => $userId])) {
            $success = "Profile updated successfully!";
        } else {
            $error = "Failed to update profile.";
        }
    } else {
        $error = "All fields are required.";
    }
}

$stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute([':id' => $userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings - Legal Portal</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f4f7f6;
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 260px;
            background-color: #1e293b;
            color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 25px 20px;
        }

        .sidebar-brand h3 {
            font-size: 18px;
            color: #f8fafc;
            margin-bottom: 5px;
        }

        .sidebar-brand span {
            font-size: 12px;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .sidebar-menu {
            list-style: none;
            margin-top: 30px;
            flex-grow: 1;
        }

        .sidebar-menu li {
            margin-bottom: 10px;
        }

        .sidebar-menu a {
            display: block;
            padding: 12px 15px;
            color: #cbd5e1;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background-color: #334155;
            color: #ffffff;
        }

        .sidebar-footer .btn-logout {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #ef4444;
            color: #ffffff;
            text-align: center;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: background-color 0.2s ease;
        }

        .sidebar-footer .btn-logout:hover {
            background-color: #dc2626;
        }

        .main-content {
            flex-grow: 1;
            padding: 40px;
            overflow-y: auto;
        }

        .container {
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
            max-width: 600px;
        }

        h2 {
            font-size: 22px;
            color: #1e293b;
            margin-bottom: 5px;
        }

        p.subtitle {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 25px;
        }

        .alert-message {
            background-color: #ffeeef;
            color: #ef4444;
            padding: 10px 14px;
            border-radius: 6px;
            font-size: 14px;
            margin-bottom: 20px;
            border: 1px solid #fecaca;
        }

        .success-message {
            background-color: #ecfdf5;
            color: #059669;
            padding: 10px 14px;
            border-radius: 6px;
            font-size: 14px;
            margin-bottom: 20px;
            border: 1px solid #a7f3d0;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 8px;
        }

        input[type="text"],
        input[type="email"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 14px;
            color: #1e293b;
            outline: none;
            transition: border-color 0.2s;
        }

        input[type="text"]:focus,
        input[type="email"]:focus {
            border-color: #3b82f6;
        }

        .btn-submit {
            background-color: #2563eb;
            color: #ffffff;
            border: none;
            padding: 12px 20px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .btn-submit:hover {
            background-color: #1d4ed8;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <div>
            <div class="sidebar-brand">
                <h3>Legal Portal</h3>
                <span><?php echo ($role === 'lawyer') ? 'Lawyer Panel' : 'Client Panel'; ?></span>
            </div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php">Dashboard</a></li>
                <?php if ($role === 'client'): ?>
                    <li><a href="book-consultation.php">Book Consultation</a></li>
                    <li><a href="my-consultations.php">My Appointments</a></li>
                <?php else: ?>
                    <li><a href="client-requests.php">Client Requests</a></li>
                    <li><a href="manage-schedule.php">Manage Schedule</a></li>
                <?php endif; ?>
                <li><a href="profile-settings.php" class="active">Profile Settings</a></li>
            </ul>
        </div>
        <div class="sidebar-footer">
            <a href="logout.php" class="btn-logout">Log Out</a>
        </div>
    </div>

    <div class="main-content">
        <div class="container">
            <h2>Profile Settings</h2>
            <p class="subtitle">Update your personal account details.</p>

            <?php if (!empty($error)): ?>
                <div class="alert-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <form action="profile-settings.php" method="POST">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name"
                        value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email"
                        value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                </div>
                <button type="submit" class="btn-submit">Save Changes</button>
            </form>
        </div>
    </div>

</body>

</html>