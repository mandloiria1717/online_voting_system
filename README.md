# 🗳️ Online Voting System
**A complete PHP + MySQL voting platform for college submissions**

---

## 📁 Folder Structure

```
voting_system/
├── index.php                  ← Voter login/register page
├── database.sql               ← Database setup script
├── assets/
│   ├── css/style.css          ← Main stylesheet
│   └── js/main.js             ← Client-side JavaScript
├── includes/
│   ├── config.php             ← Database connection
│   └── auth.php               ← Auth helpers
├── voter/
│   ├── dashboard.php          ← Voter home
│   ├── vote.php               ← Cast vote page
│   ├── vote_success.php       ← Vote confirmation
│   ├── results.php            ← Live results
│   └── logout.php
└── admin/
    ├── login.php              ← Admin login
    ├── dashboard.php          ← Admin home
    ├── candidates.php         ← Manage candidates
    ├── voters.php             ← View all voters
    ├── results.php            ← Admin results view
    └── logout.php
```

---

## ⚡ How to Run (XAMPP/WAMP)

### Step 1 — Install XAMPP
Download from https://www.apachefriends.org/ and install.

### Step 2 — Copy Project Files
Copy the `voting_system` folder to:
- **XAMPP**: `C:\xampp\htdocs\voting_system`
- **WAMP**: `C:\wamp64\www\voting_system`

### Step 3 — Start Servers
Open XAMPP Control Panel and click **Start** for:
- ✅ Apache
- ✅ MySQL

### Step 4 — Create Database
1. Open your browser → go to `http://localhost/phpmyadmin`
2. Click **"New"** on the left sidebar
3. Create a database named: `voting_system`
4. Click the database → go to **SQL tab**
5. Paste the contents of `database.sql` and click **Go**

### Step 5 — Configure Database (if needed)
Open `includes/config.php` and update if your MySQL credentials differ:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');           // Add password if you set one
define('DB_NAME', 'voting_system');
```

### Step 6 — Open the Application
- **Voter Login**: http://localhost/voting_system/
- **Admin Panel**: http://localhost/voting_system/admin/login.php

---

## 🔑 Default Credentials

| Role  | Username / Email | Password  |
|-------|-----------------|-----------|
| Admin | `admin`         | `admin123` |

---

## ✨ Features Checklist

| Feature | Status |
|---------|--------|
| Voter registration & login | ✅ |
| Admin login (separate) | ✅ |
| Add / delete candidates | ✅ |
| View all voters | ✅ |
| Search & filter voters | ✅ |
| Start / Stop voting | ✅ |
| Cast vote (once only) | ✅ |
| Duplicate vote prevention (DB + session) | ✅ |
| Vote confirmation page + confetti | ✅ |
| Live results with progress bars | ✅ |
| Admin results with winner highlight | ✅ |
| Logout functionality | ✅ |
| SQL injection prevention (prepared statements) | ✅ |
| Client-side + server-side validation | ✅ |
| Session-based authentication | ✅ |
| Responsive modern UI | ✅ |
| CSS animations | ✅ |
| Auto-refresh results (30s) | ✅ |

---

## 🛠 Tech Stack

- **Frontend**: HTML5, CSS3, Vanilla JavaScript
- **Backend**: PHP 7.4+
- **Database**: MySQL via MySQLi
- **Server**: Apache (XAMPP/WAMP)
- **Fonts**: Google Fonts (Sora + JetBrains Mono)

---

## 📝 Notes

- The `votes` table has a `UNIQUE KEY` on `user_id` to enforce one vote per user at the database level.
- Passwords are hashed with `password_hash()` (bcrypt).
- All user inputs are sanitized with `htmlspecialchars()` and validated with prepared statements.
- The admin default password hash is for `admin123` — change it after setup!
