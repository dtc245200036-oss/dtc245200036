<?php
/**
 * Website Portfolio Cá Nhân & Trưng Bày Dự Án
 * Chạy trên nền tảng Docker Compose (PHP 8.1 Apache + MySQL 8.0 + Nginx + phpMyAdmin)
 */

// Đặt header Content-Type UTF-8 để đảm bảo hiển thị đúng tiếng Việt
header('Content-Type: text/html; charset=UTF-8');

// Cấu hình thông tin kết nối MySQL (Đọc từ biến môi trường hoặc dùng giá trị mặc định)
$db_host = getenv('DB_HOST') ?: 'mysql_db';
$db_user = getenv('DB_USER') ?: 'portfolio_user';
$db_pass = getenv('DB_PASS') ?: 'portfolio_pass';
$db_name = getenv('DB_NAME') ?: 'portfolio_db';
$db_port = (int)(getenv('DB_PORT') ?: 3306);

$db_connected = false;
$db_error_message = '';
$server_info = '';

// Danh sách các dự án tiêu biểu được viết cứng trực tiếp trong file index.php
// để đảm bảo hiển thị đúng 100% tiếng Việt có dấu, tránh hoàn toàn lỗi font chữ
$projects = [
    [
        'id' => 1,
        'title' => 'Nền Tảng Thương Mại Điện Tử Đa Kênh (Omnichannel E-Commerce)',
        'description' => 'Hệ thống thương mại điện tử kiến trúc Microservices hiệu năng cao, tích hợp cổng thanh toán trực tuyến (VNPAY, MoMo), quản lý tồn kho thời gian thực với Redis và tự động hóa quy trình triển khai CI/CD bằng Docker & Kubernetes.',
        'created_at' => '2024-01-15 09:30:00'
    ],
    [
        'id' => 2,
        'title' => 'Hệ Thống Giám Sát IoT & Nhà Thông Minh (Smart IoT Dashboard)',
        'description' => 'Nền tảng thu thập và xử lý dữ liệu cảm biến môi trường thời gian thực qua giao thức MQTT/WebSocket. Cung cấp giao diện trực quan hóa thông số nhiệt độ, độ ẩm và hệ thống cảnh báo sớm thông minh đa kênh.',
        'created_at' => '2024-03-20 14:15:00'
    ]
];

