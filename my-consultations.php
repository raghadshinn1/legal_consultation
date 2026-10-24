<?php
declare(strict_types=1);
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header('location: login.php');
    exit;
}

require_once 'db.php';
require_once 'Consultation.php';

$database = new Database();
$db = $database->connect();

$consultationObj = new Consultation($db);
$myConsultations = $consultationObj->getByClientId((int)$_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Consultations - Legal Portal</title>
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

        .container {
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
        }

        th {
            background-color: #f8fafc;
            color: #334155;
            font-weight: 600;
        }

        td {
            color: #475569;
        }

        .badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge.pending { background-color: #fef3c7; color: #d97706; }
        .badge.accepted { background-color: #ecfdf5; color: #059669; }
        .badge.rejected { background-color: #fee2e2; color: #dc2626; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div>
            <div class="sidebar-brand">
                <h3>Legal Portal</h3>
                <span>Client Panel</span>
            </div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="book-consultation.php">Book Consultation</a></li>
                <li><a href="my-consultations.php" class="active">My Appointments</a></li>
                <li><a href="profile-settings.php">Profile Settings</a></li>
            </ul>
        </div>
        <div class="sidebar-footer">
            <a href="logout.php" class="btn-logout">Log Out</a>
        </div>
    </div>

    <div class="main-content">
        <div class="container">
            <h2>My Consultations</h2>
            <p class="subtitle">Track the status of your submitted legal consultation requests.</p>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Subject</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Date Submitted</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($myConsultations)): ?>
                        <?php foreach ($myConsultations as $item): ?>
                            <tr>
                                <td><?php echo $item['id']; ?></td>
                                <td><?php echo htmlspecialchars($item['subject']); ?></td>
                                <td><?php echo htmlspecialchars($item['description']); ?></td>
                                <td>
                                    <?php 
                                        $status = strtolower($item['status'] ?? 'pending');
                                        $statusClass = 'pending';
                                        if ($status === 'accepted') $statusClass = 'accepted';
                                        if ($status === 'rejected') $statusClass = 'rejected';
                                    ?>
                                    <span class="badge <?php echo $statusClass; ?>">
                                        <?php echo htmlspecialchars($item['status'] ?? 'Pending'); ?>
                                    </span>
                                </td>
                                <td><?php echo $item['created_at']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: #94a3b8;">No consultation requests found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>