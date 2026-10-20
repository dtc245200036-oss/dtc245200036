# Dự Án Website Portfolio Cá Nhân, Giám Sát & Quản Lý Log Tập Trung (Docker Compose)

Hệ thống Website Portfolio cá nhân hiện đại chạy trên kiến trúc đa container (Multi-Container Architecture) bằng Docker Compose, bao gồm:
- **Web & Reverse Proxy**: PHP 8.1 Apache kết nối MySQL 8.0, Nginx Reverse Proxy bảo mật với Security Headers, giao diện phpMyAdmin.
- **Giám sát Tài nguyên (Metrics Monitoring)**: Prometheus, Grafana và cAdvisor.
- **Quản lý Log Tập trung (Centralized Logging)**: Grafana Loki và Promtail.

---

## 📁 Cấu Trúc Thư Mục Dự Án

```
portfolio-project/
├── app/
│   ├── .dockerignore        # Bỏ qua các file không cần thiết khi build Docker
│   ├── Dockerfile           # Base image php:8.1-apache, cài đặt extension mysqli
│   └── index.php            # Giao diện Portfolio hiện đại, hiển thị dự án
├── database/
│   └── init.sql             # Khởi tạo DB portfolio_db, bảng projects (utf8mb4)
├── nginx/
│   └── default.conf         # Nginx Reverse Proxy (trỏ về web_app:80) & Security Headers
├── prometheus/
│   └── prometheus.yml       # Cấu hình Scrape metrics từ Prometheus và cAdvisor
├── grafana/
│   └── provisioning/
│       └── datasources/
│           └── datasource.yml # Tự động kết nối Grafana với Prometheus & Loki
├── loki/
│   └── loki-config.yml       # Cấu hình Loki thu thập log tập trung
├── promtail/
│   └── promtail-config.yml   # Cấu hình Promtail đọc log container và đẩy về Loki
├── loki-config.yml          # Bản copy cấu hình Loki ở thư mục gốc
├── promtail-config.yml      # Bản copy cấu hình Promtail ở thư mục gốc
├── docker-compose.yaml      # Quản lý 9 container trên chung mạng app-net
└── README.md                # Tài liệu hướng dẫn sử dụng chi tiết
```

---

## 🚀 Hướng Dẫn Khởi Chạy

### 1. Yêu cầu môi trường
- Đã cài đặt [Docker](https://www.docker.com/) và Docker Compose (hoặc Docker Desktop).

### 2. Khởi chạy toàn bộ hệ thống
Mở terminal tại thư mục gốc của dự án (`portfolio-project/`) và chạy lệnh:

```bash
docker compose up -d --build
```

Lệnh trên sẽ tự động:
1. Tạo Docker Network nội bộ `app-net`.
2. Tạo các Volume bền vững: `mysql_data`, `prometheus_data`, `grafana_data`, `loki_data`.
3. Khởi chạy ứng dụng Web PHP 8.1 và MySQL 8.0.
4. Kích hoạt Nginx Reverse Proxy và phpMyAdmin.
5. Khởi chạy bộ ba Giám sát Tài nguyên: **cAdvisor**, **Prometheus**, **Grafana**.
6. Khởi chạy bộ đôi Quản lý Log tập trung: **Loki** và **Promtail**.

---

## 🌐 Địa Chỉ Truy Cập Các Dịch Vụ

| Dịch vụ | Địa chỉ URL | Ghi chú & Tài khoản |
| :--- | :--- | :--- |
| **Website Portfolio (Nginx Proxy)** | [http://localhost](http://localhost) | Cổng 80, đi qua Nginx Reverse Proxy và kiểm tra Security Headers |
| **Giao diện Quản trị phpMyAdmin** | [http://localhost:8080](http://localhost:8080) | Quản lý database:<br>- **User**: `portfolio_user` (Pass: `portfolio_pass`) hoặc `root` (Pass: `rootpassword`)<br>- **Server**: `mysql_db` |
| **Grafana Dashboard** | [http://localhost:3000](http://localhost:3000) | Giám sát trực quan hóa:<br>- **User**: `admin`<br>- **Password**: `admin`<br>- Đã cấu hình sẵn cả **Prometheus** và **Loki** |
| **Loki Log Engine** | [http://localhost:3100](http://localhost:3100) | Endpoint nhận và truy vấn logs tập trung (VD: `/ready`, `/loki/api/v1/labels`) |
| **Prometheus Web UI** | [http://localhost:9090](http://localhost:9090) | Truy vấn chỉ số Metrics, xem Targets và Graphs |
| **cAdvisor Metrics UI** | [http://localhost:8081](http://localhost:8081) | Xem thông số CPU, RAM, Disk, Network của từng container |

---

## 📊 Xem Logs Trong Grafana (Loki)

1. Truy cập Grafana tại [http://localhost:3000](http://localhost:3000), đăng nhập với `admin` / `admin`.
2. Vào menu **Explore** (biểu tượng la bàn ở thanh bên trái).
3. Chọn Data source: **Loki**.
4. Chọn nhãn (label) `job` = `docker-containers` và bấm **Run query**.
5. Toàn bộ log thời gian thực của các container (`web_app`, `mysql_db`, `nginx_proxy`, v.v.) sẽ được hiển thị trực quan.

---

## 🔒 Kiểm Tra Security Headers Của Nginx

Để kiểm tra các Security Headers đã được cấu hình trong `nginx/default.conf`, bạn có thể chạy lệnh:

```bash
curl -I http://localhost
```

---

## 🛠️ Một Số Lệnh Quản Trị Hữu Ích

- **Kiểm tra trạng thái 9 container**:
  ```bash
  docker compose ps
  ```

- **Xem logs của các container thời gian thực**:
  ```bash
  docker compose logs -f
  ```

- **Xem logs riêng của Loki và Promtail**:
  ```bash
  docker compose logs -f loki promtail
  ```

- **Tạm dừng hệ thống**:
  ```bash
  docker compose down
  ```

- **Xóa toàn bộ hệ thống kèm cả volume database, monitoring & logs (để reset hoàn toàn)**:
  ```bash
  docker compose down -v
  ```
