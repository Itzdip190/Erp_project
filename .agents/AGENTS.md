# Permanent Architecture & Development Rules

## Enterprise ERP Architecture Principle
The project follows a strict enterprise architecture (inspired by SAP, Oracle ERP, ERPNext, and Odoo):
- **Shared Backend Core**: All Database, Models, Services, Repositories, Actions, Auth, Permissions, Queues, Jobs, and Base Logic are 100% shared.
- **Independent Web Presentation Layer**: Web Blade Views (`resources/views/*`), Web Routes (`routes/web.php`, `routes/school.php`, etc.), Web CSS/JS (`public/css/*`, `public/js/*`), Web Components (`resources/views/components/*`), Web Layouts (`resources/views/layouts/*`), Web Menus (`app/Support/ModuleRegistry.php`), Web Dashboard.
- **Independent Mobile Presentation Layer**: Mobile API Controllers (`app/Http/Controllers/Api/V1/Mobile/*`), Mobile API Resources (`app/Http/Resources/Api/V1/Mobile/*`), Mobile Routes (`routes/api_v1.php`), Mobile Feature Visibility (`FeatureVisibilityHelper`), and Android Studio Native Project (`apk for erp/`).

---

## Mandated Instruction Execution Modes

Whenever developer instructions are issued, enforce these exact modes:

### Mode 1: "Change Mobile" / "Change Android UI" / "Change Mobile App"
- ONLY modify Mobile Presentation Layer (`app/Http/Controllers/Api/V1/Mobile/*`, `app/Http/Resources/Api/V1/Mobile/*`, or files in `apk for erp/`).
- Web ERP files (`resources/views/*`, `public/css/*`, `public/js/*`, `routes/web.php`, `routes/school.php`, etc.) MUST REMAIN STABLE AND UNTOUCHED.

### Mode 2: "Change Web" / "Change Web ERP" / "Change Desktop UI"
- ONLY modify Web Presentation Layer (`resources/views/*`, `public/css/*`, `public/js/*`, `routes/web.php`, `routes/school.php`, etc.).
- Mobile presentation files (`app/Http/Controllers/Api/V1/Mobile/*`, `apk for erp/*`) MUST REMAIN STABLE AND UNTOUCHED.

### Mode 3: "Apply to both" / "Apply to Web and Mobile"
- Explicitly update both Web ERP presentation and Mobile ERP presentation layers concurrently while ensuring shared business logic remains unified in `app/Services` / `app/Repositories`.

---

## Feature Visibility Rules
Feature visibility controls are managed per channel scope:
- `web`: Web ERP Desktop Admin UI
- `mobile`: Mobile App Native UI
- `student`: Student Portal
- `teacher`: Teacher Portal
- `parent`: Parent Portal
- `staff`: Staff Portal
- `admin`: Admin Portal

Changing visibility on one scope (e.g. `mobile`) MUST NOT affect another scope (e.g. `web`) unless explicitly configured.
