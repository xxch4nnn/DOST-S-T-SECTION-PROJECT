# API Documentation

> **Last Updated:** 2026-07-14
> **Status:** Draft (Pre-Development)
> **Maintainer:** Documentation Agent

---

## Overview

The DOST Scholarship Records Digitization System uses **server-rendered views** (Blade + Livewire), not a traditional REST API. However, all routes follow RESTful conventions.

If an API layer is needed in the future, the existing controller/service architecture can be extended with API controllers.

---

## Route Summary

### Authentication (Laravel Breeze)

| Method | Route | Controller | Middleware | Description |
|--------|-------|-----------|------------|-------------|
| GET | /login | LoginController | guest | Show login form |
| POST | /login | LoginController | guest | Authenticate |
| POST | /logout | LoginController | auth | Logout |
| GET | /register | RegisterController | guest | Show registration |
| POST | /register | RegisterController | guest | Create account |

### Dashboard

| Method | Route | Controller | Middleware | Description |
|--------|-------|-----------|------------|-------------|
| GET | /dashboard | DashboardController | auth | Main dashboard |

### Scholars

| Method | Route | Controller@Method | Middleware | Description |
|--------|-------|----------|------------|-------------|
| GET | /scholars | ScholarController@index | auth | List scholars |
| GET | /scholars/create | ScholarController@create | auth, role:admin\|encoder | Create form |
| POST | /scholars | ScholarController@store | auth, role:admin\|encoder | Store scholar |
| GET | /scholars/{scholar} | ScholarController@show | auth | View scholar |
| GET | /scholars/{scholar}/edit | ScholarController@edit | auth, role:admin\|encoder | Edit form |
| PUT | /scholars/{scholar} | ScholarController@update | auth, role:admin\|encoder | Update |
| DELETE | /scholars/{scholar} | ScholarController@destroy | auth, role:admin | Delete |

### Documents

| Method | Route | Controller@Method | Middleware | Description |
|--------|-------|----------|------------|-------------|
| POST | /scholars/{scholar}/documents | DocumentController@store | auth, role:admin\|encoder | Upload document |
| GET | /documents/{document}/download | DocumentController@download | auth | Download file |
| DELETE | /documents/{document} | DocumentController@destroy | auth, role:admin | Delete document |

---

## Validation Rules

### Store Scholar
| Field | Rules |
|-------|-------|
| spas_no | required, string, max:50, unique:scholars |
| first_name | required, string, max:255 |
| last_name | required, string, max:255 |
| email | nullable, email, unique:scholars |
| birthdate | nullable, date, before:today |
| school | required, string, max:255 |
| course | nullable, string, max:255 |
| status | required, in:active,inactive,graduated |

### Store Document
| Field | Rules |
|-------|-------|
| file | required, file, mimes:pdf, max:10240 |
| document_type | required, in:agreement,tor,prospectus,endorsement,other |

---

## Response Patterns

All web routes return **Blade views** or **redirects with flash messages**.

```php
// Success redirect
return redirect()->route('scholars.index')
    ->with('success', 'Scholar created successfully.');

// Error redirect (automatic via Form Request validation)
// Returns back with validation errors
```
