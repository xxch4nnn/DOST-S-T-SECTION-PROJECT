# Database Review: DOSTorage MVP ERD

## 1. Schema Analysis

### ✅ Good
- Use of auto-increment `id` for primary keys instead of `SPAS No` ensures uniqueness (ADR-002).
- Polymorphic `documents` table elegantly supports both `scholars` and `administrative_records`.
- Audit log structure covers the necessary fields (user, action, payloads).
- Soft deletes implemented on the `documents` table to support the "strike-off" feature without losing data.

### ⚠️ Recommendations
- **Indexes:** 
  - Add a composite index on `(documentable_type, documentable_id)` in the `documents` table for fast polymorphic lookups.
  - Add an index on `scholars.spas_no` and `scholars.last_name` as these will be highly queried for search.
- **Foreign Key Cascades:** 
  - Use `ON DELETE RESTRICT` for lookup tables (e.g., `schools`, `courses`). We cannot delete a school if a scholar is assigned to it.
  - Use `ON DELETE RESTRICT` on `users` relations (`uploaded_by`, `created_by`). In a government audit context, users shouldn't be hard-deleted if they own records.

### ❌ Issues
- **`contact_number` Unique Constraint:** The original architect draft had `contact_number` as UNIQUE. The bible (Section 19) flags this for removal. **Action:** Drop the UNIQUE constraint on `contact_number` in the migration, as many scholars may share a phone number or leave it null.
- **`email_address` Nullability:** It is currently `UNIQUE`, but ensure it is `nullable()` if scholars might not have one.

## 2. Approved Action Items for Implementation
The Laravel Implementer must ensure the following when writing migrations:
1. **Remove `unique()`** from `contact_number` in `create_scholars_table`.
2. **Add composite index** `$table->index(['documentable_type', 'documentable_id'])` in `create_documents_table`.
3. **Use `constrained()->restrictOnDelete()`** for lookups and users to prevent accidental hard deletes of critical reference data.
