
# 🛡️ Crime Reporting System (PHP & MySQL)

A fully functional **Crime Reporting & Management System** built using **PHP**, **MySQL**, **HTML**, **CSS**, and **JavaScript**.  
This system enables citizens to file crime complaints online and allows police officials to manage, assign, and track complaints effectively.  
Includes **Email Notification System (PHPMailer)** for OTP, complaint updates, and alerts.

---

## 🚀 Features

### 👤 Citizen Features
- OTP‑based user registration  
- Login / Logout  
- File crime complaints  
- View complaint history  
- Track complaint status  

### 👮 Police / Incharge Features
- View assigned complaints  
- Update complaint status  
- Add police stations & officers  
- Forward complaints  

### 🏢 Admin / Head Office
- Manage stations & officers  
- Monitor all complaints  
- Dashboard insights  

### 🔐 Security & System Features
- OTP using PHPMailer  
- Password hashing  
- SQL Injection protection  
- Role‑based access  

---

# ✉️ Email Notification System (PHPMailer)

Email alerts are triggered automatically for:

### 📌 Email Triggers
- ✔️ OTP for registration  
- ✔️ Complaint successfully registered  
- ✔️ Complaint status updated  
- ✔️ Password reset requests  

### 📌 SMTP Configuration  
Manage all values via `.env`:

```
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_FROM=your-email@gmail.com
MAIL_FROM_NAME="Crime Reporting System"
```

### 📌 Email Helper (MailHelper.php)

```
src/helpers/MailHelper.php
```

```php
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../vendor/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../../vendor/PHPMailer/src/SMTP.php';
require_once __DIR__ . '/../../vendor/PHPMailer/src/Exception.php';

class MailHelper {

    public static function sendMail($to, $subject, $body) {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = $_ENV['MAIL_HOST'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['MAIL_USERNAME'];
            $mail->Password   = $_ENV['MAIL_PASSWORD'];
            $mail->SMTPSecure = 'tls';
            $mail->Port       = $_ENV['MAIL_PORT'];

            $mail->setFrom($_ENV['MAIL_FROM'], $_ENV['MAIL_FROM_NAME']);
            $mail->addAddress($to);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            return $mail->send();
        } catch (Exception $e) {
            return false;
        }
    }
}
?>
```

### 📌 Example Trigger: Complaint Registered

```php
MailHelper::sendMail(
    $user_email,
    "Complaint Registered Successfully",
    "<p>Your complaint has been registered.</p>
     <p><strong>Complaint ID:</strong> $complaint_id</p>"
);
```

---

# 📂 Project Structure

```
Crime-Reporting-System/
│
├── public/
│   ├── css/
│   ├── js/
│   ├── images/
│   ├── login.php
│   ├── register.php
│   ├── dashboards...
│
├── src/
│   ├── controllers/
│   ├── models/
│   └── helpers/
│
├── config/
│   ├── db.php
│   └── mail_config.php
│
├── vendor/                  # PHPMailer
├── migrations/              # crime_portal.sql
├── docs/                    # documentation
└── README.md
```

---

# ⚙️ Installation & Setup

### 1️⃣ Clone repository
```bash
git clone https://github.com/Likhith011/Crime-Reporting-System.git
cd Crime-Reporting-System
```

### 2️⃣ Move to Web Server Root

Linux (Apache):
```bash
sudo cp -r Crime-Reporting-System /var/www/html/
```

XAMPP (Windows):
```
C:/xampp/htdocs/Crime-Reporting-System/
```

---

### 3️⃣ Import Database

1. Open phpMyAdmin  
2. Create database:
```
crime_portal
```
3. Import:
```
migrations/crime_portal.sql
```

---

### 4️⃣ Create `.env`

```
DB_HOST=localhost
DB_NAME=crime_portal
DB_USER=root
DB_PASS=

MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_FROM=your-email@gmail.com
MAIL_FROM_NAME="Crime Reporting System"
```

---

### 5️⃣ Run Project

Linux:
```bash
sudo systemctl restart apache2
sudo systemctl restart mysql
```

Run with PHP server:
```bash
php -S localhost:8000 -t public/
```

Open:
👉 http://localhost/Crime-Reporting-System/

---

# 🤝 Contributing

- Follow MVC structure  
- Use prepared statements  
- Keep UI inside `/public`  
- Document new endpoints in `/docs`  

---

# 📄 License  
MIT License

---

# 👤 Author  
**Likhith Eshwaraiah**  
GitHub: https://github.com/Likhith011
