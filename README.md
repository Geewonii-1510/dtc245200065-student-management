# Hệ Thống Quản Lý Sinh Viên (SIS) - Đề 2

Hệ thống Quản lý Sinh viên triển khai bằng Docker Compose:
- **Ứng dụng:** Web App Quản lý sinh viên (PHP/Apache) kết nối MariaDB.
- **Cơ sở dữ liệu:** MariaDB 11 và phpMyAdmin (chỉ bind 127.0.0.1).
- **Reverse Proxy:** Nginx (HTTPS tự ký cổng 8443, Security Headers).
- **Giám sát:** Prometheus, cAdvisor, Node-exporter, Nginx-exporter, Grafana.
- **Quản trị Log:** Loki, Promtail và truy vấn bằng LogQL.

## Hướng dẫn chạy
1. Tạo chứng chỉ tự ký trong thư mục nginx/certs/
2. Cấu hình biến môi trường: `cp .env.example .env`
3. Khởi chạy: `docker compose up -d`
4. Kiểm tra trạng thái: `docker compose ps`
