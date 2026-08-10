# RFC — UUID Documents Redesign

**Status:** Approved (CHAN_ACK Q05: B — 2026-08-10)  
**Author:** Wakin (`@WakenMac`) — proposed in `db-integration` branch / PR #68  
**Decision owner:** Chan (`@xxch4nnn`)  
**Approved:** 2026-08-10 03:01 +08:00  
**Supersedes:** Mother `documents` shape (bigint PK + file columns on `documents`)

---

## Summary

Restructure the `documents` and `document_versions` tables so that:

- `documents` becomes a **thin identity shell** — UUID primary key, polymorphic owner, status, and metadata only.
- All **file-level details** (path, type, size, mime, uploader) move to `document_versions`.
- Every document always has at least one version; the "current" file is always the latest version.

This follows Wakin's design from `db-integration` and was approved by Chan via Q05=B.

---

## Why

| Problem with current shape | How UUID design fixes it |
|----------------------------|--------------------------|
| File details live on `documents` AND `document_versions` — duplicated data | File details live **only** on `document_versions` — single source of truth |
| `document_versions` is an afterthought backup; awkward to query "what changed" | Versions are the **primary** record; document is just the identity envelope |
| Integer auto-increment PKs leak record count and ordering | UUID PKs are opaque, safe for public URLs |
| No clean way to "replace" a file while keeping the old one discoverable | Upload = create new version row; old versions stay queryable |

---

## Current Schema (Mother — `master`)

### `documents`
```
id                  bigint PK auto-increment
documentable_type   string (morph)
documentable_id     bigint (morph)
file_type_id        FK → file_types
original_filename   string(255)
stored_filename     string(100)
mime_type           string(100)
file_size_kb        integer
status              enum('active','struck_off')
metadata            json nullable
uploaded_by         FK → users
timestamps
soft_deletes
```

### `document_versions`
```
id                  bigint PK auto-increment
document_id         FK → documents (cascade delete)
stored_filename     string(100)
original_filename   string(255)
file_size_kb        integer
version_number      integer
replaced_by_user_id FK → users
timestamps
```

---

## Proposed Schema (Wakin UUID design)

### `documents` — thin identity shell

```
uuid                uuid PK
documentable_type   string (morph — unchanged)
documentable_id     bigint (morph — unchanged)
status              enum('active','struck_off') default 'active'
metadata            json nullable
timestamps
soft_deletes
```

**Removed from `documents`:** `file_type_id`, `original_filename`, `stored_filename`, `mime_type`, `file_size_kb`, `uploaded_by` — all moved to `document_versions`.

### `document_versions` — holds all file data

```
id                  bigint PK auto-increment
document_uuid       FK → documents.uuid (cascade delete)
file_type_id        FK → file_types
file_path           string(500)          — full storage path
original_filename   string(255)
stored_filename     string(100)
mime_type           string(100)
file_size_bytes     bigint               — bytes, not KB (more precise)
version_number      integer
uploaded_by         FK → users           — moved here from documents
timestamps
```

**Key changes on `document_versions`:**
- `document_id` → `document_uuid` (FK to UUID)
- `file_size_kb` → `file_size_bytes` (more precise)
- Added: `file_type_id`, `file_path`, `mime_type`, `uploaded_by`
- `replaced_by_user_id` → `uploaded_by` (each version records who uploaded it)

---

## Migration Strategy — Additive, Not Replace

> **Hard rule:** Do not delete the `2026_07_15_095054_create_documents_table.php` or `2026_07_15_095055_create_document_versions_table.php` migrations. Write new additive migrations.

### Migration 1: `2026_08_10_000001_reshape_documents_to_uuid.php`

```php
// 1. Add uuid column to documents
Schema::table('documents', function (Blueprint $table) {
    $table->uuid('uuid')->after('id')->unique();
});

// 2. Backfill UUIDs for existing rows
DB::table('documents')->whereNull('uuid')->cursor()->each(function ($doc) {
    DB::table('documents')->where('id', $doc->id)->update(['uuid' => Str::uuid()]);
});

// 3. Copy file columns from documents to their version-1 rows in document_versions
//    (for any document that doesn't have a version yet)

// 4. Add new columns to document_versions
Schema::table('document_versions', function (Blueprint $table) {
    $table->uuid('document_uuid')->nullable()->after('document_id');
    $table->foreignId('file_type_id')->nullable()->after('document_uuid')
          ->constrained()->nullOnDelete();
    $table->string('file_path', 500)->nullable()->after('original_filename');
    $table->string('mime_type', 100)->nullable()->after('file_path');
    $table->bigInteger('file_size_bytes')->nullable()->after('mime_type');
    $table->foreignId('uploaded_by')->nullable()->after('file_size_bytes')
          ->constrained('users')->nullOnDelete();
});

// 5. Backfill document_uuid from document_id join
// 6. Backfill file_type_id, mime_type, uploaded_by from parent document
// 7. Convert file_size_kb → file_size_bytes (* 1024)
```

