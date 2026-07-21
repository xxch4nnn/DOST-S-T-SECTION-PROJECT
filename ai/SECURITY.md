# Security Policies

> **Last Updated:** 2026-07-14
> **Status:** Draft (Pre-Development)
> **Maintainer:** Security Reviewer Agent

---

## Authentication

| Policy | Implementation |
|--------|---------------|
| Password hashing | bcrypt (Laravel default) |
| Login throttling | Max 5 attempts per minute |
| Session lifetime | 120 minutes (default) |
| Session regeneration | On login |
| Remember me | Secure token, optional |
| Password requirements | Min 8 chars (Breeze default) |

---

## Authorization Matrix

| Action | Admin | Encoder | Records Officer | Guest |
|--------|-------|---------|----------------|-------|
| View dashboard | ✅ | ✅ | ✅ | ❌ |
| List scholars | ✅ | ✅ | ✅ | ❌ |
| View scholar | ✅ | ✅ | ✅ | ❌ |
| Create scholar | ✅ | ✅ | ❌ | ❌ |
| Edit scholar | ✅ | ✅ | ❌ | ❌ |
| Delete scholar | ✅ | ❌ | ❌ | ❌ |
| Upload document | ✅ | ✅ | ❌ | ❌ |
| Download document | ✅ | ✅ | ✅ | ❌ |
| Delete document | ✅ | ❌ | ❌ | ❌ |
| Manage users | ✅ | ❌ | ❌ | ❌ |
| Manage roles | ✅ | ❌ | ❌ | ❌ |
| Generate reports | ✅ | ❌ | ✅ | ❌ |

---

## Input Validation

| Input | Validation | Reason |
|-------|-----------|--------|
| SPAS Number | `regex:/^SPAS-\d{4}-\d{3,4}$/` | Prevent injection |
| Email | `email` filter | Format validation |
| File upload | MIME + extension + size | Prevent malicious files |
| Names | `string, max:255` | Prevent overflow |
| Dates | `date, before:today` | Logical validation |

---

## File Upload Security

| Control | Implementation |
|---------|---------------|
| Allowed types | PDF only (`mimes:pdf`) |
| Max size | 10MB (`max:10240`) |
| Storage location | `storage/app/documents/` (private) |
| Filename strategy | UUID-based (prevents path traversal) |
| MIME validation | Server-side, not just extension |
| Access control | Authenticated download route |
| Path traversal prevention | Never use user-provided filenames for storage |

---

## Environment Security

| Rule | Details |
|------|---------|
| `.env` | NEVER committed to Git |
| `APP_DEBUG` | `false` in production |
| `APP_KEY` | Unique per environment |
| Database credentials | In `.env` only |
| HTTPS | Required in production |
| CORS | Restrictive (same-origin for web) |

---

## OWASP Top 10 Coverage

| # | Threat | Laravel Mitigation |
|---|--------|-------------------|
| A01 | Broken Access Control | Spatie + Policies + Middleware |
| A02 | Cryptographic Failures | bcrypt, HTTPS, APP_KEY |
| A03 | Injection | Eloquent ORM, parameterized queries |
| A04 | Insecure Design | Architecture review pipeline |
| A05 | Security Misconfiguration | `.env`, APP_DEBUG=false |
| A06 | Vulnerable Components | `composer audit` |
| A07 | Auth Failures | Breeze, throttling, session mgmt |
| A08 | Data Integrity Failures | CSRF tokens, signed routes |
| A09 | Logging Failures | Laravel logging, audit trail |
| A10 | SSRF | No external URL fetching in scope |
