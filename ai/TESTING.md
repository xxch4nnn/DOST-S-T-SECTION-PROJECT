# Testing Strategy

> **Last Updated:** 2026-07-14
> **Status:** Draft (Pre-Development)
> **Maintainer:** QA Engineer Agent

---

## Testing Framework

- **Pest PHP** (preferred) — modern, expressive syntax
- **PHPUnit** — fallback, Laravel's default
- Both are supported and can coexist

---

## Test Categories

| Category | Purpose | Location | Example |
|----------|---------|----------|---------|
| **Feature Tests** | Full HTTP request lifecycle | `tests/Feature/` | Login, CRUD operations |
| **Unit Tests** | Individual class methods | `tests/Unit/` | Service methods, helpers |
| **Browser Tests** | End-to-end UI testing | `tests/Browser/` | Livewire interactions |

---

## Naming Convention

```php
// Pest
test('encoder can create a scholar with valid data', function () { ... });
test('guest cannot access scholar list', function () { ... });
test('upload rejects files larger than 10mb', function () { ... });

// PHPUnit
public function test_encoder_can_create_scholar_with_valid_data(): void { ... }
```

Pattern: `{role} {can/cannot} {action} {context}`

---

## Test Coverage Targets

| Area | Minimum Coverage | Priority |
|------|-----------------|----------|
| Authentication | 100% | 🔴 Critical |
| Authorization (roles/permissions) | 100% | 🔴 Critical |
| Scholar CRUD | 90% | 🟠 High |
| Document Upload | 90% | 🟠 High |
| Validation Rules | 80% | 🟡 Medium |
| Search/Filtering | 70% | 🟡 Medium |
| Dashboard | 50% | 🟢 Low |

---

## Test Data Strategy

- Use **Factories** for generating test data
- Use **Seeders** for demo/staging data
- Never use production data in tests
- Reset database between tests (`RefreshDatabase` trait)

---

## Running Tests

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test --filter=ScholarTest

# Run with coverage
php artisan test --coverage

# Run Pest specifically
./vendor/bin/pest
```

---

## Test Checklist (Per Feature)

- [ ] 3+ happy path tests
- [ ] 5+ edge case tests
- [ ] 3+ negative tests (invalid input)
- [ ] 2+ authorization tests (wrong role)
- [ ] 1+ unauthenticated test
- [ ] Database assertions (`assertDatabaseHas`, `assertDatabaseMissing`)
- [ ] Response status assertions
- [ ] Flash message assertions
