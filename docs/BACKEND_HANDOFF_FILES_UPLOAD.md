# Backend Developer Handoff: Scanned Files Upload & Category Management

## Overview
This document provides the backend developer with complete technical specifications, data structures, and database schema mappings for the **Add Scholarship File & Scanned Documents Upload** feature.

The frontend is implemented in Laravel 13 / Livewire 4, allowing staff to register scholar records, create dynamic category folders (e.g. *Amendatory Agreement*, *Report of Grades* or custom named folders), upload files (PNG, JPG, PDF) with Google Drive grid preview and drag-and-drop reordering, and immediately display the uploaded documents in the **Scholar Drawer**.

---

## 1. Frontend Data Structure (`AddFile.php`)

When submitting the Add Scholarship File form (`wire:submit="saveScholar"`), the component processes:

### Scholar Metadata
```json
{
  "last_name": "Dela Cruz",
  "first_name": "Juan",
  "middle_name": "Santos",
  "generational_suffix": null,
  "spas_no": "2023-00855-2235",
  "year_of_award": 2023,
  "scholarship_id": 1,
  "scholarship_type_id": 1,
  "school_id": 1,
  "course_id": 1,
  "clearance_status_id": 2,
  "clearance_date": null,
  "barangay": "Brgy. 34-D",
  "municipality": "Davao City",
  "province": "Davao del Sur",
  "region_id": 1,
  "birthdate": "2002-05-15",
  "sex": "Male"
}
```

### Scanned Categories & Staged Files (`$scannedCategories`)
```json
[
  {
    "id": "cat_66a1bc",
    "name": "Amendatory Agreement",
    "selected_type": "Amendatory Agreement",
    "is_custom": false,
    "files": [
      {
        "id": "file_66a1bc_1",
        "name": "amendatory_signed_page1.pdf",
        "size": 1258291,
        "size_formatted": "1.2 MB",
        "mime_type": "application/pdf",
        "is_pdf": true,
        "is_image": false,
        "temp_path": "C:\\Users\\...\\LivewireTmp\\...",
        "temp_name": "phpA1B2.tmp"
      }
    ]
  },
  {
    "id": "cat_66a1bd",
    "name": "Report of Grades",
    "selected_type": "Report of Grades",
    "is_custom": false,
    "files": [
      {
        "id": "file_66a1bd_1",
        "name": "grades_1st_sem_2023.jpg",
        "size": 450560,
        "size_formatted": "440 KB",
        "mime_type": "image/jpeg",
        "is_pdf": false,
        "is_image": true,
        "temp_path": "C:\\Users\\...\\LivewireTmp\\...",
        "temp_name": "phpC3D4.tmp"
      }
    ]
  }
]
```

---

## 2. Database Schema Mapping (`dost_system` SQL)

The frontend maps directly to the `dost_system` database schema:

```
+------------------+         +------------------+         +------------------+
|     scholars     | 1     * |    documents /   | *     1 |    file_types    |
|                  |<------->|      files       |<------->|                  |
| - id             |         | - id             |         | - id             |
| - spas_no        |         | - documentable_id|         | - name           |
| - first_name     |         | - doc_type (poly)|         | - file_group_id  |
| - last_name      |         | - file_type_id   |         | - year           |
+------------------+         | - stored_filename|         +------------------+
                             | - orig_filename  |
                             | - mime_type      |
                             | - file_size_kb   |
                             +------------------+
```

### Table 1: `scholars`
- `id` (INT / BIGINT, PK, AI)
- `spas_no` (VARCHAR 255, UNIQUE)
- `first_name` (VARCHAR 255)
- `last_name` (VARCHAR 255)
- `middle_name` (VARCHAR 255, NULLABLE)
- `generational_suffix` (VARCHAR 50, NULLABLE)
- `scholarship_type_id` (FK -> `scholarship_types.id`)
- `school_id` (FK -> `schools.id`)
- `course_id` (FK -> `courses.id`)
- `clearance_status_id` (FK -> `clearance_statuses.id`)
- `clearance_date` (DATE, NULLABLE)
- `year_of_award` (INT / VARCHAR 4)
- `barangay` (VARCHAR 255, NULLABLE)
- `municipality` (VARCHAR 255, NULLABLE)
- `province` (VARCHAR 255, NULLABLE)
- `region_id` (FK -> `regions.id`)
- `birthdate` (DATE, NULLABLE)
- `sex` (VARCHAR 10, e.g., 'Male', 'Female')

