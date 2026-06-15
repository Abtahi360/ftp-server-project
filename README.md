<div align="center">

# FTP Media Content System

### ISP Media Content Portal - Web Technologies Project 04_A

<br/>

<p>
  <img src="https://img.shields.io/badge/PHP-111?style=flat-square&logo=php&logoColor=777BB4" />
  <img src="https://img.shields.io/badge/MySQL-111?style=flat-square&logo=mysql&logoColor=4479A1" />
  <img src="https://img.shields.io/badge/JavaScript-111?style=flat-square&logo=javascript&logoColor=F7DF1E" />
  <img src="https://img.shields.io/badge/HTML5-111?style=flat-square&logo=html5&logoColor=E34F26" />
  <img src="https://img.shields.io/badge/CSS3-111?style=flat-square&logo=css3&logoColor=1572B6" />
  <img src="https://img.shields.io/badge/Apache-111?style=flat-square&logo=apache&logoColor=D22128" />
  <img src="https://img.shields.io/badge/XAMPP-111?style=flat-square&logo=xampp&logoColor=FB7A24" />
  <img src="https://img.shields.io/badge/Git-111?style=flat-square&logo=git&logoColor=F05032" />
  <img src="https://img.shields.io/badge/GitHub-111?style=flat-square&logo=github&logoColor=white" />
</p>

---

## 📌 Short Overview

This project is a full-stack PHP web application that acts as an FTP-style media portal for an ISP. The ISP stores movies, music, software, eBooks, and other files on the platform. Subscribers (called Members) can visit the site and freely browse, search, filter, and download any available content without creating an account.

</div>
Behind the scenes, two types of staff manage the system:

- **Admin** - has full control. Can add or remove Moderators, upload or delete any content, view all requests, and see the dashboard.
- **Moderator** - can upload and delete content, and review or respond to Member requests.

The project was built as a group assignment for the Web Technologies course (CSE 4101), Spring 2025–2026, Section A. Four students each built one part of the system.

---
<div align="center">

## 👥 Team & Task Split

| Student ID | Task | Responsibility |
|---|---|---|
| 23-50434-1 | Task 1 | Authentication, Profile Page, Home Page, Category Navigation |
| 23-50453-1 | Task 2 | Admin - Manage Moderators & Contents (Full CRUD) |
| 23-50637-1 | Task 3 | Moderator - Add/Delete Contents, View & Update Requests |
| 23-50674-1 | Task 4 | Member - Browse, Search, Filter, Request Box, Download |

---

## ✨ Main Features

### 🔐 Authentication (Task 1)
</div>

- Registration for Admin and Moderator accounts only
- Login with session management (`$_SESSION['user_id']`, `['name']`, `['role']`)
- **Remember Me** - secure 30-day auto-login using a random token stored as a SHA-256 hash in the database and an HttpOnly cookie in the browser
- Password hashing with `password_hash()` (bcrypt) and `password_verify()`
- CSRF token on every form - generated in the shared header, verified in every controller
- Profile page - update name, email, profile picture, change password
- Role-aware navbar - Admin, Moderator, and Guest see different links
- Logout - destroys session, clears Remember Me token from DB and cookie

<div align="center">

### ⚙️ Admin Panel (Task 2)
</div>
- Dashboard with live counts: total contents, categories, moderators, pending requests
- Add a new Moderator with full validation and bcrypt password
- Delete a Moderator (role-restricted cannot accidentally delete an Admin)
- Upload media content with server-side file validation (MIME type via `finfo`, extension whitelist, 100 MB size limit)
- View all uploaded content in a table with uploader info
- Delete content - removes both the database record and the physical file
- View all Member requests and update their status
- AJAX endpoint: load moderator list without page reload (`api/admin_moderators.php`)

<div align="center">

### 🛡️ Moderator Panel (Task 3)
</div>

- Upload new media files (same validation as Admin)
- Delete own uploaded content (Admin can delete any content)
- View the full content library in a table
- View all content requests from Members
- Update request status (fulfilled / rejected) via **AJAX**, no page reload (`api/requests_update.php`)
- AJAX JSON response with inline badge update in the table

<div align="center">

### 🌐 Member / Public Area (Task 4)
</div>

