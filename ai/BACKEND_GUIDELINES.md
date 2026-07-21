# Backend Guidelines

> **Last Updated:** 2026-07-14
> **Audience:** All implementation and review agents

---

## Coding Standards

### PHP
- **PHP 8.3+** features: typed properties, enums, readonly, match expressions
- **PSR-12** coding style
- **Strict types** in all files: `declare(strict_types=1);`
- Type hints on ALL parameters and return types
- Prefer `readonly` properties where applicable
- Use PHP 8.1+ enums instead of string constants

### Laravel
- **Thin controllers** — max 10 lines per method, delegate to services
- **Form Requests** for all validation (never inline `$request->validate()`)
- **Eloquent** over raw SQL (unless performance demands it)
- **Route Model Binding** — `Scholar $scholar` not `$id`
- **Resource Routes** — `Route::resource()` for CRUD
- **Config over hardcoding** — use `config()` and `.env`
- **Collections** over raw `foreach` loops when transforming data

### Naming Conventions

| Item | Convention | Example |
|------|-----------|---------|
| Model | Singular, PascalCase | `Scholar` |
| Controller | Singular + Controller | `ScholarController` |
| Migration | snake_case, descriptive | `create_scholars_table` |
| Table | Plural, snake_case | `scholars` |
| Column | snake_case | `spas_no`, `created_at` |
| Pivot Table | Singular, alphabetical | `role_user` |
| Foreign Key | singular_table_id | `scholar_id` |
| Form Request | Action + Model + Request | `StoreScholarRequest` |
| Service | Model + Service | `ScholarService` |
| Policy | Model + Policy | `ScholarPolicy` |
| Enum | PascalCase | `DocumentType`, `ScholarStatus` |
| Route | Plural, kebab-case | `/scholars`, `/scholar-documents` |

### File Organization
- One class per file
- Namespaces mirror directory structure
- Group by domain (not by type) when project grows

---

## Git Conventions

### Branch Naming
```
feature/scholar-crud
feature/document-upload
fix/spas-validation
refactor/service-layer
docs/api-documentation
```

### Commit Messages
```
feat: add scholar CRUD operations
fix: correct SPAS number validation regex
refactor: extract document upload to service
test: add scholar creation edge cases
docs: update API documentation
chore: update composer dependencies
```

---

## Error Handling

- Use Laravel's exception handler
- Never expose stack traces to users
- Log all errors with context
- Use custom exceptions for business logic errors
- Return appropriate HTTP status codes

---

## Security Rules

- Never trust user input
- Always validate server-side
- Use `$fillable` on every model
- Store files in private storage
- Use policies for authorization
- Never commit `.env`
- Never expose debug info in production
