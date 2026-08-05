# DOSTorage V1 — Frontend to Backend Developer Handoff

**Target Branch**: `feat/fe-08-upload-files-and-document-viewer`  
**Latest Commit**: `669006c`  
**Frontend Stack**: Laravel 13 / Livewire 4 / Tailwind CSS & Vanilla SCSS  

---

## 📌 Executive Summary

This document serves as the complete technical handoff guide for the **Scholar Document Management & Profile System** (Add Scholar, Edit Scholar, Fast Categorized Upload, Scholar Drawer, and Document Viewer).

The frontend has been structured to integrate seamlessly with the backend schema and controller endpoints.

---

## 🚀 Key Features Implemented

### 1. **Add Scholar with Categorized Document Upload**
* **Route**: `/scholars/add` (`App\Livewire\AddFile`)
* **Behavior**:
  * Captures complete scholar personal, academic, scholarship, demographic, and address metadata.
  * Allows creating categorized document folders (*e.g. Amendatory Agreement, Report of Grades, Certificate of Registration, etc.*).
  * Fast client-side file staging: files are queued instantly in browser memory, and pushed via Livewire's chunked file upload on save, eliminating UI freeze and upload lag.
  * On save, persists the `Scholar` record and links uploaded files to `Document` records.
  * Redirects to `/scholars?open_scholar={id}` to immediately open the Scholar Drawer.

---

### 2. **Edit Scholar Profile & Document Management**
* **Route**: `/scholars/{scholar}/edit` (`App\Livewire\Scholars\Edit`)
* **Behavior**:
  * Pre-populates all scholar fields from the database with model binding.
  * Loads existing documents grouped by `FileType`.
  * Allows staging new documents into categories via drag-and-drop or file picker.
  * Supports removing/soft-deleting existing documents (`deleteExistingDocument($id)`).
  * Includes graceful fallback logic during preview testing if an unseeded ID is passed.
  * Handles validation (including composite uniqueness and email uniqueness rules).

---

### 3. **Scholar Drawer & Document Viewer Integration**
* **Component**: `App\Livewire\Dashboard\ScholarDrawer`
* **Behavior**:
  * Dynamic sidebar drawer opened via event: `dispatch('open-scholar-drawer', scholarData: [...])` or route query param `?open_scholar={id}`.
  * Displays scholar summary banner, award year, scholarship program/type badge, contact information, and address.
  * Categorized document folder accordions rendering file size, upload dates, and quick actions.
  * "Edit Scholar" button routes directly to `/scholars/{id}/edit`.
  * Clicking any document opens the full-screen `DocumentViewer` modal with pagination, zoom, download, and print capabilities.

---

## 🗄️ Database & Schema Mapping Guide

### Comparison: Frontend Workspace vs. Backend Repository (`dost_system`)

| Field / Concept | Frontend Model / Field | Backend Developer's Model (`dost_system`) |
| :--- | :--- | :--- |
| **SPAS Number** | `spas_no` | `spas_number` |
| **Scholarship Program** | `scholarship_id` (`Scholarship`) | `scholarship_program_id` (`ScholarshipProgram`) |
| **Scholarship Program Type**| `scholarship_type_id` (`ScholarshipType`) | `scholarship_program_type_id` (`ScholarshipProgramType`) |
| **Clearance Status** | `clearance_status_id` (`ClearanceStatus`) | `clearance_status_id` (`ClearanceStatus`) |
| **School & Course** | `school_id`, `course_id` | `school_id`, `course_id` |
| **Address** | `barangay`, `municipality`, `district`, `province`, `region_id` | `barangay`, `municipality`, `district`, `province`, `region_id` |
| **Documents / Files** | `Document` (`documentable_type`, `documentable_id`, `file_type_id`) | `File` (`file_type_id`, `metadata->scholar_id`, `file_path`) |
| **File Categories** | `FileType` (`name`, `year`) | `FileType` (`file_group_id`, `name`, `metadata_template`) |

---

## 📦 Staged Upload Manifest Format (`saveScholarWithStagedFiles`)

When submitting files from the frontend, the manifest passed to Livewire is structured as follows:

```json
[
  {
    "index": 0,
    "cat_id": "cat_1",
    "cat_name": "Report of Grades",
    "name": "grades_1st_sem.pdf",
    "file_size": 245760,
    "mime_type": "application/pdf",
    "is_pdf": true,
    "is_image": false,
    "is_existing": false
  },
  {
    "index": 1,
    "cat_id": "cat_2",
    "cat_name": "Certificate of Registration",
    "name": "cor_2024.pdf",
    "file_size": 184320,
    "mime_type": "application/pdf",
    "is_pdf": true,
    "is_image": false,
    "is_existing": false
  }
]
```

---

## 🧪 Automated Testing & Verification

All feature tests have been written, executed, and verified against SQLite/MySQL:

```bash
# Run all feature tests
php artisan test

# Run specific scholar upload & edit tests
php artisan test tests/Feature/EditScholarTest.php
php artisan test tests/Feature/AddFileScholarUploadTest.php

# Code style and CSS linting
vendor/bin/pint --test
npm run lint:css
```

**Test Results Summary**:
* `tests/Feature/EditScholarTest.php`: **4 passed** (17 assertions)
* `tests/Feature/AddFileScholarUploadTest.php`: **3 passed** (11 assertions)
* **Total test suite**: **42 passed** (180 assertions)
* **Code quality**: 0 Pint warnings, 0 SCSS stylelint errors.

---

## 🛠️ Getting Started on the Backend

1. **Pull the feature branch**:
   ```bash
   git fetch origin
   git checkout feat/fe-08-upload-files-and-document-viewer
   git pull origin feat/fe-08-upload-files-and-document-viewer
   ```

2. **Run migrations & seeders**:
   ```bash
   php artisan migrate --seed
   ```

3. **Start local development**:
   ```bash
   php artisan serve
   npm run dev
   ```

4. **Verify UI**:
   - Access `http://127.0.0.1:8000/scholars`
   - Click **Add Scholar** at `/scholars/add`
   - Click **Edit Scholar** from the scholar drawer at `/scholars/{id}/edit`