### Migration 2: `2026_08_10_000002_drop_file_columns_from_documents.php`

```php
// Only runs AFTER backfill is verified
Schema::table('documents', function (Blueprint $table) {
    $table->dropForeign(['file_type_id']);
    $table->dropForeign(['uploaded_by']);
    $table->dropColumn([
        'file_type_id', 'original_filename', 'stored_filename',
        'mime_type', 'file_size_kb', 'uploaded_by',
    ]);
});

// Swap primary key: drop auto-increment id, promote uuid to PK
// (This step is optional for V1 — can keep id + uuid dual key and switch PK post-V1)
```

### Migration 3: `2026_08_10_000003_finalize_document_versions_fk.php`

```php
// Drop old integer FK, set uuid FK as the canonical link
Schema::table('document_versions', function (Blueprint $table) {
    $table->dropForeign(['document_id']);
    $table->dropColumn('document_id');

    $table->foreign('document_uuid')
          ->references('uuid')->on('documents')
          ->cascadeOnDelete();

    $table->dropColumn(['file_size_kb', 'replaced_by_user_id']);
});
```

> **Rollback safety:** Each migration has a `down()` that restores the previous shape. If any step fails, `migrate:rollback` restores mother schema cleanly.

---

## Model Changes

### `Document.php`

```php
class Document extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'documentable_type', 'documentable_id',
        'status', 'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function documentable()
    {
        return $this->morphTo();
    }

    public function versions()
    {
        return $this->hasMany(DocumentVersion::class, 'document_uuid', 'uuid');
    }

    /** Current (latest) version — convenience accessor. */
    public function currentVersion()
    {
        return $this->hasOne(DocumentVersion::class, 'document_uuid', 'uuid')
                     ->latestOfMany('version_number');
    }
}
```

### `DocumentVersion.php`

```php
class DocumentVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_uuid', 'file_type_id', 'file_path',
        'original_filename', 'stored_filename', 'mime_type',
        'file_size_bytes', 'version_number', 'uploaded_by',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class, 'document_uuid', 'uuid');
    }

    public function fileType()
    {
        return $this->belongsTo(FileType::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
```

---

## Impact on Existing Code

| Area | What changes | Effort |
|------|-------------|--------|
| **Upload (PR #65 path)** | `Document::create()` no longer passes file columns — create document shell, then create version row | Medium — rewrite `AddFile` Livewire |
| **Download / viewer** | `$document->stored_filename` → `$document->currentVersion->stored_filename` | Low — accessor swap |
| **Search (Q09)** | Query `document_versions` for filename/type, join back to `documents` for owner | Medium — after viewer ships |
| **Audit logs** | No change — audit still references `documents.uuid` via record_type/record_id | None |
| **Tests** | Factories need updating; `Document::factory()` creates shell + version | Medium |
| **Seeders** | Same as tests — seed version row alongside document | Low |

---

## Slice Execution Order (Post-RFC Merge)

| Order | PR Branch | Scope | Depends on |
|------:|-----------|-------|------------|
| 0 | `docs/uuid-documents-rfc` | **This RFC** — merged as docs-only PR | Nothing |
| 1 | `feat/be-58-uuid-migration` | Migrations 1–3 + model changes + factory updates | RFC merged |
| 2 | `feat/be-58-viewer` | Viewer/print/download ported to `currentVersion` accessor | Migration merged |
| 3 | `feat/be-58-scholar-observer` | ScholarObserver → `record_*` audit columns (Q08=C) | Migration merged |
| 4 | `feat/be-58-search` | Dashboard search against `document_versions` | Viewer merged |

---

## Open Questions for Wakin

> **Interim defaults shipped in #73** (override in a follow-up if needed):

1. **Dual-key transition?** ✅ V1 keeps `documents.id` (bigint PK) + unique `uuid`. App relations/FK use `uuid`; route binding still uses bigint `id` for now.

2. **`file_path` format:** ✅ Relative from storage disk root, e.g. `documents/{stored_filename}` (matches current `Storage::disk('local')` layout).

3. **`file_size_bytes` type:** ✅ `unsignedBigInteger` / bigint.

---

## Decision Record

```
CHAN_ACK Q02: B     — prefer Wakin's side on remaining conflicts
CHAN_ACK Q05: B     — UUID thin documents + bytes on versions (this RFC)
CHAN_ACK Q07: C     — folders code stays dormant (no migration, no routes)
Decided: 2026-08-10 03:01 +08:00
```