### Table 2: `file_types`
- `id` (INT / BIGINT, PK, AI)
- `name` (VARCHAR 255) -> e.g. "Amendatory Agreement", "Report of Grades"
- `year` (VARCHAR 4, NULLABLE) -> Fiscal/award year
- `file_group_id` (FK -> `file_groups.id`, NULLABLE)

### Table 3: `documents` (or `files` in `dost_system`)
- `id` (INT / BIGINT, PK, AI)
- `documentable_type` (VARCHAR 255) -> `App\Models\Scholar`
- `documentable_id` (BIGINT) -> `$scholar->id`
- `file_type_id` (FK -> `file_types.id`)
- `original_filename` (VARCHAR 255) -> Client's uploaded name (e.g. `tor_scan.pdf`)
- `stored_filename` (VARCHAR 255) -> Unique safe filename (e.g. `doc_66a1bc920.pdf`)
- `mime_type` (VARCHAR 100) -> `application/pdf`, `image/png`, `image/jpeg`
- `file_size_kb` (INT) -> Size in KB
- `status` (VARCHAR 50) -> Default `'active'`
- `uploaded_by` (FK -> `users.id`) -> ID of authenticated staff user

---

## 3. Storage Architecture & Directory Convention

- **Disk**: `local` or `public` disk configured in `config/filesystems.php`.
- **Target Folder**: `documents/` (e.g., `storage/app/private/documents/` or `storage/app/public/documents/`).
- **File Retrieval & Security**:
  - Secure downloads routed via `route('documents.download', $id)` in `DocumentViewer` and `ScholarDrawer`.
  - Content-Type header dynamically set to `application/pdf`, `image/png`, `image/jpeg` to allow inline browser viewing.

---

## 4. Eloquent Relationship Definitions

### Scholar Model (`app/Models/Scholar.php`)
```php
public function documents(): MorphMany
{
    return $this->morphMany(Document::class, 'documentable');
}
```

### Document Model (`app/Models/Document.php`)
```php
public function documentable(): MorphTo
{
    return $this->morphTo();
}

public function fileType(): BelongsTo
{
    return $this->belongsTo(FileType::class);
}

public function uploader(): BelongsTo
{
    return $this->belongsTo(User::class, 'uploaded_by');
}
```

### FileType Model (`app/Models/FileType.php`)
```php
public function documents(): HasMany
{
    return $this->hasMany(Document::class);
}
```

---

## 5. Summary of Standard DOST File Categories Seeded

| ID | File Type Name | Suggested File Group |
|---|---|---|
| 1 | Amendatory Agreement | Scholarship Documents |
| 2 | Report of Grades | Academic Records |
| 3 | Certificate of Grades (COG) | Academic Records |
| 4 | Transcript of Records (TOR) | Academic Records |
| 5 | Scholarship Agreement | Contracts & Legal |
| 6 | Certificate of Graduation / Diploma | Completion Documents |
| 7 | Enrollment / Registration Form | Academic Records |
| 8 | Clearance Form / Certificate | Clearance & Status |
| 9 | Official Receipt (O.R.) | Financial Documents |
| 10 | Medical Certificate | Supporting Documents |
| 11 | Other Supporting Documents | General Documents |

---

## 6. How the Drawer Renders Documents

1. Staff saves scholar -> Livewire redirects to `/scholars?open_scholar={$scholar->id}`.
2. `ScholarDrawer` automatically triggers `openDrawer($scholarId)` on `mount()`.
3. It fetches `$scholar->load('documents.fileType')` and groups them by `fileType->name`.
4. Folder accordions render for each category.
5. Clicking any file opens the `DocumentViewer` overlay modal with high-resolution view, pagination, download, and print capabilities.