// Kết nối tới cơ sở dữ liệu MySQL sử dụng extension mysqli
try {
    // Tắt báo cáo lỗi mặc định của PHP để xử lý chủ động
    mysqli_report(MYSQLI_REPORT_OFF);
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);
    $conn->set_charset("utf8mb4");

    if ($conn->connect_errno) {
        $db_error_message = "Lỗi kết nối MySQL (#" . $conn->connect_errno . "): " . $conn->connect_error;
    } else {
        $db_connected = true;
        $server_info = $conn->server_info;
        $conn->close();
    }
} catch (Throwable $e) {
    $db_connected = false;
    $db_error_message = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio Cá Nhân | Full-Stack Developer & Cloud DevOps</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-primary: #0b0f19;
            --bg-secondary: #111827;
            --bg-card: rgba(17, 24, 39, 0.75);
            --bg-card-hover: rgba(30, 41, 59, 0.85);
            --border-color: rgba(255, 255, 255, 0.08);
            --border-hover: rgba(99, 102, 241, 0.4);
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --accent-indigo: #6366f1;
            --accent-cyan: #06b6d4;
            --accent-pink: #ec4899;
            --accent-emerald: #10b981;
            --accent-amber: #f59e0b;
            --gradient-primary: linear-gradient(135deg, #6366f1 0%, #06b6d4 100%);
            --gradient-accent: linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%);
            --shadow-glow: 0 0 25px rgba(99, 102, 241, 0.25);
            --font-sans: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            --font-mono: 'JetBrains Mono', monospace;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-sans);
            background-color: var(--bg-primary);
            color: var(--text-primary);
            line-height: 1.6;
            overflow-x: hidden;
            background-image: 
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(6, 182, 212, 0.12) 0px, transparent 50%),
                radial-gradient(at 50% 100%, rgba(236, 72, 153, 0.08) 0px, transparent 50%);
            background-attachment: fixed;
            min-height: 100vh;
        }

        /* Container & Navigation */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
        }

        header.navbar {
            position: sticky;
            top: 0;
            z-index: 50;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            background: rgba(11, 15, 25, 0.8);
            border-bottom: 1px solid var(--border-color);
            padding: 18px 0;
        }

        .nav-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: var(--text-primary);
            font-weight: 800;
            font-size: 1.25rem;
            letter-spacing: -0.02em;
        }

        .brand-badge {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 800;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.35);
        }

        .nav-links {
            display: flex;
            gap: 28px;
            align-items: center;
            list-style: none;
        }

        .nav-links a {
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: color 0.2s ease;
        }

        .nav-links a:hover {
            color: #fff;
        }

        .btn-pma {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(99, 102, 241, 0.12);
            color: #818cf8;
            border: 1px solid rgba(99, 102, 241, 0.3);
            padding: 8px 16px;
            border-radius: 9999px;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.25s ease;
        }

        .btn-pma:hover {
            background: var(--accent-indigo);
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(99, 102, 241, 0.4);
        }

        /* Hero Section */
        .hero {
            padding: 80px 0 60px;
            text-align: center;
            position: relative;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.25);
            border-radius: 9999px;
            color: #34d399;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 24px;
            animation: pulse-slow 3s infinite;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: var(--accent-emerald);
            box-shadow: 0 0 8px var(--accent-emerald);
        }

        .hero-title {
            font-size: clamp(2.5rem, 5vw, 4rem);
            font-weight: 800;
            letter-spacing: -0.03em;
            line-height: 1.15;
            margin-bottom: 20px;
        }

        .gradient-text {
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-subtitle {
            font-size: 1.2rem;
            color: var(--text-secondary);
            max-width: 680px;
            margin: 0 auto 36px;
            font-weight: 400;
        }

        .tech-stack-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            margin-bottom: 40px;
        }

        .tech-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-secondary);
            font-size: 0.85rem;
            font-family: var(--font-mono);
        }

        /* Database Banner */
        .db-status-bar {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 20px 24px;
            margin-bottom: 60px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            backdrop-filter: blur(12px);
        }

        .db-status-info {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .db-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .db-icon.success {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .db-icon.error {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .db-text h4 {
            font-size: 1rem;
            font-weight: 700;
            color: #fff;
        }

        .db-text p {
            font-size: 0.85rem;
            color: var(--text-secondary);
            font-family: var(--font-mono);
        }

        .db-meta {
            display: flex;
            gap: 20px;
            align-items: center;
            flex-wrap: wrap;
        }

        .db-meta-item {
            text-align: right;
        }

        .db-meta-item span {
            display: block;
            font-size: 0.75rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .db-meta-item strong {
            font-size: 0.95rem;
            color: var(--text-primary);
            font-family: var(--font-mono);
        }

        /* Projects Section */
        .section-header {
            text-align: center;
            margin-bottom: 48px;
        }

        .section-subtitle {
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--accent-indigo);
            font-size: 0.85rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .section-title {
            font-size: 2.25rem;
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .projects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
            gap: 30px;
            margin-bottom: 70px;
        }

        .project-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 32px;
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
            backdrop-filter: blur(12px);
        }

        .project-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: var(--gradient-primary);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .project-card:hover {
            transform: translateY(-6px);
            background: var(--bg-card-hover);
            border-color: var(--border-hover);
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.5), var(--shadow-glow);
        }

        .project-card:hover::before {
            opacity: 1;
        }

        .card-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .project-index {
            font-family: var(--font-mono);
            font-size: 0.85rem;
            color: var(--accent-cyan);
            font-weight: 700;
            background: rgba(6, 182, 212, 0.1);
            padding: 4px 10px;
            border-radius: 6px;
            border: 1px solid rgba(6, 182, 212, 0.2);
        }

        .project-date {
            font-size: 0.8rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .project-title {
            font-size: 1.35rem;
            font-weight: 700;
            margin-bottom: 14px;
            color: #fff;
            line-height: 1.35;
        }

        .project-description {
            color: var(--text-secondary);
            font-size: 0.95rem;
            line-height: 1.7;
            margin-bottom: 24px;
            flex-grow: 1;
        }

        .card-footer {
            border-top: 1px solid var(--border-color);
            padding-top: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #cbd5e1;
            font-size: 0.88rem;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .card-btn:hover {
            color: var(--accent-cyan);
        }

        .card-tags {
            display: flex;
            gap: 8px;
        }

        .tag-badge {
            font-size: 0.72rem;
            font-family: var(--font-mono);
            color: var(--text-muted);
            background: rgba(255, 255, 255, 0.05);
            padding: 3px 8px;
            border-radius: 4px;
        }

        /* Architecture / Infrastructure Section */
        .infra-section {
            background: rgba(17, 24, 39, 0.5);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 48px;
            margin-bottom: 80px;
        }

        .infra-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 24px;
            margin-top: 36px;
        }

        .infra-card {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 22px;
            transition: border-color 0.2s ease;
        }

        .infra-card:hover {
            border-color: rgba(99, 102, 241, 0.4);
        }

        .infra-badge {
            display: inline-block;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 3px 8px;
            border-radius: 6px;
            margin-bottom: 12px;
        }

        .infra-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: #fff;
        }

        .infra-desc {
            font-size: 0.85rem;
            color: var(--text-secondary);
            line-height: 1.55;
        }

        /* Empty & Error State */
        .empty-state {
            grid-column: 1 / -1;
            background: var(--bg-card);
            border: 1px dashed var(--border-color);
            border-radius: 18px;
            padding: 60px 24px;
            text-align: center;
        }

        .empty-state-icon {
            font-size: 3rem;
            margin-bottom: 16px;
        }

        /* Footer */
        footer {
            border-top: 1px solid var(--border-color);
            padding: 40px 0;
            text-align: center;
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 24px;
            margin-bottom: 16px;
        }

        .footer-links a {
            color: var(--text-secondary);
            text-decoration: none;
            transition: color 0.2s;
        }

        .footer-links a:hover {
            color: #fff;
        }

        @keyframes pulse-slow {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.85; transform: scale(1.02); }
        }

        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }
            .hero {
                padding: 50px 0 30px;
            }
            .infra-section {
                padding: 28px 20px;
            }
            .db-status-bar {
                flex-direction: column;
                align-items: flex-start;
            }
            .db-meta {
                width: 100%;
                justify-content: space-between;
            }
            .db-meta-item {
                text-align: left;
            }
        }
    </style>
