<?php
declare(strict_types=1);
require_once('User.php');
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = isset($_POST['role']) ? trim($_POST['role']) : 'client';
    $user = new User();
    if ($user->register($name, $email, $password, $role)) {
        $message = 'Registration successful..';
        header('location: login.php');
        exit();
    } else {
        $message = "An error occurred during registration. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - Legal Consultation System</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f0f4f8 0%, #d9e2ec 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px 0;
        }

        .card {
            background: #ffffff;
            padding: 45px 35px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08);
            width: 100%;
            max-width: 420px;
            border: 1px solid rgba(226, 232, 240, 0.8);
        }

        .brand-header {
            text-align: center;
            margin-bottom: 25px;
        }

        .brand-icon {
            width: 50px;
            height: 50px;
            background-color: #eff6ff;
            color: #1e40af;
            border-radius: 12px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 14px;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.1);
        }

        .brand-icon svg {
            width: 26px;
            height: 26px;
            fill: currentColor;
        }

        .brand-header h1 {
            color: #1e293b;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: -0.3px;
            margin-bottom: 4px;
        }

        .brand-header p {
            color: #64748b;
            font-size: 13px;
            font-weight: 500;
        }

        .alert-message {
            background-color: #fef2f2;
            color: #dc2626;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 22px;
            text-align: center;
            border: 1px solid #fecaca;
            font-weight: 500;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #334155;
            font-size: 13px;
            font-weight: 600;
        }

        .form-group input, 
        .form-group select {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            background-color: #f8fafc;
            color: #0f172a;
            transition: all 0.2s ease;
        }

        .form-group input:focus, 
        .form-group select:focus {
            border-color: #2563eb;
            background-color: #fff;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        .btn-submit {
            width: 100%;
            padding: 13px;
            background-color: #1e40af;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s ease, transform 0.1s ease;
            margin-top: 8px;
            box-shadow: 0 4px 12px rgba(30, 64, 175, 0.2);
        }

        .btn-submit:hover {
            background-color: #1e3a8a;
        }

        .btn-submit:active {
            transform: scale(0.99);
        }

        .footer-text {
            text-align: center;
            margin-top: 22px;
            font-size: 13px;
            color: #64748b;
        }

        .footer-text a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 600;
        }

        .footer-text a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="card">
        <div class="brand-header">
            <div class="brand-icon">
                <!-- Balance scale icon representing legal consultation -->
                <svg viewBox="0 0 24 24">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path>
                </svg>
            </div>
            <h1>Legal Consultation System</h1>
            <p>Create your professional account</p>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert-message">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" placeholder="Your name" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="example@mail.com" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>

            <div class="form-group">
                <label for="role">Register As</label>
                <select id="role" name="role" required>
                    <option value="" disabled selected>Choose your role...</option>
                    <option value="client">Client</option>
                    <option value="lawyer">Lawyer</option>
                </select>
            </div>

            <button type="submit" class="btn-submit">Sign Up</button>
        </form>

        <div class="footer-text">
            Already have an account? <a href="login.php">Log in</a>
        </div>
    </div>

</body>

</html>