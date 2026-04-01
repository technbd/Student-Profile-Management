## Student Management System using PHP + MySQL:

Now I'll create a complete PHP + MySQL student management system with login and profile pages with picture upload.


### Directory Structure:

```
.
student_system/
├── index.php             ← Step 1: Landing page 
├── register.php          ← Step 2: Create account
├── login.php             ← Step 3: Sign in
├── profile.php           ← Step 4: View/edit profile + upload photo
│
├── api/
│   ├── register.php      ← Validates + creates user + student profile
│   ├── login.php         ← Checks credentials, starts session
│   ├── logout.php        ← Destroys session, redirects
│   └── profile.php       ← GET / UPDATE / UPLOAD picture
│
├── config/
│   ├── db.php            ← DB credentials + connection function
│   └── session.php       ← Session boot, requireLogin(), redirect()
│
├── database.sql          ← users + students tables + admin seed
└── nginx-conf/
    ├── student_system.conf   ← Nginx server block (Rocky Linux 8)

```


### User Flow:

```
register.php  →  fill form  →  success overlay shows STU-2026-XXXX
     ↓
login.php  →  username or email + password  →  profile.php
     ↓
profile.php  →  edit info  →  save  /  click camera icon  →  upload photo
```



### File Overview:

- The three front-end pages:
    - index.php - Landing page 
    - register.php - full registration page 
    - login.php - login page 
    - profile.php - student profile page with picture upload 

- Backend API and DB:
    - api/register.php - registration endpoint
    - api/login.php - login endpoint
    - api/logout.php - logout 
    - api/profile.php - get + update student profile, upload picture
    - config/db.php - single DB connection file used everywhere
    - config/session.php - session bootstrap included by every PHP page
- DB schema:
    - database.sql - DB full schema




---
---



## Setup Instructions:

### 1. Requirements:
- MySQL 5.7+ or MariaDB
- PHP 7.4+ with `mysqli` extension
- Apache/Nginx (XAMPP, WAMP, or LAMP)


#### Install MySQL:

```bash
yum module list mysql
yum module enable -y mysql:8.4

yum install mysql-server -y
```


```bash
systemctl start mysqld 
systemctl enable mysqld 
systemctl status mysqld 
```



#### Install Nginx: 

```bash
yum install -y nginx

systemctl restart nginx
```



#### Install PHP from Remi Repository:

```bash
dnf install -y epel-release
dnf install -y https://rpms.remirepo.net/enterprise/remi-release-8.rpm

dnf module reset -y php
dnf module enable -y php:remi-7.4

dnf install -y php php-cli php-common php-mysqlnd php-xml php-mbstring php-gd php-opcache
yum install -y php-fpm
```


```bash
systemctl start php-fpm
systemctl status php-fpm
```



### 2. Database Setup:

_Create database and user:_
```sql
CREATE DATABASE IF NOT EXISTS student_system;

CREATE USER 'student_app'@'localhost' IDENTIFIED BY 'student_app';

GRANT ALL PRIVILEGES ON student_system.* TO 'student_app'@'localhost';

FLUSH PRIVILEGES;
```


_Open phpMyAdmin or MySQL CLI and run:_
```sql
source /path/to/database.sql
```


Or,

```sql
mysql -u student_app -pstudent_app

CREATE DATABASE IF NOT EXISTS student_system;

USE student_system;


-- ── users ────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
    id          INT          AUTO_INCREMENT PRIMARY KEY,
    username    VARCHAR(50)  NOT NULL UNIQUE,
    email       VARCHAR(150) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    role        ENUM('admin','student') NOT NULL DEFAULT 'student',
    is_active   TINYINT(1)   NOT NULL DEFAULT 1,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;


-- ── students ─────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS students (
    id              INT           AUTO_INCREMENT PRIMARY KEY,
    user_id         INT           NOT NULL UNIQUE,
    student_code    VARCHAR(20)   NOT NULL UNIQUE,   -- e.g. STU-2026-0001
    full_name       VARCHAR(100)  NOT NULL,
    email           VARCHAR(150)  NOT NULL,
    phone           VARCHAR(30)   DEFAULT NULL,
    date_of_birth   DATE          DEFAULT NULL,
    gender          ENUM('Male','Female','Other') DEFAULT NULL,
    department      VARCHAR(100)  DEFAULT NULL,
    year_of_study   TINYINT       DEFAULT NULL,
    address         TEXT          DEFAULT NULL,
    profile_picture VARCHAR(255)  NOT NULL DEFAULT 'default.png',
    gpa             DECIMAL(3,2)  DEFAULT NULL,
    enrollment_date DATE          DEFAULT NULL,
    created_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
                                  ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;


-- ── seed: one admin account (password = Admin@1234) ──────────
-- ── To Run create for admin hash password ──────────
-- ── php -r "echo password_hash('Admin@1234', PASSWORD_BCRYPT);"  ──────────

INSERT IGNORE INTO users (username, email, password, role)
VALUES (
    'admin',
    'admin@university.edu',
    '$2y$10$7NgdJiZsRh7bV1894tx1Re5YnyFwuMF5iSEJWtpO0jx/bv55qi9ZS',
    'admin'
);
```



