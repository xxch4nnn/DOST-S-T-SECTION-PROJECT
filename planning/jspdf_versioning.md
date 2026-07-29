# jspdf_versioning.md

Purpose: lock the client-side canvas → jsPDF pipeline and versioning rules before Rui and Wakin build independently.

---

## V1 canvas target

We cover **(a)** and **(b)** only. No text/annotation editing in V1.

- **(a)** Export a scholar’s documents as one PDF.
- **(b)** Reorder / delete / combine / insert pages before save.

Inserting pages between existing pages is allowed **only** when both source and destination are already loaded in the same canvas session.

---

## Save path ownership

| Layer | Owner | Responsibility |
|---|---|---|
| FE sortable UI | Rui | SortableJS drag-and-drop canvas, add/remove page controls, CSS data flow |
| BE save path + rules | Wakin | `document_type_id` update, `document_versions` write, validation |
| Shared contract | Both | JSON payload shape for `document_versions` fields; agreed in standup |

Rui delivers CSS data (page order array); Wakin impliments the write path.

---

## Versioning rules

1. Every Save → new `document_versions` row (no pure overwrite).
2. Duplicate modal options:
   - **Cancel** → abort save, no DB write.
   - **Keep history** → write new `document_versions` row.
   - **Overwrite** → write new `document_versions` row + mark previous as replaced (for audit).
3. `documents` table holds latest path, dates, CRUD metadata, soft deletes.
4. `document_versions` holds historical `version_number`, `replaced_by_user_id`, `updated_at`.

### Data shape (draft)

```
documents:
  id, documentable_id, documentable_type, document_type_id,
  file_name, file_path, file_size, mime_type,
  status (active / struck_off),
  uploaded_by (users.id),
  created_at, updated_at, deleted_at

document_versions:
  id, document_id,
  version_number,
  replaced_by_user_id,
  file_path,
  metadata_snapshot (JSON),
  updated_at
```

---

## SortableJS dependency

- Import: `sortablejs` via Vite (not CDN).
- Not the same as any table-column sort.
- `feat/ui-07-sortable` is table-column sort only if needed. Canvas sort is inside `feat/be-04-jspdf-export`.

---

## Error handling

- Invalid metadata = **hard fail** in Livewire/FormRequest before DB/storage write.
- If jsPDF export fails mid-canvas, preserve current canvas state; do not silently drop page order.

---

## Checklist before stitching

- [ ] SortableJS import added in Vite
- [ ] jsPDF v2.x installed (compat check with SortableJS)
- [ ] `document_versions` migration exists in mother
- [ ] `documents.documentable_*` morph columns exist
- [ ] `file_types.metadata_template` column name locked
- [ ] Invalid-metadata FormRequest / observer unit test added
- [ ] Duplicate modal wired to `document_versions` write path
