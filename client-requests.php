<?php
declare(strict_types=1);
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lawyer') {
    header('location:login.php');
    exit();
}

require_once 'db.php';
require_once 'Consultation.php';

$database = new Database();
$db = $database->connect();

$consultationObj = new Consultation($db);

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['consultation_id'], $_POST['status'])) {
    $consultationId = (int) $_POST['consultation_id'];
    $status = trim($_POST['status']);

    if (in_array($status, ['Accepted', 'Rejected'], true)) {
        if ($consultationObj->updateStatus($consultationId, $status)) {
            $success = "Consultation status updated successfully!";
        } else {
            $message = "Failed to update status.";
        }
    }

}
$lawyerId = (int) $_SESSION['user_id'];
$requests = $consultationObj->getByLawyerId($lawyerId);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Requests - Lawyer Panel</title>
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
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

        .badge.pending {
            background-color: #fef3c7;
            color: #d97706;
        }

        .badge.accepted {
            background-color: #ecfdf5;
            color: #059669;
        }

        .badge.rejected {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .btn-action {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            color: #fff;
            margin-right: 5px;
        }

        .btn-accept {
            background-color: #059669;
        }

        .btn-accept:hover {
            background-color: #047857;
        }

        .btn-reject {
            background-color: #ef4444;
        }

        .btn-reject:hover {
            background-color: #dc2626;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <div>
            <div class="sidebar-brand">
                <h3>Legal Portal</h3>
                <span>Lawyer Panel</span>
            </div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="client-requests.php" class="active">Client Requests</a></li>
                <li><a href="manage-schedule.php">Manage Schedule</a></li>
                <li><a href="profile-settings.php">Profile Settings</a></li>
            </ul>
        </div>
        <div class="sidebar-footer">
            <a href="logout.php" class="btn-logout">Log Out</a>
        </div>
    </div>

    <div class="main-content">
        <div class="container">
            <h2>Client Requests</h2>
            <p class="subtitle">Review and manage consultation requests submitted by clients.</p>

            <?php if (!empty($message)): ?>
                <div class="alert-message"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Client Name</th>
                        <th>Subject</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($requests)): ?>
                        <?php foreach ($requests as $req): ?>
                            <tr>
                                <td><?php echo $req['id']; ?></td>
                                <td><?php echo htmlspecialchars($req['client_name'] ?? 'Unknown'); ?></td>
                                <td><?php echo htmlspecialchars($req['subject']); ?></td>
                                <td><?php echo htmlspecialchars($req['description']); ?></td>
                                <td>
                                    <?php
                                    $statusClass = 'pending';
                                    if (isset($req['status']) && strtolower($req['status']) === 'accepted')
                                        $statusClass = 'accepted';
                                    if (isset($req['status']) && strtolower($req['status']) === 'rejected')
                                        $statusClass = 'rejected';
                                    ?>
                                    <span class="badge <?php echo $statusClass; ?>">
                                        <?php echo htmlspecialchars($req['status'] ?? 'Pending'); ?>
                                    </span>
                                </td>
                                <td><?php echo $req['created_at']; ?></td>
                                <td>
                                    <form action="client-requests.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="consultation_id" value="<?php echo $req['id']; ?>">
                                        <input type="hidden" name="status" value="Accepted">
                                        <button type="submit" class="btn-action btn-accept">Accept</button>
                                    </form>
                                    <form action="client-requests.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="consultation_id" value="<?php echo $req['id']; ?>">
                                        <input type="hidden" name="status" value="Rejected">
                                        <button type="submit" class="btn-action btn-reject">Reject</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; color: #94a3b8;">No client requests found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>