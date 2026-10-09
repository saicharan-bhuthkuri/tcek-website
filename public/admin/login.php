<?php
/**
 * TCEK Admin Login Portal
 * Styled with Modern Clean Light Theme matching Image 2 & Trinity College Design System
 */
require_once __DIR__ . '/../backend/auth.php';

// If already logged in, redirect straight to dashboard
if (is_admin_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

$error_msg = null;
$success_msg = null;

if (isset($_GET['msg']) && $_GET['msg'] === 'logged_out') {
    $success_msg = 'You have been safely logged out of the TCEK Administrative Portal.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($csrfToken)) {
        $error_msg = 'Security validation failed (CSRF token mismatch). Please reload and try again.';
        http_response_code(403);
    } else {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        $res = verify_admin_login($username, $password);
        if ($res['success']) {
            header('Location: dashboard.php');
            exit;
        } else {
            $error_msg = $res['message'];
            if (!empty($res['retry_after'])) {
                http_response_code(429);
                header('Retry-After: ' . (int)$res['retry_after']);
            }
        }
    }
}

$db_connected = isDbConnected();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff &amp; Admin Login - Trinity College of Engineering &amp; Technology</title>
    <!-- Fonts & Icons matching index.php -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Main College Website Stylesheet -->
    <link rel="stylesheet" href="../css/style.css">
    <!-- Admin Extension Stylesheet -->
    <link rel="stylesheet" href="css/admin.css?v=<?php echo filemtime(__DIR__ . '/css/admin.css'); ?>">
    <style>
        body {
            background-color: #f8fafc;
            font-family: 'Poppins', sans-serif;
            color: #0f172a;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Clean Modern Light Hero matching Image 2 */
        .portal-hero-light {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 40px 20px 75px;
            text-align: center;
            position: relative;
        }

        .portal-hero-container {
            max-width: 900px;
            margin: 0 auto;
        }

        .portal-pill-light {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #059669;
            padding: 5px 18px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 12px;
            letter-spacing: 0.4px;
        }

        .portal-hero-light h1 {
            font-size: 30px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 8px;
            letter-spacing: -0.4px;
        }

        .portal-hero-light p {
            color: #64748b;
            font-size: 14.5px;
            max-width: 650px;
            margin: 0 auto;
        }

        /* Login Card Wrap */
        .portal-card-wrap {
            max-width: 480px;
            margin: -50px auto 60px;
            padding: 0 20px;
            position: relative;
            z-index: 10;
            width: 100%;
        }

        .portal-login-card {
            background: #ffffff;
            border-radius: 18px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 12px 35px rgba(15, 23, 42, 0.05);
            padding: 38px 36px;
            position: relative;
            overflow: hidden;
        }

        .portal-login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #d97706, #00b894, #2563eb);
        }

        .card-heading {
            text-align: center;
            margin-bottom: 24px;
        }

        .card-heading h2 {
            font-size: 21px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 6px;
        }

        .card-heading p {
            font-size: 13.5px;
            color: #64748b;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 7px;
        }

        .input-group-modern {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-group-modern .input-icon {
            position: absolute;
            left: 15px;
            color: #94a3b8;
            font-size: 14px;
            pointer-events: none;
        }

        .input-group-modern .form-control {
            width: 100%;
            padding: 11px 16px 11px 42px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 13.5px;
            font-family: 'Poppins', sans-serif;
            color: #0f172a;
            background: #f8fafc;
            transition: all 0.2s ease;
        }

        .input-group-modern .form-control:focus {
            background: #ffffff;
            border-color: #00b894;
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 184, 148, 0.12);
        }

        .btn-pwd-toggle {
            position: absolute;
            right: 14px;
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            font-size: 14px;
            padding: 4px;
        }

        .btn-pwd-toggle:hover {
            color: #00b894;
        }

        .form-meta-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13px;
            margin-bottom: 24px;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #64748b;
            cursor: pointer;
        }

        .return-link {
            color: #00b894;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }

        .return-link:hover {
            color: #00a884;
            text-decoration: underline;
        }

        .btn-portal-submit {
            width: 100%;
            background: #00b894;
            color: #ffffff;
            border: none;
            padding: 13px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(0, 184, 148, 0.25);
        }

        .btn-portal-submit:hover {
            background: #00a884;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(0, 184, 148, 0.35);
        }

        .card-footer-info {
            margin-top: 24px;
            padding-top: 18px;
            border-top: 1px solid #f1f5f9;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
        }

        .portal-help-note {
            margin-top: 20px;
            text-align: center;
            font-size: 13px;
            color: #64748b;
        }

        .portal-help-note a {
            color: #00b894;
            font-weight: 600;
            text-decoration: none;
        }

        /* Sleek Minimal Portal Footer */
        .portal-minimal-footer {
            margin-top: auto;
            padding: 24px 20px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            background: #ffffff;
            color: #94a3b8;
            font-size: 12.5px;
        }

        .portal-minimal-footer a {
            color: #00b894;
            text-decoration: none;
            font-weight: 600;
            margin-left: 6px;
        }

        .portal-minimal-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <!-- Portal Hero Header (Clean Light Theme) -->
    <section class="portal-hero-light">
        <div class="portal-hero-container">
            <div class="portal-pill-light">
                <span class="pulse-indicator"></span>
                <span>AUTHENTICATED ACCESS CONTROL</span>
            </div>
            <h1>Staff &amp; Administrator Portal</h1>
            <p>Access notifications dispatch, PDF examination circulars, campus event fests, and media storage registry</p>
        </div>
    </section>

    <!-- Main Login Card -->
    <div class="portal-card-wrap">
        <div class="portal-login-card">
            <div class="card-heading">
                <h2>Sign In to TCEK Portal</h2>
                <p>Enter your administrative credentials to continue</p>
            </div>

            <?php if ($success_msg): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle" style="color:#059669; font-size:16px;"></i>
                    <span><?php echo htmlspecialchars($success_msg); ?></span>
                </div>
            <?php endif; ?>

            <?php if ($error_msg): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle" style="color:#dc2626; font-size:16px;"></i>
                    <span><?php echo htmlspecialchars($error_msg); ?></span>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(get_csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                <div class="form-group">
                    <label for="username" class="form-label"><i class="fas fa-user-shield" style="color:#00b894;"></i> Username or Email</label>
                    <div class="input-group-modern">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" id="username" name="username" class="form-control" placeholder="Enter username or email" value="<?php echo htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required autocomplete="username" autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label"><i class="fas fa-key" style="color:#00b894;"></i> Secure Password</label>
                    <div class="input-group-modern">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Enter password" required autocomplete="current-password">
                        <button type="button" class="btn-pwd-toggle" id="btnTogglePassword" title="Show/Hide Password">
                            <i class="far fa-eye" id="togglePasswordIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="form-meta-row">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember" style="accent-color:#00b894;">
                        <span>Keep me signed in</span>
                    </label>
                    <a href="../index.php" class="return-link"><i class="fas fa-arrow-left"></i> Return to Site</a>
                </div>

                <button type="submit" class="btn-portal-submit">
                    <span>Access Dashboard</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </form>

            <div class="card-footer-info">
                <span>Portal Security: <strong>Active</strong> &bull; Authenticated Access Control</span>
            </div>
        </div>

        <div class="portal-help-note">
            Trinity College of Engineering and Technology &bull; Bandarikunta, Peddapalli<br>
            Technical support: <a href="mailto:saivortex.dev@gmail.com">saivortex.dev@gmail.com</a>
        </div>
    </div>

    <!-- Sleek Minimal Portal Footer -->
    <footer class="portal-minimal-footer">
        <p>&copy; <?php echo date('Y'); ?> Trinity College of Engineering &amp; Technology. All Rights Reserved. &bull; <a href="../index.php"><i class="fas fa-arrow-left"></i> Return to Main Website</a></p>
    </footer>

    <script>
        const btnToggle = document.getElementById('btnTogglePassword');
        const pwdInput = document.getElementById('password');
        const icon = document.getElementById('togglePasswordIcon');

        if (btnToggle && pwdInput) {
            btnToggle.addEventListener('click', () => {
                const isPassword = pwdInput.type === 'password';
                pwdInput.type = isPassword ? 'text' : 'password';
                icon.className = isPassword ? 'far fa-eye-slash' : 'far fa-eye';
            });
        }
    </script>
</body>
</html>