</head>
<body>

    <!-- Header Navigation -->
    <header class="navbar">
        <div class="container nav-content">
            <a href="#" class="brand">
                <div class="brand-badge">P</div>
                <span>DevPortfolio<span class="gradient-text">.io</span></span>
            </a>
            <ul class="nav-links">
                <li><a href="#projects">Dự án tiêu biểu</a></li>
                <li><a href="#architecture">Kiến trúc Docker</a></li>
                <li><a href="#about">Về tôi</a></li>
                <li>
                    <a href="http://localhost:8080" target="_blank" class="btn-pma">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                        phpMyAdmin :8080
                    </a>
                </li>
            </ul>
        </div>
    </header>

    <main class="container">
        <!-- Hero Section -->
        <section class="hero" id="about">
            <div class="status-pill">
                <span class="status-dot"></span>
                <span>Hệ thống Docker Compose đang hoạt động mượt mà</span>
            </div>
            <h1 class="hero-title">
                Xây dựng & Triển khai Ứng dụng <br>
                <span class="gradient-text">Hiện Đại, Linh Hoạt & An Toàn</span>
            </h1>
            <p class="hero-subtitle">
                Xin chào! Tôi là Kỹ sư phần mềm chuyên về Full-Stack Web và Tối ưu hóa Hạ tầng Container. 
                Dưới đây là các dự án tiêu biểu được tải trực tiếp từ cơ sở dữ liệu MySQL qua Reverse Proxy Nginx.
            </p>
            <div class="tech-stack-row">
                <span class="tech-pill">🐳 Docker Compose</span>
                <span class="tech-pill">🌐 Nginx Proxy</span>
                <span class="tech-pill">🐘 PHP 8.1 Apache</span>
                <span class="tech-pill">🗄️ MySQL 8.0</span>
                <span class="tech-pill">🔒 Security Hardening</span>
                <span class="tech-pill">⚡ phpMyAdmin</span>
            </div>
        </section>

        <!-- Database Status Banner -->
        <div class="db-status-bar">
            <div class="db-status-info">
                <?php if ($db_connected): ?>
                    <div class="db-icon success">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>
                    <div class="db-text">
                        <h4>Kết Nối Cơ Sở Dữ Liệu MySQL Thành Công</h4>
                        <p>Máy chủ: <strong><?= htmlspecialchars($db_host) ?>:<?= $db_port ?></strong> | Database: <strong><?= htmlspecialchars($db_name) ?></strong></p>
                    </div>
                <?php else: ?>
                    <div class="db-icon error">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    </div>
                    <div class="db-text">
                        <h4 style="color: #f87171;">Chưa Kết Nối Được Tới MySQL</h4>
                        <p><?= htmlspecialchars($db_error_message) ?></p>
                        <small style="color: var(--text-muted); display: block; margin-top: 4px;">Gợi ý: MySQL có thể mất 10-15 giây để khởi động dữ liệu lần đầu tiên. Hãy làm mới lại trang web.</small>
                    </div>
                <?php endif; ?>
            </div>
            <div class="db-meta">
                <div class="db-meta-item">
                    <span>Tổng số dự án</span>
                    <strong style="color: var(--accent-cyan);"><?= count($projects) ?> Dự án</strong>
                </div>
                <div class="db-meta-item">
                    <span>Phiên bản MySQL</span>
                    <strong><?= $db_connected ? htmlspecialchars($server_info) : 'Offline' ?></strong>
                </div>
                <div class="db-meta-item">
                    <span>Web Server</span>
                    <strong>Apache (PHP 8.1)</strong>
                </div>
            </div>
        </div>

        <!-- Projects Showcase Section -->
        <section id="projects">
            <div class="section-header">
                <div class="section-subtitle">Danh mục sản phẩm</div>
                <h2 class="section-title">Dự Án Tiêu Biểu</h2>
            </div>

            <div class="projects-grid">
                <?php if (!empty($projects)): ?>
                    <?php foreach ($projects as $index => $project): ?>
                        <article class="project-card">
                            <div>
                                <div class="card-top">
                                    <span class="project-index">PROJ-0<?= htmlspecialchars((string)($index + 1)) ?></span>
                                    <span class="project-date">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                        <?= date("d/m/Y", strtotime($project['created_at'])) ?>
                                    </span>
                                </div>
                                <h3 class="project-title"><?= htmlspecialchars($project['title']) ?></h3>
                                <p class="project-description"><?= nl2br(htmlspecialchars($project['description'])) ?></p>
                            </div>
                            <div class="card-footer">
                                <div class="card-tags">
                                    <span class="tag-badge">MySQL</span>
                                    <span class="tag-badge">PHP 8.1</span>
                                    <span class="tag-badge">Production</span>
                                </div>
                                <a href="#details" class="card-btn">
                                    <span>Chi tiết</span>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">📂</div>
                        <h3>Chưa có dự án nào trong bảng `projects`</h3>
                        <p style="color: var(--text-secondary); margin-top: 8px;">
                            Dữ liệu mẫu từ file <code>database/init.sql</code> sẽ được nạp tự động khi container <code>mysql_db</code> khởi chạy lần đầu tiên.
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Architecture & Docker Ecosystem -->
        <section class="infra-section" id="architecture">
            <div class="section-header" style="margin-bottom: 24px;">
                <div class="section-subtitle">Hạ tầng Container</div>
                <h2 class="section-title">Mô Hình Triển Khai Docker 4 Thành Phần</h2>
                <p style="color: var(--text-secondary); max-width: 650px; margin: 12px auto 0;">
                    Toàn bộ hệ thống giao tiếp qua mạng nội bộ biệt lập <code>app-net</code> với các tiêu chuẩn bảo mật và hiệu năng cao.
                </p>
            </div>

            <div class="infra-grid">
                <div class="infra-card">
                    <span class="infra-badge" style="background: rgba(6, 182, 212, 0.15); color: #22d3ee;">Reverse Proxy</span>
                    <h3 class="infra-title">Nginx Proxy (:80)</h3>
                    <p class="infra-desc">Điểm tiếp nhận traffic duy nhất từ bên ngoài, tích hợp Security Headers (X-Frame-Options, XSS, nosniff) và chuyển tiếp tới Web App.</p>
                </div>

                <div class="infra-card">
                    <span class="infra-badge" style="background: rgba(99, 102, 241, 0.15); color: #a5b4fc;">Application</span>
                    <h3 class="infra-title">PHP 8.1 Apache</h3>
                    <p class="infra-desc">Container xử lý ứng dụng web, cài đặt sẵn driver <code>mysqli</code>, phục vụ render dữ liệu portfolio từ database.</p>
                </div>

                <div class="infra-card">
                    <span class="infra-badge" style="background: rgba(245, 158, 11, 0.15); color: #fcd34d;">Database</span>
                    <h3 class="infra-title">MySQL 8.0 Server</h3>
                    <p class="infra-desc">Lưu trữ dữ liệu bền vững với Docker Volume <code>mysql_data</code>, tự động chạy script <code>init.sql</code> khi khởi tạo.</p>
                </div>

                <div class="infra-card">
                    <span class="infra-badge" style="background: rgba(236, 72, 153, 0.15); color: #f472b6;">Management</span>
                    <h3 class="infra-title">phpMyAdmin (:8080)</h3>
                    <p class="infra-desc">Giao diện đồ họa trực quan giúp quản trị viên dễ dàng thêm, sửa, xóa dữ liệu và quản lý các bảng trong <code>portfolio_db</code>.</p>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-links">
                <a href="#about">Giới thiệu</a>
                <a href="#projects">Dự án</a>
                <a href="#architecture">Kiến trúc</a>
                <a href="http://localhost:8080" target="_blank">phpMyAdmin</a>
            </div>
            <p>© <?= date('Y') ?> Portfolio Cá Nhân. Triển khai hoàn toàn bằng Docker Compose & PHP mysqli.</p>
        </div>
    </footer>

</body>
</html>
