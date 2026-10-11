<?php
/**
 * TCEK Master Global 404 Error Page
 * Trinity College of Engineering & Technology
 * 
 * Controlled exclusively by the Developer account (saivortex.dev@gmail.com).
 * When enabled (ON): Renders full Trinity College branded 404 experience.
 * When disabled (OFF): Emits standard server 404 message.
 */

http_response_code(404);

require_once __DIR__ . '/backend/settings_crud.php';

$is404CustomEnabled = get_global_404_status();
$settings = load_system_settings();
$customMsg = !empty($settings['global_404_custom_message']) 
    ? $settings['global_404_custom_message'] 
    : 'The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>404 - Page Not Found | Trinity College of Engineering & Technology</title>
    <?php include 'head.php'; ?>
    <style>
        .error-hero-section {
            background: linear-gradient(135deg, #f8fafc 0%, #eef2f6 100%);
            padding: 80px 20px 90px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .error-hero-section::before {
            content: '';
            position: absolute;
            top: -100px;
            right: -100px;
            width: 350px;
            height: 350px;
            background: rgba(0, 184, 148, 0.08);
            border-radius: 50%;
            pointer-events: none;
        }

        .error-hero-section::after {
            content: '';
            position: absolute;
            bottom: -80px;
            left: -80px;
            width: 300px;
            height: 300px;
            background: rgba(99, 102, 241, 0.06);
            border-radius: 50%;
            pointer-events: none;
        }

        .error-content-box {
            max-width: 780px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }

        .error-badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #fff;
            border: 1px solid #e2e8f0;
            padding: 6px 16px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
            margin-bottom: 24px;
        }

        .error-badge-pill .pulse-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #ef4444;
            display: inline-block;
            box-shadow: 0 0 8px rgba(239, 68, 68, 0.6);
        }

        .error-giant-code {
            font-size: 120px;
            font-weight: 900;
            line-height: 1;
            letter-spacing: -3px;
            background: linear-gradient(135deg, #00b894 0%, #0984e3 50%, #6366f1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0 0 16px 0;
            text-shadow: 0 10px 30px rgba(0, 184, 148, 0.15);
        }

        @media (max-width: 768px) {
            .error-giant-code {
                font-size: 80px;
            }
        }

        .error-headline {
            font-size: 28px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 14px;
        }

        .error-desc {
            font-size: 16px;
            color: #64748b;
            line-height: 1.6;
            max-width: 600px;
            margin: 0 auto 34px auto;
        }

        .error-btn-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            flex-wrap: wrap;
            margin-bottom: 50px;
        }

        .btn-error-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #00b894;
            color: #ffffff !important;
            padding: 12px 26px;
            border-radius: 10px;
            font-size: 14.5px;
            font-weight: 700;
            text-decoration: none !important;
            box-shadow: 0 4px 15px rgba(0, 184, 148, 0.25);
            transition: all 0.25s ease;
        }

        .btn-error-primary:hover {
            background: #00a884;
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(0, 184, 148, 0.35);
        }

        .btn-error-secondary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #ffffff;
            color: #1e293b !important;
            border: 1px solid #cbd5e1;
            padding: 12px 24px;
            border-radius: 10px;
            font-size: 14.5px;
            font-weight: 700;
            text-decoration: none !important;
            transition: all 0.25s ease;
        }

        .btn-error-secondary:hover {
            border-color: #00b894;
            color: #00b894 !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .quick-nav-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 18px;
            text-align: left;
            margin-top: 20px;
        }

        .quick-nav-item {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            text-decoration: none !important;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .quick-nav-item:hover {
            border-color: #00b894;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
        }

        .quick-nav-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: rgba(0, 184, 148, 0.1);
            color: #00b894;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .quick-nav-text strong {
            display: block;
            font-size: 14px;
            color: #0f172a;
            margin-bottom: 2px;
        }

        .quick-nav-text span {
            font-size: 12px;
            color: #64748b;
        }
    </style>
</head>
<body>
    <?php $page = '404'; include 'header.php'; ?>

    <main class="error-hero-section">
        <div class="error-content-box">
            <div class="error-badge-pill">
                <span class="pulse-dot"></span>
                <span>HTTP Error 404 &bull; Resource Not Found</span>
            </div>

            <h1 class="error-giant-code">404</h1>
            <h2 class="error-headline">Lost Your Way on Campus?</h2>
            <p class="error-desc"><?php echo htmlspecialchars($customMsg); ?></p>

            <div class="error-btn-row">
                <a href="index.php" class="btn-error-primary">
                    <i class="fas fa-home"></i> Return to Homepage
                </a>
                <a href="departments.php" class="btn-error-secondary">
                    <i class="fas fa-sitemap"></i> Academic Departments
                </a>
                <button type="button" onclick="window.history.back()" class="btn-error-secondary" style="cursor:pointer;">
                    <i class="fas fa-arrow-left"></i> Go Back
                </button>
            </div>

            <!-- Quick Shortcut Recommendations -->
            <div class="quick-nav-cards">
                <a href="admission.php" class="quick-nav-item">
                    <div class="quick-nav-icon"><i class="fas fa-graduation-cap"></i></div>
                    <div class="quick-nav-text">
                        <strong>Admissions</strong>
                        <span>B.Tech, Diploma &amp; MBA</span>
                    </div>
                </a>

                <a href="circulars.php" class="quick-nav-item">
                    <div class="quick-nav-icon" style="background:rgba(99,102,241,0.1); color:#6366f1;"><i class="fas fa-bullhorn"></i></div>
                    <div class="quick-nav-text">
                        <strong>Circulars</strong>
                        <span>Official Notices &amp; Timetables</span>
                    </div>
                </a>

                <a href="events.php" class="quick-nav-item">
                    <div class="quick-nav-icon" style="background:rgba(245,158,11,0.1); color:#f59e0b;"><i class="fas fa-calendar-alt"></i></div>
                    <div class="quick-nav-text">
                        <strong>Campus Events</strong>
                        <span>Festivals, Sports &amp; Media</span>
                    </div>
                </a>

                <a href="contact.php" class="quick-nav-item">
                    <div class="quick-nav-icon" style="background:rgba(14,165,233,0.1); color:#0ea5e9;"><i class="fas fa-headset"></i></div>
                    <div class="quick-nav-text">
                        <strong>Contact Desk</strong>
                        <span>Peddapally Campus Helpline</span>
                    </div>
                </a>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
