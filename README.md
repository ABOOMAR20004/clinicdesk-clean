# ClinicDesk

ClinicDesk is a private PHP/MySQL clinic management dashboard built for the SDEV 2106 / WDMM 2010 / MOBC 2102 final project.

## Stack

- PHP 8+
- MySQL / MariaDB
- mysqli prepared statements
- AdminLTE 3.2.0 loaded locally from `public/assets/adminlte`

## AdminLTE 3.2.0 Integration

- The AdminLTE runtime was copied from the provided `AdminLTE-3.2.0` package.
- Only required runtime files are kept in `public/assets/adminlte`: `dist`, selected `plugins`, and `LICENSE`.
- Development/demo files such as AdminLTE `build`, `docs`, `pages`, sample dashboards, npm configs, and GitHub metadata were removed from the project copy.
- The template is split into reusable PHP partials: `header.php`, `navbar.php`, `sidebar.php`, `content_header.php`, `footer.php`, `alerts.php`, and `pagination.php`.
- AdminLTE components used across the project include login card, navbar, sidebar, content wrapper, breadcrumbs, cards, small boxes, info boxes, badges, custom file inputs, DataTables, and Chart.js.

## Quick Run With XAMPP

1. Install or open XAMPP.
2. Double-click `run_project.bat`.
3. Open the URL shown in the terminal, usually `http://127.0.0.1:8000/index.php`.

The run script starts MySQL when needed, creates `config/database.php` from the example file when missing, imports `database/clinicdesk_db.sql` if the database is not ready, and starts PHP's local server.

## Manual Setup

1. Copy the `clinicdesk` folder into your Apache/PHP web root.
2. Start MySQL from XAMPP.
3. Import `database/clinicdesk_db.sql` into MySQL.
4. Copy `config/database.example.php` to `config/database.php`.
5. Edit `config/database.php` to match your local database credentials.
6. Open `http://localhost/clinicdesk/index.php`.

## Demo Accounts

| Role | Email | Password |
|---|---|---|
| Admin | `admin@clinic.local` | `Admin@1234` |
| Doctor | `lina@clinic.local` | `Doctor@1234` |
| Doctor | `omar@clinic.local` | `Doctor@1234` |
| Patient | `maya@clinic.local` | `Patient@1234` |
| Patient | `yousef@clinic.local` | `Patient@1234` |

## Implemented Requirements

- Login wall with one shared login page for all roles.
- Account creation is handled by the admin inside the dashboard.
- Role-based access through `Auth::requireRole()` in controllers and views.
- `Database` singleton with mysqli prepared statements.
- Models for users, doctors, specializations, appointments, and prescriptions.
- CSRF protection for every POST form.
- Admin user management, doctor management, specialization CRUD, appointment oversight, and CSV reports.
- Patient appointment booking with conflict checks, past-date validation, and doctor availability validation.
- Doctor schedule, status transitions, notes, completed appointment prescriptions, and PDF upload validation.
- Secure prescription file serving through PHP after ownership checks.
- AdminLTE partials for header, navbar, sidebar, content header with breadcrumbs, footer, alerts, and pagination.
- Dashboard statistics for admin, doctor, and patient roles.
- `.gitignore` excludes `config/database.php` and uploaded files; upload folders keep only their security placeholders in the clean distribution.

## Upload Security

- Avatars and doctor photos are validated with `getimagesize()`.
- Prescription PDFs are validated with `finfo_file()`.
- `public/uploads/prescriptions/.htaccess` blocks direct browser access.
- Upload folders contain `index.php` files to prevent directory browsing.
