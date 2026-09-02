# DOSTorage Architecture

## 1. Context Diagram

```mermaid
graph TD
    %% Actors
    Encoder[Records Encoder]
    Admin[Records Officer / Admin]
    SuperAdmin[Super Admin]
    
    %% System
    System((DOSTorage System))
    
    %% Interactions
    Encoder -->|Uploads scans & creates scholar metadata| System
    Admin -->|Approves strike-offs, manages metadata| System
    SuperAdmin -->|Performs hard deletes, manages users| System
    System -->|Returns searchable records & PDFs| Admin
    System -->|Returns searchable records & PDFs| Encoder
```

## 2. Data Flow Diagram (DFD) - Level 0

```mermaid
graph TD
    Actor(User) -->|1. Submit Login Credentials| Auth[Auth System]
    Auth -->|2. Authenticated Session & Role| Actor
    
    Actor -->|3. Submit Scholar / Admin Metadata| RecordManager[Record Management]
    RecordManager -->|4. Save to DB| Database[(MySQL Database)]
    
    Actor -->|5. Upload Scanned PDF/Image| FileManager[File Storage Manager]
    FileManager -->|6. Save hashed file| LocalStorage[(Local Private Storage)]
    FileManager -->|7. Save file metadata & UUID| Database
    
    Actor -->|8. Search Query| SearchEngine[Search Engine]
    SearchEngine -->|9. Fetch Results| Database
    SearchEngine -->|10. Return Results| Actor
    
    Actor -->|11. Request File Download| FileManager
    FileManager -->|12. Stream decrypted/original file| Actor
```

## 3. Entity Relationship Diagram (ERD)

```mermaid
erDiagram
    users {
        int id PK
        string name
        string email
    }

    scholarships {
        int id PK
        string name
        boolean is_available
    }

    scholarship_types {
        int id PK
        string name
        boolean is_available
    }

    schools {
        int id PK
        string name
        string campus
        boolean is_available
    }

    courses {
        int id PK
        string name
        string abbreviation
        boolean is_available
    }

    regions {
        int id PK
        string name
        string abbreviation
        boolean is_available
    }

    clearance_statuses {
        int id PK
        string name
        boolean is_available
    }

    file_types {
        int id PK
        string name
        string year
    }

    scholars {
        int id PK
        string first_name
        string middle_name
        string last_name
        string generational_suffix
        int year_of_award
        int scholarship_id FK
        int scholarship_type_id FK
        string spas_no
        string sex
        date birthdate
        string contact_number
        string email_address
        int school_id FK
        int course_id FK
        string program
        string barangay
        string municipality
        string district
        string province
        int region_id FK
        int clearance_status_id FK
        date clearance_date
        boolean for_disposal
    }

    administrative_records {
        int id PK
        string record_type
        string series_number
        string title
        string recipient
        int year
        string quarter
        boolean for_disposal
        int created_by FK
    }

    documents {
        int id PK
        int documentable_id
        string documentable_type
        int file_type_id FK
        string original_filename
        string stored_filename
        string mime_type
        int file_size_kb
        string status
        int uploaded_by FK
        timestamp created_at
        timestamp deleted_at
    }

    document_versions {
        int id PK
        int document_id FK
        string stored_filename
        string original_filename
        int file_size_kb
        int version_number
        int replaced_by_user_id FK
    }

    audit_logs {
        int id PK
        int user_id FK
        string action
        string record_type
        int record_id
        json before_payload
        json after_payload
        string ip_address
    }

    %% Relationships
    scholars }o--|| scholarships : "belongs to"
    scholars }o--|| scholarship_types : "belongs to"
    scholars }o--|| schools : "attends"
    scholars }o--|| courses : "takes"
    scholars }o--|| regions : "lives in"
    scholars }o--|| clearance_statuses : "has"

    scholars ||--o{ documents : "has many (polymorphic)"
    administrative_records ||--o{ documents : "has many (polymorphic)"

    documents }o--|| file_types : "is of type"
    documents }o--|| users : "uploaded by"
    administrative_records }o--|| users : "created by"

    documents ||--o{ document_versions : "has history"
    document_versions }o--|| users : "replaced by"

    audit_logs }o--|| users : "performed by"
```

## 4. Approved Architecture Decisions
- **Framework**: Laravel 11 + Livewire + Spatie Permissions
- **Deployment**: Local Docker environment (Sail) offline on a single LAN server.
- **File Storage**: Local private disk with UUID hashed filenames, polymorphic relationships for documents (ADR-005).
- **Concurrency**: Simple locking strategy for records to prevent duplicate entry.
- **Search**: Scoped to Name, SPAS No, Award Year, School, and Course for scholars.
- **Schema**: Additive tables on top of DOST's draft schema (ADR-006).
