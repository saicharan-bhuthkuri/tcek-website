# 🚀 GoDaddy cPanel Deployment & Architecture Guide for TCEK

This guide details the complete PHP & MySQL architecture implemented for **Trinity College of Engineering & Technology (TCEK)** on GoDaddy Shared Hosting / cPanel.

---

## 🏛️ Architecture Overview

```
GoDaddy Server (public_html/)
│
├── index.php, gallery.php, events.php (Public Frontend Pages)
│
├── backend/
│   ├── config/database.php      (PDO MySQL Connection)
│   ├── auth.php                 (Session Security, Roles & Activity Logger)
│   ├── upload.php               (File Storage Engine & MySQL Indexer)
│   ├── crud.php                 (Master CRUD Controller for all 6 tables)
│   └── database.sql             (Schema & Seed Data)
│
├── uploads/                     (GoDaddy Server Storage for Raw Files)
│   ├── .htaccess                (Security: Blocks Execution of PHP Scripts)
│   ├── images/                  (Campus Photos, Event Posters, Faculty Portraits)
│   ├── pdfs/                    (Exam Schedules, Academic Circulars)
│   ├── documents/               (DOCX / DOC Guidelines & Application Forms)
│   └── videos/                  (Campus Life, Events, Freshers Aarambh 2K26)
│
└── admin/                       (Administrative Control Center)
    ├── login.php                (Admin Login with Password Toggle)
    ├── dashboard.php            (All 6 Modules + Activity Audit Trail)
    ├── logout.php               (Secure Session Destruction)
    ├── css/admin.css            (Responsive Dashboard Styling)
    └── js/admin.js              (Drag-and-Drop, Filters, Copy-Link Tools)
```

---

## 🗄️ Database Tables Schema

The database `tcek` contains all 6 required tables:

1. **`users`**
   - Admin users & Regular staff accounts (`username`, `password` hash, `full_name`, `email`, `role`, `is_active`).
   - Default Admins seeded: `tcek` and `Charan` (Password: `tcek@developer`).

2. **`gallery`**
   - Media showcase (`title`, `media_type` ['image','video'], `category` ['events','campus','milestones','press'], `file_path`, `video_url`, `description`).

3. **`events`**
   - College fests & schedules (`title`, `event_date`, `venue`, `description`, `image_path`, `video_path`, `is_featured`).
   - Pre-seeded with **Freshers Aarambh 2K26** and **College Sports Week 2026**.

4. **`notifications`**
   - Official alerts with multi-format attachments (`title`, `category`, `description`, `attachment_type` ['pdf','image','docx','none'], `attachment_path`, `link_url`, `is_marquee`, `publish_date`).
   - **`is_marquee` (Scrolling Marquee Text)**: Flagged notifications dynamically scroll across the breaking news ticker on [index.php](public/index.php)!

5. **`staff`**
   - Faculty & administrative directory (`full_name`, `designation`, `department`, `qualification`, `email`, `phone`, `profile_image`, `bio`, `display_order`).

6. **`activity_logs`**
   - Audit trail capturing **who changed what and when**:
     - `admin_name`: User who performed action.
     - `action`: `Added`, `Updated`, `Deleted`, `Uploaded`, `Login`.
     - `module`: `Events`, `Gallery`, `Notifications`, `Staff`, `Users`, `Uploads`.
     - `record_name`: Identifier (e.g. `Aarambh-2K26`, `Sports Day 2026 Image`).
     - `created_at`: Date & time (`d-m-Y h:i A`).
     - `ip_address`: Remote client IP.
     - `description`: Detailed explanation of the change.
   - **Access Restricted**: Visible only to authorized administrators in the dashboard.

7. **`uploads`**
   - Registry tracking actual files stored on GoDaddy disk (`uploads/images/`, `uploads/pdfs/`, `uploads/documents/`, `uploads/videos/`).

---

## 📋 Activity Logging Format Example

In the Admin Dashboard (**Activity Logs** tab), actions are presented in this exact structured view:

```text
Admin: Charan
Action: Updated
Module: Events
Record: Aarambh-2K26
Date/Time: 08-10-2026 07:30 PM
```

```text
Admin: Admin2
Action: Deleted
Module: Gallery
Record: Sports Day 2026 Image
Date/Time: 08-10-2026 07:45 PM
```

---

## 📋 Step-by-Step GoDaddy cPanel Setup

### Step 1: Create Database in cPanel
1. Go to **cPanel &rarr; MySQL Databases**.
2. **Create New Database**: `tcek`
3. **Create Database User**: `tcek` with password `tcek@developer`
4. **Add User to Database**: Select user `tcek` and database `tcek`, check **ALL PRIVILEGES**, and click **Make Changes**.

### Step 2: Import Database Tables
- **Via phpMyAdmin**: Select `tcek` &rarr; Click **Import** &rarr; Upload [`database.sql`](file:///c:/Users/bhuth/OneDrive/Desktop/tcek.in-php-main/database.sql) &rarr; Click **Go**.
- **Or via Admin Dashboard**: Log in at `https://yourdomain.com/admin/login.php` &rarr; Go to **Database & Status** tab &rarr; Click **"Run / Verify Tables"**.

### Step 3: Upload Website
Upload the contents of `public/` into GoDaddy's `public_html/`.

---

## 🛡️ Default Login Credentials

- **URL**: `https://yourdomain.com/admin/login.php` (or click "Staff Login" in footer)
- **Username**: `tcek` or `Charan`
- **Password**: `tcek@developer`
