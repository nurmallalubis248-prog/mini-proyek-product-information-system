<?php
require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($fullname) || empty($username) || empty($password)) {
        $error = 'Harap isi semua kolom yang wajib!';
    } elseif ($password !== $confirm_password) {
        $error = 'Konfirmasi password tidak cocok!';
    } else {
        if (!isset($_SESSION['users'])) {
            $_SESSION['users'] = [];
        }

        if (isset($_SESSION['users'][$username]) || (defined('ADMIN_USERNAME') && $username === ADMIN_USERNAME)) {
            $error = 'Username sudah terdaftar!';
        } else {
            $_SESSION['users'][$username] = $password;
            $_SESSION['success_msg'] = 'Registrasi berhasil! Silakan login.';
            header('Location: login.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PIS Register - Secure Access</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f2edf0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Container Utama Full Screen / Split Screen */
        .login-wrapper {
            width: 100vw;
            height: 100vh;
            display: grid;
            grid-template-columns: 48% 52%;
            background: #f2edf0;
            overflow: hidden;
        }

        /* Panel Kiri (Visual/Brand) dengan Tema Warna Pink */
        .visual-panel {
            background-color: #ff69b4;
            padding: 60px 80px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            color: #ffffff;
            position: relative;
        }

        .brand-header {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-icon {
            font-size: 24px;
            line-height: 1;
        }

        .brand-header small {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 2.5px;
            color: #ffe6f0;
            text-transform: uppercase;
        }

        .visual-content {
            margin-top: auto;
            margin-bottom: auto;
            max-width: 500px;
        }

        .visual-content h1 {
            font-family: "Georgia", serif;
            font-size: 42px;
            font-weight: 400;
            line-height: 1.15;
            margin-bottom: 24px;
            color: #ffffff;
        }

        .visual-content h2 {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 16px;
            line-height: 1.3;
            color: #fff0f5;
        }

        .visual-content p {
            font-size: 14px;
            line-height: 1.6;
            color: #ffe6f0;
            opacity: 0.9;
        }

        .visual-footer {
            font-size: 11px;
            color: #ffe6f0;
            opacity: 0.8;
        }

        /* Panel Kanan (Form Card Melayang) */
        .form-panel {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px;
            position: relative;
            overflow-y: auto;
        }

        .form-card {
            background: #ffffff;
            width: 100%;
            max-width: 440px;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        }

        .form-header {
            margin-bottom: 24px;
        }

        .form-header .label {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 2px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 8px;
            display: block;
        }

        .form-header h2 {
            font-size: 32px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 10px;
        }

        .error {
            background: #ffe6e6;
            color: #d93025;
            border: 1px solid #ffcdd2;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 12px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 14px;
        }

        .form-group label {
            display: block;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.5px;
            color: #4a4a4a;
            margin-bottom: 6px;
            text-transform: uppercase;
        }

        .input-box {
            position: relative;
        }

        .input-box input {
            width: 100%;
            height: 44px;
            border: 1px solid #e1e1e1;
            border-radius: 8px;
            background: #fcfcfc;
            color: #333333;
            font-size: 13px;
            padding: 0 40px 0 38px;
            outline: none;
            transition: all 0.2s ease;
        }

        .input-box input:focus {
            background: #ffffff;
            border-color: #ff69b4;
            box-shadow: 0 0 0 3px rgba(255, 105, 180, 0.15);
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #888888;
            font-size: 14px;
        }

        .password-button {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: #888888;
            cursor: pointer;
            font-size: 14px;
        }

        .login-button {
            width: 100%;
            height: 48px;
            border: none;
            border-radius: 8px;
            background: #ff69b4;
            color: #ffffff;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            cursor: pointer;
            margin-top: 10px;
            transition: background 0.2s, transform 0.1s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .login-button:hover {
            background: #ff1493;
        }

        .login-button:active {
            transform: scale(0.99);
        }

        .login-option {
            margin-top: 18px;
            text-align: center;
            font-size: 13px;
            color: #666;
        }

        .login-option a {
            color: #ff69b4;
            font-weight: 700;
            text-decoration: none;
            margin-left: 4px;
        }

        .login-option a:hover {
            color: #ff1493;
            text-decoration: underline;
        }

        .page-copyright {
            position: absolute;
            bottom: 24px;
            font-size: 11px;
            color: #888888;
        }

        /* Responsif untuk Layar Kecil */
        @media (max-width: 900px) {
            .login-wrapper {
                grid-template-columns: 1fr;
                height: auto;
            }
            .visual-panel {
                padding: 40px 30px;
            }
            .form-panel {
                padding: 40px 20px;
            }
            .page-copyright {
                position: relative;
                bottom: 0;
                margin-top: 20px;
            }
        }
    </style>
</head>
<body>

    <div class="login-wrapper">
        <!-- Panel Sisi Kiri (Visual & Branding) -->
        <section class="visual-panel">
            <div class="brand-header">
                <span class="brand-icon">❖</span>
                <small>PRODUCT MANAGEMENT SUITE</small>
            </div>

            <div class="visual-content">
                <h1>"Nurmala Lubis"<br>Product Information System</h1>
                <h2>Register Your Workspace Account.<br>Your Data. Precisely Managed.</h2>
                <p>
                    Create a secure, integrated account for complete data oversight, from product tracking to inventory control.
                </p>
            </div>

            <div class="visual-footer">
                Product Information System Nurmala Lubis © 2026
            </div>
        </section>

        <!-- Panel Sisi Kanan (Form Registrasi) -->
        <section class="form-panel">
            <div class="form-card">
                <div class="form-header">
                    <span class="label">SECURE USER REGISTRATION</span>
                    <h2>Create Account</h2>
                </div>

                <?php if ($error): ?>
                    <div class="error">
                        <?= htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label for="fullname">FULL NAME</label>
                        <div class="input-box">
                            <span class="input-icon">👤</span>
                            <input type="text" id="fullname" name="fullname" placeholder="FULL NAME" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="username">USERNAME / USER ID</label>
                        <div class="input-box">
                            <span class="input-icon">👤</span>
                            <input type="text" id="username" name="username" placeholder="USERNAME" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">EMAIL ADDRESS</label>
                        <div class="input-box">
                            <span class="input-icon">✉</span>
                            <input type="email" id="email" name="email" placeholder="EMAIL ADDRESS" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password">PASSWORD</label>
                        <div class="input-box">
                            <span class="input-icon">🔒</span>
                            <input type="password" id="password" name="password" placeholder="PASSWORD" required>
                            <button type="button" class="password-button" onclick="togglePassword('password', 'pBtn1')" id="pBtn1">👁</button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">CONFIRM PASSWORD</label>
                        <div class="input-box">
                            <span class="input-icon">🔒</span>
                            <input type="password" id="confirm_password" name="confirm_password" placeholder="CONFIRM PASSWORD" required>
                            <button type="button" class="password-button" onclick="togglePassword('confirm_password', 'pBtn2')" id="pBtn2">👁</button>
                        </div>
                    </div>

                    <button type="submit" class="login-button">
                        CREATE SECURE ACCOUNT →
                    </button>
                </form>

                <div class="login-option">
                    Already have an account? <a href="login.php">Sign In</a>
                </div>
            </div>

            <div class="page-copyright">
                © 2026 Nurmala Lubis & Affiliates. All Rights Reserved.
            </div>
        </section>
    </div>

    <script>
        function togglePassword(inputId, buttonId) {
            const password = document.getElementById(inputId);
            const button = document.getElementById(buttonId);

            if (password.type === "password") {
                password.type = "text";
                button.textContent = "🙈";
            } else {
                password.type = "password";
                button.textContent = "👁";
            }
        }
    </script>
</body>
</html>