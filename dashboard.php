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

$stmt = $db->prepare("SELECT name FROM users WHERE id = :id");
$stmt->execute([':id' => $userId]);
$currentUser = $stmt->fetch(PDO::FETCH_ASSOC);
$userName = $currentUser['name'] ?? 'User';

if ($role === 'client') {
    $stmtAppr = $db->prepare("SELECT consultations.*, users.name AS lawyer_name FROM consultations JOIN users ON consultations.lawyer_id = users.id WHERE consultations.client_id = :client_id AND consultations.status = 'Accepted' ORDER BY consultations.created_at DESC");
    $stmtAppr->execute([':client_id' => $userId]);
} else {
    $stmtAppr = $db->prepare("SELECT consultations.*, users.name AS client_name FROM consultations JOIN users ON consultations.client_id = users.id WHERE consultations.lawyer_id = :lawyer_id AND consultations.status = 'Accepted' ORDER BY consultations.created_at DESC");
    $stmtAppr->execute([':lawyer_id' => $userId]);
}
$approvedConsultations = $stmtAppr->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Legal Portal</title>
    <?php if ($role === 'lawyer'): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?php endif; ?>
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
            position: sticky;
            top: 0;
            height: 100vh;
            flex-shrink: 0;
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
            font-size: 24px;
            color: #1e293b;
            margin-bottom: 10px;
        }

        p.welcome-text {
            color: #64748b;
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 25px;
        }

        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 35px;
        }

        .card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 20px;
            border-radius: 8px;
            text-decoration: none;
            color: inherit;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border-color: #cbd5e1;
        }

        .card h4 {
            font-size: 16px;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .card p {
            font-size: 13px;
            color: #64748b;
        }

        .analytics-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 35px;
        }

        @media (max-width: 992px) {
            .analytics-grid {
                grid-template-columns: 1fr;
            }
        }

        .chart-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 20px;
            border-radius: 8px;
        }

        .chart-card h3 {
            font-size: 16px;
            color: #1e293b;
            margin-bottom: 15px;
        }

        .chart-container {
            position: relative;
            height: 220px;
            width: 100%;
        }

        .stats-box {
            display: flex;
            flex-direction: column;
            gap: 12px;
            justify-content: center;
        }

        .stat-item {
            background: #ffffff;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #2563eb;
        }

        .stat-item span {
            font-size: 12px;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
        }

        .stat-item h4 {
            font-size: 20px;
            color: #1e293b;
            margin-top: 2px;
        }

        .approved-section {
            margin-top: 30px;
            border-top: 1px solid #e2e8f0;
            padding-top: 25px;
        }

        .approved-section h3 {
            font-size: 18px;
            color: #1e293b;
            margin-bottom: 15px;
        }

        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
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
            background-color: #ecfdf5;
            color: #059669;
            display: inline-block;
        }

        .empty-row {
            text-align: center;
            color: #94a3b8;
            padding: 20px;
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
                <li><a href="dashboard.php" class="active">Dashboard</a></li>
                <?php if ($role === 'client'): ?>
                    <li><a href="book-consultation.php">Book Consultation</a></li>
                    <li><a href="my-consultations.php">My Appointments</a></li>
                <?php else: ?>
                    <li><a href="client-requests.php">Client Requests</a></li>
                    <li><a href="manage-schedule.php">Manage Schedule</a></li>
                <?php endif; ?>
                <li><a href="profile-settings.php">Profile Settings</a></li>
            </ul>
        </div>
        <div class="sidebar-footer">
            <a href="logout.php" class="btn-logout">Log Out</a>
        </div>
    </div>

    <div class="main-content">
        <div class="container">
            <h2>Welcome back, <?php echo htmlspecialchars($userName); ?>! 👋</h2>
            <p class="welcome-text">
                <?php if ($role === 'lawyer'): ?>
                    You are logged in as a <strong>Lawyer</strong>. Use the quick links below or the sidebar to manage incoming client consultation requests and organize your legal schedule.
                <?php else: ?>
                    You are logged in as a <strong>Client</strong>. You can book new legal consultations, track the status of your existing appointments, and manage your profile settings.
                <?php endif; ?>
            </p>

            <div class="card-grid">
                <?php if ($role === 'client'): ?>
                    <a href="book-consultation.php" class="card">
                        <h4>Book Consultation</h4>
                        <p>Submit a new legal consultation request to our professional legal team.</p>
                    </a>
                    <a href="my-consultations.php" class="card">
                        <h4>My Appointments</h4>
                        <p>Check the status and history of all your submitted legal requests.</p>
                    </a>
                <?php else: ?>
                    <a href="client-requests.php" class="card">
                        <h4>Client Requests</h4>
                        <p>Review new consultation submissions from clients and accept or reject them.</p>
                    </a>
                    <a href="manage-schedule.php" class="card">
                        <h4>Manage Schedule</h4>
                        <p>View all accepted appointments and organize your working calendar.</p>
                    </a>
                <?php endif; ?>
                <a href="profile-settings.php" class="card">
                    <h4>Profile Settings</h4>
                    <p>Update your personal account details, name, and email address.</p>
                </a>
            </div>

            <?php if ($role === 'lawyer'): ?>
            <div class="analytics-grid">
                <div class="chart-card">
                    <h3>Consultations Overview</h3>
                    <div class="chart-container">
                        <canvas id="consultationChart"></canvas>
                    </div>
                </div>
                <div class="chart-card stats-box">
                    <div class="stat-item">
                        <span>Total Accepted</span>
                        <h4><?php echo count($approvedConsultations ?? []); ?></h4>
                    </div>
                </div>
            </div>
            <script>
                const ctx = document.getElementById('consultationChart').getContext('2d');
                const consultationChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                        datasets: [{
                            label: 'Consultations',
                            data: [2, 5, 3, 7, 4, <?php echo count($approvedConsultations ?? []); ?>],
                            backgroundColor: '#2563eb',
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: '#f1f5f9' }
                            },
                            x: {
                                grid: { display: false }
                            }
                        }
                    }
                });
            </script>
            <?php endif; ?>

            <div class="approved-section">
                <h3><?php echo ($role === 'lawyer') ? 'My Accepted Client Requests' : 'My Approved Consultations'; ?></h3>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th><?php echo ($role === 'lawyer') ? 'Client Name' : 'Lawyer Name'; ?></th>
                                <th>Subject</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($approvedConsultations)): ?>
                                <?php foreach ($approvedConsultations as $appr): ?>
                                    <tr>
                                        <td><?php echo $appr['id']; ?></td>
                                        <td><?php echo htmlspecialchars(($role === 'lawyer') ? ($appr['client_name'] ?? 'Unknown') : ($appr['lawyer_name'] ?? 'Unknown')); ?></td>
                                        <td><?php echo htmlspecialchars($appr['subject']); ?></td>
                                        <td><?php echo htmlspecialchars($appr['description']); ?></td>
                                        <td><span class="badge">Accepted</span></td>
                                        <td><?php echo $appr['created_at']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="empty-row">No approved consultations found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</body>
</html>