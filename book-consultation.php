<?php
declare(strict_types=1);
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header('location: login.php');
    exit;
}

require_once 'db.php';
require_once 'Consultation.php';
require_once 'User.php';

$message = '';
$success = '';

$userObj = new User();
$lawyers = $userObj->getLawyers();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lawyerId = (int) ($_POST['lawyer_id'] ?? 0);
    $subject = trim($_POST['subject'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($lawyerId > 0 && !empty($subject) && !empty($description)) {
        $database = new Database();
        $db = $database->connect();

        $consultation = new Consultation($db);
        if ($consultation->create((int) $_SESSION['user_id'], $lawyerId, $subject, $description)) {
            $success = "Your consultation request has been submitted successfully!";
        } else {
            $message = "Failed to submit request. Please try again.";
        }
    } else {
        $message = "Please fill in all fields and select a lawyer.";
    }
}

$role = $_SESSION['role'] ?? '';
$name = $_SESSION['name'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Consultation - Legal Consultation System</title>
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

        .form-container {
            background: #ffffff;
            padding: 35px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
            max-width: 600px;
        }

        .form-container h2 {
            font-size: 22px;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .form-container p.subtitle {
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

        .form-group label {
            display: block;
            margin-bottom: 6px;
            color: #334155;
            font-size: 14px;
            font-weight: 600;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #dcdfe6;
            border-radius: 6px;
            font-size: 14px;
            outline: none;
            background-color: #fff;
            color: #1e293b;
            transition: border-color 0.2s ease;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #2563eb;
        }

        .form-group textarea {
            resize: vertical;
            height: 120px;
        }

        .btn-submit {
            padding: 12px 20px;
            background-color: #2563eb;
            color: #ffffff;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s ease;
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
                <span>Client Panel</span>
            </div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="book-consultation.php" class="active">Book Consultation</a></li>
                <li><a href="my-consultations.php">My Appointments</a></li>
                <li><a href="profile-settings.php">Profile Settings</a></li>
            </ul>
        </div>
        <div class="sidebar-footer">
            <a href="logout.php" class="btn-logout">Log Out</a>
        </div>
    </div>

    <div class="main-content">
        <div class="form-container">
            <h2>Book a Legal Consultation</h2>
            <p class="subtitle">Submit your case details to connect with professional lawyers.</p>

            <?php if (!empty($message)): ?>
                <div class="alert-message"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <form action="book-consultation.php" method="POST">
                <div class="form-group">
                    <label for="lawyer_id">Select Lawyer</label>
                    <select name="lawyer_id" id="lawyer_id" required>
                        <option value="">-- Choose a Lawyer --</option>
                        <?php foreach ($lawyers as $lawyer): ?>
                            <option value="<?php echo $lawyer['id']; ?>"><?php echo htmlspecialchars($lawyer['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="subject">Consultation Subject</label>
                    <input type="text" id="subject" name="subject"
                        placeholder="e.g., Contract Review, Family Law Inquiry" required>
                </div>

                <div class="form-group">
                    <label for="description">Case Description & Details</label>
                    <textarea id="description" name="description"
                        placeholder="Provide clear details about your legal case..." required></textarea>
                </div>

                <button type="submit" class="btn-submit">Submit Request</button>
            </form>
        </div>
    </div>

</body>

</html>