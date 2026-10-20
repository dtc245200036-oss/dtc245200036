-- ============================================================
-- File: database/init.sql
-- Mục đích: Khởi tạo database portfolio_db, bảng projects và dữ liệu mẫu
-- Đảm bảo chuẩn bảng mã utf8mb4_unicode_ci cho tiếng Việt có dấu
-- ============================================================

-- Thiết lập bảng mã utf8mb4 cho session kết nối khi import dữ liệu
SET NAMES 'utf8mb4';
SET CHARACTER SET utf8mb4;

-- Khởi tạo Database nếu chưa tồn tại
CREATE DATABASE IF NOT EXISTS `portfolio_db` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `portfolio_db`;

-- Tạo bảng projects (id, title, description, created_at) với collation utf8mb4_unicode_ci
CREATE TABLE IF NOT EXISTS `projects` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `description` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Xóa dữ liệu cũ nếu có trước khi chèn mới để tránh trùng lặp
DELETE FROM `projects`;

-- Chèn 2 dòng dữ liệu mẫu các dự án tiêu biểu (chuẩn tiếng Việt có dấu)
INSERT INTO `projects` (`title`, `description`, `created_at`) VALUES
(
    'Nền Tảng Thương Mại Điện Tử Đa Kênh (Omnichannel E-Commerce)',
    'Hệ thống thương mại điện tử kiến trúc Microservices hiệu năng cao, tích hợp cổng thanh toán trực tuyến (VNPAY, MoMo), quản lý tồn kho thời gian thực với Redis và tự động hóa quy trình triển khai CI/CD bằng Docker & Kubernetes.',
    '2024-01-15 09:30:00'
),
(
    'Hệ Thống Giám Sát IoT & Nhà Thông Minh (Smart IoT Dashboard)',
    'Nền tảng thu thập và xử lý dữ liệu cảm biến môi trường thời gian thực qua giao thức MQTT/WebSocket. Cung cấp giao diện trực quan hóa thông số nhiệt độ, độ ẩm và hệ thống cảnh báo sớm thông minh đa kênh.',
    '2024-03-20 14:15:00'
);