- No login required - any visitor is a Member
- Browse content by top-level category (Movies, Music, Software, eBooks, etc.)
- Filter by sub-category (e.g. Movies → Action, Drama)
- **AJAX live search** - results appear as you type, with 350ms debounce (`api/search.php`)
- Download files — PHP streams the file safely, the original file path is never exposed
- Download counter — increments once per real download
- **Content Request Box** — Members submit requests via AJAX form with instant feedback (`api/request_add.php`)

---

<div align="center">

## 🛡️ Security Features

| Feature | How it works |
|---|---|
| SQL Injection Prevention | Every query uses `mysqli_prepare()` with bound parameters — no string-concatenated SQL |
| XSS Prevention | Every output is wrapped in `htmlspecialchars()` |
| CSRF Protection | Random 32-byte token per session, hidden field in every POST form, verified in every controller |
| Secure Passwords | `password_hash(PASSWORD_BCRYPT)` for storage, `password_verify()` for login |
| Remember Me Security | Plain token only in browser cookie. SHA-256 hash stored in DB. Stale cookies deleted immediately |
| File Upload Security | `finfo_file()` detects real MIME type. Extension whitelist check. Max 100 MB enforced |
| Upload Directory Protection | `.htaccess` blocks PHP execution inside `/public/uploads/` |
| Role-Based Access Control | Every protected page checks `$_SESSION['role']` at the top and redirects unauthorized users |

---

## 🛠️ Tech Stack

<p>
  <img src="https://img.shields.io/badge/PHP%208-111?style=flat-square&logo=php&logoColor=777BB4" />
  <img src="https://img.shields.io/badge/MySQL-111?style=flat-square&logo=mysql&logoColor=4479A1" />
  <img src="https://img.shields.io/badge/JavaScript-111?style=flat-square&logo=javascript&logoColor=F7DF1E" />
  <img src="https://img.shields.io/badge/HTML5-111?style=flat-square&logo=html5&logoColor=E34F26" />
  <img src="https://img.shields.io/badge/CSS3-111?style=flat-square&logo=css3&logoColor=1572B6" />
  <img src="https://img.shields.io/badge/Apache-111?style=flat-square&logo=apache&logoColor=D22128" />
  <img src="https://img.shields.io/badge/XAMPP-111?style=flat-square&logo=xampp&logoColor=FB7A24" />
  <img src="https://img.shields.io/badge/Git-111?style=flat-square&logo=git&logoColor=F05032" />
</p>

| Layer | Technology | Notes |
|---|---|---|
| Backend | PHP 8 (Procedural MVC) | No framework - plain PHP |
| Database | MySQL 5.7+ / MariaDB 10+ | All queries use prepared statements |
| Frontend | HTML5 + CSS3 (custom) | No CSS framework |
| JavaScript | Plain JS / XMLHttpRequest | No jQuery, React, or Vue |
| Authentication | PHP Sessions + Cookies | bcrypt + Remember Me token |
| File Detection | PHP `fileinfo` extension | Server-side MIME check |
| Version Control | Git / GitHub | Feature branches + Pull Requests |
| Local Server | XAMPP / Apache | Development environment |

---

## 📖 Usage Guide
</div>

### 🌐 Guest / Member (no login needed)
1. Visit the home page - browse category tabs and featured content cards
2. Click **Browse** to filter content by category and sub-category
3. Click **Search** to find content by title or description (live, as you type)
4. Click **Download** on any card to download the file
5. Click **Request Content** to ask for something not yet in the library

### 🛡️ Moderator
1. Log in → land on the **Moderator Dashboard**
2. Upload new media files with title, description, category, and file
3. Delete files you uploaded yourself
4. Open **Requests** - mark pending ones as Fulfilled or Rejected (updates live without page reload)

### ⚙️ Admin
1. Log in → land on the **Admin Dashboard** with stat cards
2. Click **Refresh** on the moderator quick-list to load it via AJAX
3. Add or remove Moderators
4. Upload, view, or delete any content
5. Manage all content requests from Members

---
<div align="center">

## 🔌 AJAX / JSON API Endpoints

| Method | Endpoint | Auth | Description |
|---|---|---|---|
| `GET` | `api/categories.php` | None | Returns all top-level categories |
| `GET` | `api/admin_moderators.php` | Admin | Returns the full moderator list |
| `POST` | `api/requests_update.php` | Admin / Mod | Updates a request's status |
| `GET` | `api/search.php?q=keyword` | None | Returns matching content items |
| `POST` | `api/request_add.php` | None | Saves a new content request |

All endpoints return `Content-Type: application/json` with a `success` field.
</div>

