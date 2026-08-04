# DOST-SEI Davao Region Scholarship Records Management System (DOSTorage V1)
## Technical Specifications / System Documentation

**Organization:** University of Southeastern Philippines — College of Information and Computing  
**Department:** Department of Science and Technology - Region XI Office  
**System Version:** 1.0  
**Date:** 2026-07-29  
**Prepared By:** Christian Jhon Ed J. Rosal (Fullstack / AIOps Lead), DOST-SEI OJT Intern  
**Subject:** System documentation for the DOST-SEI Davao Region scholarship records management system, covering functional/non-functional requirements, architecture, database design, technology stack, hardware/software requirements, and deployment details.

---

## Table of Contents

1. [System Overview](#1-system-overview)
2. [Functional Requirements](#2-functional-requirements)
3. [Non-Functional Requirements](#3-non-functional-requirements)
4. [System Architecture](#4-system-architecture)
5. [Database Design and Specifications](#5-database-design-and-specifications)
6. [Technology Stack](#6-technology-stack)
7. [Hardware and Software Requirements](#7-hardware-and-software-requirements)
8. [Integration and Deployment Details](#8-integration-and-deployment-details)
9. [Appendices](#9-appendices)

---

## 1. System Overview

### 1.1 Purpose
DOSTorage V1 is an offline-first web application that digitizes and manages scholarship records and administrative documents for the DOST-SEI Davao Region office. The system replaces physical document filing with a structured, searchable, auditable digital archive.

### 1.2 Scope
V1 covers:
- **Scholar 201 Records:** scholar profiles, status tracking, and document management.
- **Administrative Records:** policy files, memos, program forms, and administrative documents.
- **Document lifecycle:** upload, view, versioned replacement, soft-delete strike-off, restore, audit trail.
- **Search and reporting:** dashboard KPIs, filters by file type/group, export pack generation.
- **Access control:** role-based permissions via Spatie, multi-user concurrent usage.

Out of scope for V1:
- Financial Ledger module.
- Online public portal; V1 is restricted to the DOST RXI internal network.
- Mobile-native app; responsive web access is supported.

### 1.3 Intended Audience
- OJT developers and reviewers.
- DOST RXI Scholarship Section staff.
- USeP OJT coordinators and evaluators.

---

## 2. Functional Requirements

### 2.1 User and Access Management
- Super Admin can create, edit, suspend, and assign roles to users.
- Role-based access control using Spatie permissions.
- Predefined roles: Super Admin, Admin, Staff, Viewer.
- Login/logout with Laravel Breeze/session auth.
- Audit log entries for login events and sensitive actions.

### 2.2 Dashboard
- KPI cards for total scholars, active documents, pending reviews, recent uploads.
- Charts for document volume by type/group, monthly upload trends.
- Quick links to recent items, pending approvals, and strike-off queue.

### 2.3 Scholar Records Management
- Create and maintain scholar profile records linked to DOST-SEI 201 fields.
- Search scholars by name, school, region, scholarship type, status.
- View scholar details with associated documents.
- Mark scholars as cleared, on-hold, or graduated.
- Prevent duplicate scholar profile creation where required.

### 2.4 Administrative Records Management
- Create and manage administrative file metadata separately from scholar records.
- Search administrative records by series/reference number, subject, year, document type.
- Associate supporting files with administrative records.
- Enable quick retrieval by category and date range.

### 2.5 Document and File Management
- Upload files: PDF, PNG, JPG, JPEG; maximum 10 MB each.
- Store files in a private directory outside public webroot; access via controlled download routes.
- Versioned replace: every replacement creates a new `document_versions` row and preserves history.
- Soft delete via strike-off; undo/restore supported.
- Super Admin-only permanent deletion outside normal UI.
- Metadata validation against `file_types.metadata_template` before storage.
- File type taxonomy with `file_groups` and `file_types`.
- Primary searchable metadata key per file type for search indexing.

### 2.6 Duplicate Handling and Conflict Resolution
- Three-way modal when an existing document is replaced:
  - Cancel.
  - Keep history (create new version).
  - Overwrite with confirmation.
- Detect duplicate scholar records where business rules require uniqueness.

### 2.7 Search and Reporting
- Global search with filters for document type, group, date, scholar, status.
- Export pack: generate combined PDF export for selected scholar documents using jsPDF.
- Reports generation: downloadable statistics for monitoring and planning.
- Print-friendly or PDF output for selected records.

### 2.8 System Administration
- Manage file types and file groups taxonomy.
- View audit logs for CRUD actions on key entities.
- Configure application settings and constants.
- Run database health checks, migration status, and rollback procedures.

---

## 3. Non-Functional Requirements

### 3.1 Performance Benchmarks
- Typical page load target: < 2 seconds on local network for loaded lists; < 5 seconds for filtered searches.
- Upload endpoint should accept files up to 50 concurrent MB within 10 seconds on LAN.
- PDF export of 50 pages target: < 15 seconds.
- Search index queries should return results in < 1 second for datasets under 50,000 rows.

### 3.2 Security Requirements
- All traffic is internal-only on the DOST RXI local network.
- Session-based authentication with secure, HttpOnly cookies.
- Password hashing via Laravel native bcrypt/argon2id.
- CSRF protection on all state-changing routes.
- Input validation and sanitization for all user-supplied data.
- File upload whitelist: only PDF, PNG, JPG, JPEG.
- Max upload size enforced at application and server levels: 10 MB.
- Soft deletes; no normal-user hard deletes.
- Audit logging for sensitive actions.
- Permissions enforced on routes and UI actions via policies/gates.

### 3.3 Reliability and Availability
- Data durability via local MySQL with daily logical backups.
- Dockerized services allow quick restore from backup scripts.
- Graceful handling of database connection unavailability with user-friendly error pages.
- JSON metadata validation prevents corrupt template data from persisting.

### 3.4 Usability
- Responsive Bootstrap layout usable on desktop and tablet screens.
- Consistent terminology aligned with DOST-SEI document types.
- Accessible labels, focus states, and error messages.
- Wizard-style upload flow with clear progress indicators and validation messages.

### 3.5 Maintainability
- Code organized by Laravel conventions: Models, Controllers, Livewire components, Policies, Observers.
- Detailed PHPDoc blocks on domain models and complex methods.
- `TEAM_WORKFLOW.md` and `CHANGELOG.md` updated per behavioral or schema change.
- `AGENTIC_CHANGELOG.md` updated per agent/stitch session affecting backend, docs, or CI.

### 3.6 Portability and Compatibility
- Runs on Docker Compose (local dev) and CI pipeline.
- SQLite support for local agent testing; MySQL in production/CI.
- Browser compatibility: latest Chrome, Edge, Firefox.

---

## 4. System Architecture

### 4.1 High-Level Architecture
DOSTorage V1 follows a standard Laravel MVC + Livewire architecture with a Dockerized LAMP-equivalent stack.

```
                    +-----------------------+
                    |   User Browser        |
                    | (Chrome/Edge/Firefox) |
                    +----------+------------+
                               |
                        HTTP over LAN
                               |
              +----------------+----------------+
              |                                 |
      +-------v------+                +--------v------+
      | Nginx/Apache |                |  Vite assets |
      |  :80 / :443  |                +--------------+
      +------+-------+                        |
             |                                |
      +------v-------------------+            |
      | Laravel Application       |            |
      | - Routes                  |            |
      | - Controllers / Livewire  |            |
      | - Models / Policies        |            |
      | - Observers                |            |
      +------+--------------------+            |
             |                                 |
      +------v------------+       +-----------v-------+
      | MySQL              |       | Private Storage   |
      | - scholars         |       | app/storage       |
      | - file_groups      |       | uploads/docs/*    |
      | - file_types       |       +-------------------+
      | - documents        |
      | - document_versions|
      | - audit_logs       |
      +--------------------+
```

### 4.2 Component Breakdown

| Layer | Responsibility | Key Files |
|-------|-----------------|-----------|
| Presentation | Bootstrap 5 UI, Livewire components, responsive layout | `resources/views`, `app/Livewire` |
| Application | Route handling, auth, validation, business logic | `routes/web.php`, `app/Http/Controllers`, `app/Livewire` |
| Domain | Eloquent models, policies, observers, casting | `app/Models`, `app/Policies`, `app/Observers` |
| Persistence | MySQL schema, migrations, seeders | `database/migrations`, `database/seeders` |
| Storage | Local private disk for documents; downloads through controlled routes | `config/filesystems.php`, `storage/app/private` |
| Integration | Optional Google Docs Bible Center integration via Hermes bible_keeper.py | `scripts/bible_keeper.py` |
| CI | GitHub Actions for lint, build, and testing | `.github/workflows` |

### 4.3 Module Interaction Summary
- **Dashboard** reads aggregate counts from `documents`, `document_versions`, `scholars`, and `audit_logs`.
- **Scholar Management** manages `scholars` and related lookups; documents are associated through polymorphic `documents.documentable_id/documentable_type`.
- **Administrative Records** use the same polymorphic `documents` relation but are grouped under `AdministrativeRecord` or equivalent owner record.
- **Upload/Save flow:** validate metadata template → store private file → create/update `documents` row → append `document_versions` row → trigger observer for audit.
- **Search flow:** query `documents` with type/group filters plus one primary metadata key per type.

---

## 5. Database Design and Specifications

### 5.1 ERD Overview
Primary entities:
- `users`
- `scholars`
- `administrative_records`
- `file_groups`
- `file_types`
- `documents`
- `document_versions`
- `audit_logs`

Relationships:
- A `user` has many `documents`, `scholars`, and `audit_logs`.
- A `scholar` has many `documents`.
- An `administrative_record` has many `documents`.
- A `file_type` belongs to a `file_group`.
- A `document` has many `document_versions`.
- `audit_logs` record actor, action, record type, record id, payload snapshot, and timestamp.

### 5.2 Schema Definitions

#### users
- `id` BIGINT PK
- `name` VARCHAR(255)
- `email` VARCHAR(255) UNIQUE
- `email_verified_at` TIMESTAMP NULLABLE
- `password` VARCHAR(255)
- `role` / Spatie roles managed via `model_has_roles`, `roles`, `permissions`
- `created_at`, `updated_at`

#### scholars
- `id` BIGINT PK
- `first_name`, `middle_name`, `last_name`, `suffix` VARCHAR(255)
- `student_id` VARCHAR(255) UNIQUEable
- `school`, `course`, `region`, `scholarship_type` VARCHAR(255)
- `status` ENUM or VARCHAR: active, cleared, on-hold, graduated
- `contact_number`, `email` VARCHAR(255)
- `photo_path` nullable
- `soft_deleted_at` nullable strike-off timestamp
- `created_by`, `updated_by`, `deleted_by` BIGINT FK to `users`
- `created_at`, `updated_at`, `deleted_at`

#### administrative_records
- `id` BIGINT PK
- `title` VARCHAR(255)
- `reference_number` VARCHAR(255) nullable
- `category` VARCHAR(255)
- `date_prepared`, `date_approved` DATE nullable
- `prepared_by`, `received_by` VARCHAR(255) nullable
- `soft_deleted_at` nullable
- audit FKs as applicable
- `created_at`, `updated_at`, `deleted_at`

#### file_groups
- `id` BIGINT PK
- `name` VARCHAR(255) UNIQUE
- `slug` VARCHAR(255) UNIQUE
- `description` TEXT nullable
- `created_at`, `updated_at`

#### file_types
- `id` BIGINT PK
- `file_group_id` BIGINT FK to `file_groups.id`
- `name` VARCHAR(255)
- `allowed_extensions` JSON nullable default `["pdf","png","jpg","jpeg"]`
- `max_size_mb` INT default 10
- `metadata_template` JSON nullable form schema for dynamic metadata fields
- `primary_search_key` VARCHAR(255) nullable
- `is_active` BOOLEAN default true
- `created_at`, `updated_at`

#### documents
- `id` BIGINT PK
- `documentable_id` BIGINT nullable polymorphic owner id
- `documentable_type` VARCHAR(255) nullable polymorphic owner type
- `file_type_id` BIGINT FK to `file_types.id`
- `file_group_id` BIGINT FK to `file_groups.id`
- `filename` VARCHAR(255)
- `filepath` VARCHAR(1024)
- `mime_type` VARCHAR(255)
- `size_bytes` BIGINT
- `metadata` JSON nullable schema-defined values only
- `status` VARCHAR(255) nullable active, strike-off, archived
- `strike_off_reason` TEXT nullable
- `last_version_number` INT default 1
- `created_by`, `updated_by` BIGINT FK to `users`
- `created_at`, `updated_at`
- `deleted_at` soft delete

#### document_versions
- `id` BIGINT PK
- `document_id` BIGINT FK to `documents.id`
- `version_number` INT
- `filename`, `filepath`, `mime_type`, `size_bytes` VARCHAR/BIGINT as applicable
- `metadata` JSON snapshot
- `uploaded_by` BIGINT FK to `users`
- `created_at`

#### audit_logs
- `id` BIGINT PK
- `user_id` BIGINT FK to `users.id` nullable for system events
- `action` VARCHAR(255) created, updated, deleted, restored, login, logout, upload, replace, strike-off, permanent-delete
- `record_type` VARCHAR(255)
- `record_id` BIGINT
- `payloads` JSON before/after or metadata summary
- `ip_address` VARCHAR(45) nullable
- `user_agent` TEXT nullable
- `created_at`

### 5.3 Data Dictionary
| Endpoint / field | Meaning | Constraints |
|------------------|---------|-------------|
| `documents.metadata` | Runtime form values; must validate against `file_types.metadata_template` | Hard abort before insert if invalid |
| `document_versions` | Append-only history row | One or more per document |
| `file_types.metadata_template` | JSON schema describing document-specific fields | Admin-editable per type |
| `file_types.primary_search_key` | One searchable metadata key per type | Required for indexed search |

### 5.4 Key Constraints
- No hard deletes through application UI.
- Every save replacement creates a new `document_versions` row.
- Canonical tables are `documents` + `document_versions`; flat `files` table is not used.
- Duplicate detection respects business rules and may block or warn during scholar or document creation.

---

## 6. Technology Stack

| Domain | Technology | Purpose |
|--------|------------|---------|
| Backend Framework | Laravel | Application core, routing, auth, ORM |
| Frontend | Livewire + Bootstrap 5 | Reactive UI without heavy JS framework |
| CSS/UI | Bootstrap 5 | Layout, components, responsive behavior |
| Authorization | Spatie Laravel Permission | Roles and permissions |
| Database | MySQL | Primary data store |
| Local test DB | SQLite | Cloud VM / lightweight testing |
| Containerization | Docker + Docker Compose | Local reproducible environment |
| Asset bundling | Vite | JS/CSS build pipeline |
| Testing | PHPUnit | Feature and unit tests |
| Code style | Laravel Pint | PHP lint/format |
| PDF/Export | jsPDF + SortableJS | Client-side PDF assembly and page ordering |
| Version control | Git + GitHub | Source control and PR workflow |
| CI | GitHub Actions | Lint, build, test automation |
| Documentation | Markdown, Google Docs Bible Center | Project docs and canonical tracker |

---

## 7. Hardware and Software Requirements

### 7.1 End-User Requirements

Minimum:
- CPU: Dual core 1.8 GHz
- RAM: 4 GB
- Disk: 20 GB available
- Display: 1366x768
- Browser: Chrome/Edge/Firefox latest stable
- Network: LAN connection to DOST RXI internal network

Recommended:
- CPU: Quad core 2.0 GHz or higher
- RAM: 8 GB or higher
- Disk: 50 GB SSD
- Display: 1920x1080
- Browser: Chrome/Edge latest stable with PDF viewer enabled
- Network: wired LAN connection for reliable server access

### 7.2 Developer Requirements

Minimum:
- CPU: Quad core 2.0 GHz
- RAM: 8 GB
- Disk: 80 GB SSD free
- Display: 1920x1080
- OS: Windows 10 22H2+, Ubuntu 22.04+, macOS 13+
- Tooling: Docker Desktop, Git, Composer, Node.js 18+, PHP 8.3+

Recommended:
- CPU: Six core 3.0 GHz+
- RAM: 16 GB+
- Disk: 256 GB SSD
- Additional: WAMP/XAMPP alternative stack for local debugging if needed

### 7.3 Server / Deployment Host Requirements
- CPU: 4 cores+
- RAM: 8 GB+
- Disk: 120 GB+ with backup volume or scheduled export
- OS: Linux server with Docker and Docker Compose
- Network: Static LAN IP reachable by internal clients

---

## 8. Integration and Deployment Details

### 8.1 Environment Setup
1. Clone repository:
   ```bash
   git clone git@github.com:xxch4nnn/DOST-S-T-SECTION-PROJECT.git
   cd DOST-S-T-SECTION-PROJECT
   ```
2. Copy `.env.example` to `.env` and set database credentials, app URL, and any feature flags.
3. Create SQLite file if testing locally without Docker:
   ```bash
   touch database/database.sqlite
   ```
4. Install dependencies:
   ```bash
   composer install
   npm install
   ```
5. Generate app key:
   ```bash
   php artisan key:generate
   ```

### 8.2 Docker Deployment
- Build and start services:
  ```bash
  docker compose up -d --build
  ```
- Services expected: app/php-fpm or Apache, MySQL, Vite, queue worker optional.
- Private uploads directory must be volume-backed to persist uploaded documents across container rebuilds.
- Backup scripts target MySQL and private storage directory.

### 8.3 Database Migrations and Seeding
- Run migrations:
  ```bash
  php artisan migrate
  ```
- Seed lookups and sample data:
  ```bash
  php artisan db:seed
  ```
- Fresh reset for test/CI:
  ```bash
  php artisan migrate:fresh --seed
  ```

### 8.4 Configuration Notes
- `config/filesystems.php` defines private upload disk; default is `storage/app/private`.
- `.env` expected keys:
  - `APP_NAME`, `APP_URL`
  - `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
  - `MAX_UPLOAD_MB=10`
  - `ALLOWED_EXTENSIONS=pdf,png,jpg,jpeg`
- Spatie permission cache cleared after role/permission changes in production:
  ```bash
  php artisan permission:cache-clear
  ```

### 8.5 CI/CD
- GitHub Actions should run:
  - `vendor/bin/pint --test`
  - `php artisan test`
  - `npm run build`
- Branch protection on `master`:
  - Require PR review.
  - Require status checks.
  - Squash merge only.
- Artefacts: build assets, test results, lint reports.

### 8.6 Backup and Restore
- MySQL logical backup via `mysqldump` to timestamped SQL files.
- For application backup:
  - Export `storage/app/private` directory.
  - Export SQL dump.
- Restore:
  - Recreate database.
  - Import SQL.
  - Restore private storage directory.
  - Run `php artisan migrate`.
  - Clear caches as needed.

### 8.7 Local Network Considerations
- Ensure server host firewall allows port 80/443 and MySQL port from workstation subnet.
- Clients access app by LAN IP or hostname configured in `APP_URL`.
- If HTTPS is desired, use self-signed certificates inside Docker or reverse proxy.

---

## 9. Appendices

### 9.1 Document Conventions
- `MUST` indicates mandatory requirements.
- `SHOULD` indicates recommended but not strictly required.
- `MAY` indicates optional features.

### 9.2 Reference Links
- Project README: `C:\Users\Asus\Documents\Personal\Programs\DOSTorage\README.md`
- Planning docs: `C:\Users\Asus\Documents\Personal\Programs\DOSTorage\planning`
- Bible Center: DOSTorage V1 Google Doc for canonical task and meeting tracking.

### 9.3 Change Control
- All changes affecting schema, behavior, user workflows, or CI must update `CHANGELOG.md`.
- Agent/background integration sessions must append `planning/AGENTIC_CHANGELOG.md`.
- Stitch-specific executions append `planning/STITCH_EXECUTION_LOG.md`.

---

*End of Technical Specifications*
