<?php
declare(strict_types=1);
session_start();
if (!isset($_SESSION['user_id'])){
    header('location:login.php');
    exit();
}
$role = $_SESSION['role'];
$name = $_SESSION['name'];
$email = $_SESSION['email'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Legal Consultation System</title>
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

        .sidebar-menu a:hover, .sidebar-menu a.active {
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

        .header-welcome {
            background: #ffffff;
            padding: 25px 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-welcome h1 {
            font-size: 24px;
            color: #1e293b;
            margin-bottom: 5px;
        }

        .header-welcome p {
            color: #64748b;
            font-size: 14px;
        }

        .badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge.client {
            background-color: #eef2ff;
            color: #2563eb;
        }

        .badge.lawyer {
            background-color: #fef3c7;
            color: #d97706;
        }

        .grid-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .card-box {
            background: #ffffff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
            border-left: 4px solid #2563eb;
        }

        .card-box.lawyer-border {
            border-left-color: #d97706;
        }

        .card-box h3 {
            font-size: 16px;
            color: #1e293b;
            margin-bottom: 10px;
        }

        .card-box p {
            font-size: 14px;
            color: #64748b;
            line-height: 1.5;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div>
            <div class="sidebar-brand">
                <h3>Legal Portal</h3>
                <span><?php echo htmlspecialchars($role); ?> Panel</span>
            </div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php" class="active">Dashboard</a></li>
                <?php if ($role === 'client'): ?>
                    <li><a href="#">Book Consultation</a></li>
                    <li><a href="#">My Appointments</a></li>
                <?php else: ?>
                    <li><a href="#">Client Requests</a></li>
                    <li><a href="#">Manage Schedule</a></li>
                <?php endif; ?>
                <li><a href="#">Profile Settings</a></li>
            </ul>
        </div>
        <div class="sidebar-footer">
            <a href="logout.php" class="btn-logout">Log Out</a>
        </div>
    </div>

    <div class="main-content">
        <div class="header-welcome">
            <div>
                <h1>Welcome back, <?php echo htmlspecialchars($name); ?>!</h1>
                <p>Here is what's happening with your account today.</p>
            </div>
            <span class="badge <?php echo htmlspecialchars($role); ?>">
                <?php echo ucfirst(htmlspecialchars($role)); ?>
            </span>
        </div>

        <?php if ($role === 'client'): ?>
            <div class="grid-cards">
                <div class="card-box">
                    <h3>Book a Consultation</h3>
                    <p>Connect with expert lawyers and schedule your legal advice session easily.</p>
                </div>
                <div class="card-box">
                    <h3>My Appointments</h3>
                    <p>View your upcoming and past legal consultation appointments.</p>
                </div>
            </div>
        <?php else: ?>

            <div class="grid-cards">
                <div class="card-box lawyer-border">
                    <h3>Incoming Requests</h3>
                    <p>Review new consultation requests submitted by clients.</p>
                </div>
                <div class="card-box lawyer-border">
                    <h3>My Schedule</h3>
                    <p>Manage your available hours and booked consultation times.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>