#### DB Connection Test (mysqli):

_Create a file like `db_conn.php`:_
```php
<?php

#$conn = new mysqli("localhost", "root", "", "student_db");
$conn = new mysqli("localhost", "student_app", "student_app", "student_system");

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

echo "✅ Database connected successfully!";
?>
```


_Test from CLI:_
```bash
php db_conn.php

✅ Database connected successfully!
```


_Run in browser:_
```
http://localhost/db_conn.php
```



### 3. Place Files:

_Copy the `student_system/` folder to your web root:_
- XAMPP: `C:/xampp/htdocs/student_system/`
- WAMP: `C:/wamp64/www/student_system/`
- Linux: `/var/www/html/student_system/` or `/usr/share/nginx/html/student_system`



### 4. Create directory and uploads files:

```bash
mkdir -p /usr/share/nginx/html/student_system

cp -r 01.php-mysql/* /usr/share/nginx/html/student_system

chown -R nginx:nginx /usr/share/nginx/html
chmod 777 /usr/share/nginx/html/student_system/uploads
```


### 5. Configure Database:

_Edit `config/db.php`:_
```php

// ── Database configuration ─────────
define('DB_HOST', 'localhost');
define('DB_NAME', 'student_system');
define('DB_USER', 'student_app');        // your MySQL username
define('DB_PASS', 'student_app'); // your MySQL password
define('DB_PORT', 3306);
```



### 6. Configure Nginx:

_Edit/Configure file on `/etc/nginx/nginx.conf`:_
```conf 

# nginx.conf
# For more information on configuration, see:
#   * Official English Documentation: http://nginx.org/en/docs/
#   * Official Russian Documentation: http://nginx.org/ru/docs/

user nginx;
worker_processes auto;
error_log /var/log/nginx/error.log;
pid /run/nginx.pid;

# Load dynamic modules. See /usr/share/doc/nginx/README.dynamic.
include /usr/share/nginx/modules/*.conf;

events {
    worker_connections 1024;
}

http {
    log_format  main  '$remote_addr - $remote_user [$time_local] "$request" '
                      '$status $body_bytes_sent "$http_referer" '
                      '"$http_user_agent" "$http_x_forwarded_for"';

    access_log  /var/log/nginx/access.log  main;

    sendfile            on;
    tcp_nopush          on;
    tcp_nodelay         on;
    keepalive_timeout   65;
    types_hash_max_size 2048;

    include             /etc/nginx/mime.types;
    default_type        application/octet-stream;

    # Load modular configuration files from the /etc/nginx/conf.d directory.
    # See http://nginx.org/en/docs/ngx_core_module.html#include
    # for more information.
    include /etc/nginx/conf.d/*.conf;

    server {
        listen       80 default_server;
        server_name  _;

#        root         /usr/share/nginx/html;
        root         /usr/share/nginx/html/student_system;
        index index.php ;


        # Load configuration files for the default server block.
        include /etc/nginx/default.d/*.conf;

        location / {

        }


    }

}
```


```bash
systemctl restart nginx
```



### 7. Access the App:

#### Demo Credentials:

| Role    | Username  | Password    |
|---------|-----------|-------------|
| Admin   | admin     | admin1234   |


Open: 
```
http://localhost//index.php
```



---
---




## License:
This project is licensed under the MIT License.






