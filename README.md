# NEXERA &mdash; College Management System

NEXERA is a modern web application that streamlines student, parent, and staff collaboration for small colleges. It is designed for XAMPP (Apache + PHP + MySQL) and pairs a neon-glow dark UI with secure role-based workflows.

---

## ✨ Highlights
- Responsive dark glassmorphism interface with electric blue / neon purple accents
- Distinct dashboards for Students, Parents, Teaching Staff, and Non-Teaching Staff
- Secure authentication with PDO, prepared statements, session hardening, CSRF tokens, and password hashing
- Modules for attendance analytics, internal assessments, placements, tasks, fee management, and document vault
- Progressive enhancement: optional GIF backgrounds and animations that gracefully downgrade

---

## 🗂️ Project Structure
```
NEXERA/
├── index.php                     # Landing page
├── login.php                     # Multi-role login
├── change_password.php           # First-login password reset
├── dashboard_*.php               # Role dashboards
├── secure_download.php           # Protected file delivery
├── register_student.php          # Staff registration entry
│
├── config/
│   ├── config.php                # PDO connection helper
│   └── constants.php             # Global constants (roles, sessions, etc.)
│
├── controllers/                  # Application logic
├── models/                       # Database models (PDO)
├── includes/                     # Sessions, helpers, csrf, logger
├── views/
│   ├── layout/                   # Header & footer
│   └── components/               # Reusable snippets
│
├── css/style.css                 # Global styling
├── js/                           # Front-end behavior
│
├── assets/                       # Placeholder GIFs, asset notes
├── uploads/                      # Secure document storage (kept empty)
│
├── schema.sql                    # Database schema
├── seed.sql                      # Sample data
└── README.md                     # You are here
```

---

## ⚙️ Prerequisites
- **XAMPP** (Apache + PHP 8+ + MySQL 5.7/8.x) on Windows
- Enable `openssl` in `php.ini` to keep `password_hash` working (enabled by default)
- Browser with ES module support (Edge/Chrome/Firefox/Safari)

---

## 🚀 Setup Guide (XAMPP)
1. **Clone / copy** the `NEXERA` folder into `C:\xampp\htdocs\`.
2. **Start services**: open XAMPP Control Panel and run **Apache** and **MySQL**.
3. **Create database**:
   - Visit `http://localhost/phpmyadmin`.
   - Click **Import** and select `schema.sql`. Execute.
   - Click **Import** again and select `seed.sql`. Execute to load demo data.
4. **Configure database credentials** (if not using defaults):
   - Open `config/config.php`.
   - Update `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` to match your MySQL setup.
5. **Set folder permissions** (Windows usually OK):
   - Ensure `uploads/` is writable for document uploads.
6. **Access the application**: open `http://localhost/NEXERA/` in your browser.

---

## 🔐 Sample Credentials
| Role                 | Username       | Password       |
|----------------------|----------------|----------------|
| Teaching Staff       | `teach.hero`   | `Teach@123`    |
| Non-Teaching Staff   | `ops.manager`  | `NonTeach@123` |
| Student              | `STU001`       | `2004-06-15` *(DOB format - must change on first login)* |
| Parent               | `par.stu001`   | `Parent@123`   |
| Admin (reference)    | `admin.nexera` | `Admin@123`    |

> **Note**: After importing `seed.sql`, if you get "incorrect username or password" errors, make sure:
> 1. The database was created and both `schema.sql` and `seed.sql` were imported successfully
> 2. You're using the exact usernames and passwords from the table above
> 3. For staff login: Click "Staff" first, then select "Teaching Staff" or "Non-Teaching Staff" to see the login form

> Students seeded with their DOB as initial password are flagged for a mandatory password change on first login.

---

## 📁 Assets & Theming
- Replace placeholder GIFs in `assets/` with high-quality variants (same filenames to avoid code changes).
- Update `css/style.css` to tweak colors, fonts, or glass effects (look for the `:root` CSS variables).
- Logo swap: Replace `.navbar-brand` text in `views/layout/header.php` or use an `<img>`.

---

## 🔄 Workflows by Role
- **Student**: Edit profile, view analytics, apply for leave, download study material, track placements, manage tasks, check events, view fees, use placeholder tracker.
- **Parent**: Review child profile, attendance summary, internal marks, leave status, placements, and access the “Developing” tracker modal.
- **Teaching Staff**: Update attendance, internal marks, approve leaves, assign tasks, view placement updates, manage study materials, and review mentorship notes.
- **Non-Teaching Staff**: Register students/parents, update fees, and handle operational notices.

All sensitive actions use CSRF tokens, server-side validation, and audit logging.

---

## 🛡️ Security Checklist & Hardening Tips
- Change default credentials immediately; enforce complex password policy in production.
- Update `config/config.php` with a strong MySQL user/password and limit DB access to localhost.
- Move the `uploads/` directory outside the webroot (update `file_path` storage accordingly) or add `.htaccess` rules to block direct access.
- Configure HTTPS via Apache + SSL for real deployments.
- Consider enabling reCAPTCHA or rate limiting on login to mitigate brute force attacks.
- Review `php.ini` for `session.cookie_secure`, `session.cookie_httponly`, and `session.use_strict_mode` in production.
- Enable server-side logging (e.g., via Monolog) and centralize `audit_logs` review.
- Harden file uploads: validate MIME types, file signatures, and store with random names (already supported via `generate_safe_filename()` helper).

---

## 🧪 Testing Suggestions
- Verify all login flows (including incorrect credentials and forced password reset).
- Submit leave requests as student; approve/reject as teaching staff.
- Register new student/parent as non-teaching staff and confirm linked views.
- Upload a document (manually insert record or extend UI) and confirm download is protected.
- Run through the dashboards on desktop, tablet, and mobile to validate responsiveness.
- Use browser dev tools Lighthouse/Accessibility audit for further tuning.

---

## 🛠️ Extending NEXERA
- **REST API**: Wrap controllers with a routing layer (Slim, FastRoute) to expose JSON endpoints.
- **Role Enhancements**: Add granular permissions to `users` table or introduce additional roles.
- **Notifications**: Integrate email/SMS for leave approvals or placement alerts.
- **Analytics**: Expand Chart.js dashboards with more datasets (attendance trends, marks timeline).
- **Mobile App**: Reuse models/controllers to provide JSON + JWT for mobile clients.

---

## 🤝 Contributing
1. Fork & create feature branches (`feat/<feature-name>`).
2. Follow PSR-12 for PHP and lint CSS/JS before commits.
3. Document changes in `README.md` or `docs/`.

---

## 📄 License
Provide licensing terms here (MIT, proprietary, etc.). Default: All rights reserved unless specified.

---

Crafted with 💡 by GPT-5 Codex for a connected and future-ready campus.

