# Database Documentation

> **Last Updated:** 2026-07-14
> **Status:** Draft (Pre-Development)
> **Maintainer:** Database Reviewer Agent

---

## Connection Details

| Environment | Host | Port | Database | User | Password |
|-------------|------|------|----------|------|----------|
| Local Dev | 127.0.0.1 | 3306 | laravel_bootcamp | laravel_dev | bootcamp2026 |
| Docker | mysql | 3306 | laravel_bootcamp | laravel_dev | bootcamp2026 |

---

## Entity Relationship Diagram

```
┌──────────┐       ┌───────────┐       ┌────────────┐
│  users   │──M:N──│ role_user │──M:N──│   roles    │
│          │       │  (pivot)  │       │            │
│ id       │       │ user_id   │       │ id         │
│ name     │       │ role_id   │       │ name       │
│ email    │       └───────────┘       │ guard_name │
│ password │                           └────────────┘
└────┬─────┘
     │ 1:N (uploaded_by)
     │
┌────▼─────────┐       ┌──────────────┐
│  documents   │──N:1──│   scholars   │
│              │       │              │
│ id           │       │ id           │
│ scholar_id   │───────│ spas_no      │
│ document_type│       │ first_name   │
│ original_name│       │ last_name    │
│ file_path    │       │ email        │
│ file_size    │       │ birthdate    │
│ uploaded_by  │       │ school       │
│ created_at   │       │ course       │
│ updated_at   │       │ status       │
└──────────────┘       │ created_at   │
                       │ updated_at   │
                       └──────────────┘
```

---

## Tables

### users (Managed by Laravel Breeze)
| Column | Type | Nullable | Key | Notes |
|--------|------|----------|-----|-------|
| id | BIGINT UNSIGNED | No | PK | Auto-increment |
| name | VARCHAR(255) | No | | |
| email | VARCHAR(255) | No | UQ | |
| email_verified_at | TIMESTAMP | Yes | | |
| password | VARCHAR(255) | No | | Hashed |
| remember_token | VARCHAR(100) | Yes | | |
| created_at | TIMESTAMP | Yes | | |
| updated_at | TIMESTAMP | Yes | | |

### scholars
| Column | Type | Nullable | Key | Notes |
|--------|------|----------|-----|-------|
| id | BIGINT UNSIGNED | No | PK | Auto-increment |
| spas_no | VARCHAR(50) | No | UQ | SPAS number |
| first_name | VARCHAR(255) | No | | |
| last_name | VARCHAR(255) | No | IDX | Searchable |
| email | VARCHAR(255) | Yes | UQ | |
| birthdate | DATE | Yes | | |
| school | VARCHAR(255) | No | IDX | |
| course | VARCHAR(255) | Yes | | |
| status | ENUM | No | IDX | active/inactive/graduated |
| created_at | TIMESTAMP | Yes | | |
| updated_at | TIMESTAMP | Yes | | |

### documents
| Column | Type | Nullable | Key | Notes |
|--------|------|----------|-----|-------|
| id | BIGINT UNSIGNED | No | PK | Auto-increment |
| scholar_id | BIGINT UNSIGNED | No | FK → scholars | ON DELETE CASCADE |
| document_type | ENUM | No | IDX | agreement/tor/prospectus/endorsement/other |
| original_filename | VARCHAR(255) | No | | User's original filename |
| file_path | VARCHAR(500) | No | | Storage path |
| file_size | BIGINT UNSIGNED | No | | Bytes |
| uploaded_by | BIGINT UNSIGNED | Yes | FK → users | ON DELETE SET NULL |
| created_at | TIMESTAMP | Yes | | |
| updated_at | TIMESTAMP | Yes | | |

### roles & permissions (Managed by Spatie)
See [Spatie documentation](https://spatie.be/docs/laravel-permission) for schema details.

---

## Indexes

| Table | Index | Columns | Type | Reason |
|-------|-------|---------|------|--------|
| scholars | idx_spas_no | spas_no | UNIQUE | Primary search field |
| scholars | idx_last_name | last_name | INDEX | Name search |
| scholars | idx_school | school | INDEX | Filter by school |
| scholars | idx_status | status | INDEX | Filter by status |
| documents | idx_scholar_type | scholar_id, document_type | COMPOSITE | Common query pattern |

---

## Migration Changelog

| Date | Migration | Description | Reviewed |
|------|-----------|-------------|----------|
| (pending) | create_scholars_table | Initial scholar schema | ⬜ |
| (pending) | create_documents_table | Initial documents schema | ⬜ |
