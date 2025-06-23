# ВЕАТА - Интернет-магазин строительных материалов (PHP версия)

## Описание
Онлайн-магазин строительных материалов с полным функционалом для управления товарами, заказами и пользователями. Построен на PHP и MySQL.

## Технологии
- Frontend: HTML5, CSS3, JavaScript, Bootstrap 5
- Backend: PHP 8.1+
- Database: MySQL 8.0+
- Authentication: PHP Sessions
- Template Engine: Twig
- CSS Framework: Bootstrap 5
- JavaScript Framework: Alpine.js

## Установка и запуск

### Требования
- PHP 8.1 или выше
- MySQL 8.0 или выше
- Apache/Nginx веб-сервер
- Composer (менеджер зависимостей PHP)
- Git

### Установка зависимостей
```bash
# Клонирование репозитория
git clone <repository-url>
cd veata-shop-php

# Установка зависимостей через Composer
composer install
```

### Настройка базы данных
1. Создайте базу данных MySQL:
```sql
CREATE DATABASE veata_shop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Импортируйте структуру базы данных:
```bash
mysql -u your_username -p veata_shop < database/schema.sql
```

### Настройка окружения
1. Создайте файл `.env` в корневой директории:
```env
DB_HOST=localhost
DB_NAME=veata_shop
DB_USER=your_db_user
DB_PASS=your_db_password

SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=your_email@gmail.com
SMTP_PASS=your_email_password

APP_URL=http://localhost
APP_ENV=production
APP_DEBUG=false
```

2. Настройте права доступа:
```bash
chmod 755 -R public/
chmod 777 -R storage/
```

### Настройка веб-сервера

#### Apache (.htaccess)
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    RewriteRule ^index\.php$ - [L]
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule . /index.php [L]
</IfModule>
```

#### Nginx
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/veata-shop-php/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

## Структура проекта
```
veata-shop-php/
├── app/                    # Основной код приложения
│   ├── Controllers/       # Контроллеры
│   ├── Models/           # Модели
│   ├── Services/         # Сервисы
│   └── Helpers/          # Вспомогательные функции
├── config/                # Конфигурационные файлы
├── database/             # Миграции и сиды
├── public/               # Публичная директория
│   ├── assets/          # CSS, JS, изображения
│   └── index.php        # Точка входа
├── resources/            # Ресурсы
│   ├── views/           # Шаблоны
│   └── lang/            # Локализация
├── storage/              # Загрузки и кэш
├── tests/                # Тесты
├── vendor/               # Зависимости Composer
├── .env                  # Переменные окружения
├── composer.json         # Зависимости проекта
└── README.md            # Документация
```

## API Endpoints
- `/api/auth` - Аутентификация
- `/api/products` - Управление товарами
- `/api/orders` - Управление заказами
- `/api/users` - Управление пользователями

## Безопасность
- Защита от SQL-инъекций через PDO
- CSRF защита
- XSS защита
- Валидация входных данных
- Безопасное хранение паролей (password_hash)
- Защита от брутфорса
- Логирование действий

## Оптимизация
- Кэширование запросов
- Оптимизация изображений
- Минификация CSS/JS
- Gzip сжатие
- HTTP/2 поддержка

## Мониторинг
- Логирование ошибок
- Мониторинг производительности
- Анализ безопасности

## Резервное копирование
```bash
#!/bin/bash
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_DIR="/path/to/backups"
mysqldump -u your_username -p veata_shop > "$BACKUP_DIR/veata_shop_$TIMESTAMP.sql"
```

## Поддержка
По всем вопросам обращайтесь: support@mosplitka.ru 