**Example - `GET api/search.php?q=inception`:**

```json
{
  "success": true,
  "keyword": "inception",
  "count": 1,
  "results": [
    {
      "id": 5,
      "title": "Inception (2010)",
      "file_path": "1779034740_abc123.mkv",
      "category_name": "Movies",
      "download_count": 14
    }
  ]
}
```

---
<div align="center">

## ✅ Verification Checklist
</div>

After installation, check these:

- [ ] Home page loads and category tabs appear via AJAX
- [ ] Browse page shows content with category and sub-category filter tabs
- [ ] Search returns results as you type (live)
- [ ] Request form submits without page reload and shows success message
- [ ] Login works with `admin@ftp.local` / `password`
- [ ] Admin dashboard shows correct stat counts
- [ ] Moderator list loads on dashboard via the Refresh button (AJAX)
- [ ] File upload saves to `public/uploads/contents/` and inserts a DB row
- [ ] Download increments the counter by exactly 1 per click
- [ ] Deleting content removes both the DB row and the physical file
- [ ] Request Fulfill / Reject updates the badge without page reload (AJAX)
- [ ] Logout clears the session and cookie
- [ ] Remember Me auto-logs in on the next browser visit

---
<div align="center">

## 🔮 Future Improvements
</div>

1. **Email verification** after registration, confirm account before it becomes active
2. **Category management UI** - let Admins create, rename, and delete categories from a panel
3. **Content editing** - change title, description, or category without re-uploading the file
4. **Pagination** on content lists. Avoid loading everything at once
5. **File type filter** on the search page filter by video, audio, PDF, etc.
6. **Request history for Members** - show past requests using IP or a local cookie
7. **File preview** - thumbnail or in-browser preview for images and PDFs
8. **Admin analytics** - a simple chart showing downloads over time per category
9. **Two-factor login for Admin** - extra security for the most powerful account
10. **Dark mode toggle** - CSS custom properties are already in place, easy to add

---

<div align="center">

## 📝 Developer Notes
</div>

- **No framework** - plain procedural PHP with a simple MVC pattern. No Laravel, CodeIgniter, or Symfony.
- **No Composer** - no external PHP packages. Only built-in PHP functions.
- **No JS library** - all AJAX uses native `XMLHttpRequest`. No jQuery, React, or Vue.
- All file paths use `__DIR__` instead of relative strings like `'/../'` to avoid path issues on Windows vs Linux.
- PHP sessions are file-based by default. If AJAX feels slow, consider calling `session_write_close()` on pages before serving files.

---
<div align="center">

## 📄 License
</div>

This project was created as an academic group assignment for the Web Technologies course (CSE 4101) at **American International University-Bangladesh (AIUB)**, Spring 2025–2026.

It is shared here for educational reference only. You are welcome to study the code and learn from it. Please do not submit it as your own academic work.

---
<div align="center">

## Conclusion
</div>

This project covers the full life cycle of a real web application from user authentication and role-based access to file uploads, AJAX interactions, and database-driven content management. Each of the four team members built one self-contained module that connects to the shared database, making the whole system work together as one complete platform.

The code is kept simple and readable on purpose so anyone learning PHP, MySQL, and JavaScript can follow it without needing to know a framework. Every security measure CSRF tokens, bcrypt hashing, prepared statements, MIME validation, and HttpOnly cookies is built from scratch using only PHP's built-in tools.

---
<div align="center">

<div align="center">

<p>
  <img src="https://img.shields.io/badge/PHP-111?style=flat-square&logo=php&logoColor=777BB4" />
  <img src="https://img.shields.io/badge/MySQL-111?style=flat-square&logo=mysql&logoColor=4479A1" />
  <img src="https://img.shields.io/badge/JavaScript-111?style=flat-square&logo=javascript&logoColor=F7DF1E" />
  <img src="https://img.shields.io/badge/Apache-111?style=flat-square&logo=apache&logoColor=D22128" />
  <img src="https://img.shields.io/badge/Git-111?style=flat-square&logo=git&logoColor=F05032" />
</p>

**Made with ❤️ by Team FTP — AIUB Web Technologies Spring 2025–2026**

[🔗 Repository](https://github.com/Abtahi360/ftp-server-project) &nbsp;·&nbsp;
[📊 Contributors](https://github.com/Abtahi360/ftp-server-project/graphs/contributors)

</div>
