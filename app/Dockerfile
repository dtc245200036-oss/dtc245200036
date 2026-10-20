# ============================================================
# File: app/Dockerfile
# Base image: PHP 8.1 đi kèm Apache Web Server
# ============================================================
FROM php:8.1-apache

# Cài đặt extension mysqli để kết nối MySQL
RUN docker-php-ext-install mysqli \
    && docker-php-ext-enable mysqli

# Kích hoạt module rewrite của Apache
RUN a2enmod rewrite

# Sao chép toàn bộ mã nguồn ứng dụng vào thư mục web root
WORKDIR /var/www/html
COPY . /var/www/html/

# Phân quyền cho người dùng web server Apache (www-data)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expose cổng 80 cho container
EXPOSE 80

# Chạy Apache ở chế độ foreground
CMD ["apache2-foreground"]
