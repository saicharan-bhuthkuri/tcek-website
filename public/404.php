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

        /* Developer Contact & Gmail Section */
        .dev-contact-box {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            padding: 34px 28px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
            margin: 28px auto 0 auto;
            max-width: 620px;
            text-align: center;
            position: relative;
        }

        .dev-contact-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            margin-bottom: 14px;
        }

        .dev-avatar-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            box-shadow: 0 4px 14px rgba(99, 102, 241, 0.3);
        }

        .dev-header-meta {
            text-align: left;
        }

        .dev-header-meta .dev-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #ede9fe;
            color: #6366f1;
            font-size: 11px;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 8px;
            letter-spacing: 0.4px;
            text-transform: uppercase;
        }

        .dev-header-meta .dev-name-title {
            font-size: 17px;
            font-weight: 800;
            color: #0f172a;
            margin: 2px 0 0 0;
        }

        .dev-contact-desc {
            font-size: 14px;
            color: #475569;
            line-height: 1.6;
            margin: 0 0 18px 0;
        }

        .dev-email-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            padding: 8px 18px;
            border-radius: 30px;
            font-size: 14px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 22px;
        }

        .dev-actions-grid {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn-gmail-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #ea4335;
            color: #ffffff !important;
            padding: 12px 22px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none !important;
            box-shadow: 0 4px 14px rgba(234, 67, 53, 0.28);
            transition: all 0.25s ease;
        }

        .btn-gmail-primary:hover {
            background: #d93025;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(234, 67, 53, 0.38);
        }

        .btn-mailto-secondary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #0f172a;
            color: #ffffff !important;
            padding: 12px 20px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none !important;
            transition: all 0.25s ease;
        }

        .btn-mailto-secondary:hover {
            background: #1e293b;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.2);
        }

        .btn-copy-email {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: #f1f5f9;
            color: #334155 !important;
            border: 1px solid #cbd5e1;
            padding: 12px 18px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-copy-email:hover {
            background: #e2e8f0;
            color: #0f172a !important;
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

            <!-- Contact Developer & Gmail Section -->
            <div class="dev-contact-box">
                <div class="dev-contact-header">
                    <div class="dev-avatar-icon">
                        <i class="fas fa-laptop-code"></i>
                    </div>
                    <div class="dev-header-meta">
                        <span class="dev-badge"><i class="fas fa-shield-alt"></i> SYSTEM DEVELOPER</span>
                        <h3 class="dev-name-title">Charan</h3>
                    </div>
                </div>

                <p class="dev-contact-desc">
                    For system inquiries, website access questions, or technical support, please reach out directly to the authorized Developer.
                </p>

                <div class="dev-email-chip">
                    <i class="fas fa-envelope" style="color:#ea4335;"></i>
                    <span>saivortex.dev@gmail.com</span>
                </div>

                <div class="dev-actions-grid">
                    <a href="https://mail.google.com/mail/?view=cm&fs=1&to=saivortex.dev@gmail.com&su=TCEK%20Website%20Inquiry%20/%20Support" target="_blank" rel="noopener noreferrer" class="btn-gmail-primary" title="Compose message in Gmail">
                        <i class="fab fa-google"></i> Open in Gmail
                    </a>
                    <a href="mailto:saivortex.dev@gmail.com?subject=TCEK%20Website%20Inquiry%20/%20Support" class="btn-mailto-secondary" title="Open in default mail client">
                        <i class="fas fa-paper-plane"></i> Send Email
                    </a>
                    <button type="button" onclick="copyDevEmail()" class="btn-copy-email" id="btnCopyEmail" title="Copy developer email address">
                        <i class="fas fa-copy"></i> <span id="copyEmailLabel">Copy Email</span>
                    </button>
                    <button type="button" onclick="window.history.back()" class="btn-copy-email" style="background:#fff;" title="Go back to previous page">
                        <i class="fas fa-arrow-left"></i> Go Back
                    </button>
                </div>
            </div>
        </div>
    </main>

    <script>
    function copyDevEmail() {
        const email = 'saivortex.dev@gmail.com';
        const label = document.getElementById('copyEmailLabel');
        const btn = document.getElementById('btnCopyEmail');
        
        navigator.clipboard.writeText(email).then(() => {
            if (label) label.innerText = 'Copied!';
            if (btn) btn.style.borderColor = '#10b981';
            setTimeout(() => {
                if (label) label.innerText = 'Copy Email';
                if (btn) btn.style.borderColor = '#cbd5e1';
            }, 2000);
        }).catch(() => {
            prompt('Developer Email:', email);
        });
    }
    </script>

    <?php include 'footer.php'; ?>
</body>
</html>
