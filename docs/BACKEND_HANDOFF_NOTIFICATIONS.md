# DOSTorage V1 — Notifications & Corner Alert System Handoff

**Target Branch**: `feat/fe-08-upload-files-and-document-viewer`  
**Frontend Stack**: Laravel 13 / Livewire 4 / Alpine.js / Phosphor Icons / SCSS  

---

## 📌 Feature Overview

This document provides technical specifications and implementation details for the **Notifications Center** and **Global Right-Corner Alert System** built for DOSTorage V1.

---

## 📂 Frontend Files Implemented

### 1. Livewire Component & Blade Views
* **Livewire Component**: [`app/Livewire/Notifications/Index.php`](file:///c:/Users/palab/.gemini/antigravity-ide/scratch/DOST-S-T-SECTION-PROJECT/app/Livewire/Notifications/Index.php)
* **Main Blade View**: [`resources/views/livewire/notifications/index.blade.php`](file:///c:/Users/palab/.gemini/antigravity-ide/scratch/DOST-S-T-SECTION-PROJECT/resources/views/livewire/notifications/index.blade.php)
* **Global Toast Component**: [`resources/views/components/notification-toast.blade.php`](file:///c:/Users/palab/.gemini/antigravity-ide/scratch/DOST-S-T-SECTION-PROJECT/resources/views/components/notification-toast.blade.php)
* **Layout Mounting**: [`resources/views/layouts/app.blade.php`](file:///c:/Users/palab/.gemini/antigravity-ide/scratch/DOST-S-T-SECTION-PROJECT/resources/views/layouts/app.blade.php) (mounted `<x-notification-toast />` globally before `</body>`)
* **Sidebar Link**: [`resources/views/livewire/layout/sidebar.blade.php`](file:///c:/Users/palab/.gemini/antigravity-ide/scratch/DOST-S-T-SECTION-PROJECT/resources/views/livewire/layout/sidebar.blade.php) (`route('notifications.index')`)

### 2. Styling & CSS Partials
* **SCSS Styles**: [`resources/css/components/_notifications.scss`](file:///c:/Users/palab/.gemini/antigravity-ide/scratch/DOST-S-T-SECTION-PROJECT/resources/css/components/_notifications.scss)
* **Import Manifest**: [`resources/css/app.scss`](file:///c:/Users/palab/.gemini/antigravity-ide/scratch/DOST-S-T-SECTION-PROJECT/resources/css/app.scss)

### 3. Automated Tests
* **Feature Tests**: [`tests/Feature/NotificationsTest.php`](file:///c:/Users/palab/.gemini/antigravity-ide/scratch/DOST-S-T-SECTION-PROJECT/tests/Feature/NotificationsTest.php) (7 feature tests covering auth guard, unread count, filtering, mark as read, mark all read, and toast dispatch).

---

## 🔔 How to Trigger Corner Alerts from Any Component / Backend

The toast system is mounted globally on every authenticated page. Any Livewire component or JavaScript script can trigger corner alerts instantly.

### In Livewire Components (PHP):
```php
// Success (Green)
$this->dispatch('notify', [
    'message' => 'Scholar record updated successfully!',
    'type' => 'green', // 'purple' | 'cyan' | 'green' | 'yellow' | 'red' | 'gray'
    'duration' => 5000, // optional in ms (default: 6000ms)
]);

// Warning (Yellow)
$this->dispatch('notify', [
    'message' => 'Document pending signature verification.',
    'type' => 'yellow',
]);

// Error / Critical (Red)
$this->dispatch('notify', [
    'message' => 'Clearance status is currently Not Cleared.',
    'type' => 'red',
]);

// Info / System (Purple / Cyan / Gray)
$this->dispatch('notify', [
    'message' => 'Admin Name edited document metadata for Fernandez, Gianfranco Miguel D.',
    'type' => 'purple',
]);
```

### In JavaScript:
```javascript
window.dispatchEvent(new CustomEvent('notify', {
    detail: {
        message: 'File upload completed.',
        type: 'green',
        duration: 5000
    }
}));
```

---

## 🎨 Corner Alert Themes Reference

| Theme Key | Background | Border / Accent | Text / Icon Color | Usage Context |
| :--- | :--- | :--- | :--- | :--- |
| `'purple'` | `#f3e8ff` | `#d8b4fe` | `#581c87` / `#4c1d95` | Metadata edits, audit activity, default info |
| `'cyan'` | `#cffafe` | `#67e8f9` | `#0e7490` / `#155e75` | New document uploads, scholar status changes |
| `'green'` | `#dcfce7` | `#86efac` | `#166534` / `#14532d` | Success notifications, clearance approvals |
| `'yellow'` | `#fef9c3` | `#fde047` | `#854d0e` / `#713f12` | Pending actions, warnings, reminders |
| `'red'` | `#fee2e2` | `#fca5a5` | `#991b1b` / `#7f1d1d` | Rejections, non-clearance alerts, errors |
| `'gray'` | `#f1f5f9` | `#cbd5e1` | `#334155` / `#1e293b` | General system messages, maintenance alerts |

---

## 🗄️ Suggested Backend Persistence & Integration

If you wish to store user notifications in a database table rather than relying solely on audit logs/session state:

### Standard Laravel Database Notifications:
Run:
```bash
php artisan make:notifications-table
php artisan migrate
```

Each notification record can store payload in `data` JSON:
```json
{
    "actor": "Admin Name",
    "action_text": "edited document metadata for",
    "target_name": "Fernandez, Gianfranco Miguel D.",
    "type": "purple",
    "link": "/scholars/12"
}
```

The Livewire component `App\Livewire\Notifications\Index` is already prepared with `$notifications`, `unreadCount`, `markAsRead($id)`, and `markAllAsRead()` to bind cleanly with `auth()->user()->unreadNotifications` and `auth()->user()->notifications`.

---

## 🧪 Testing Commands

```bash
# Run Notifications feature tests
php artisan test tests/Feature/NotificationsTest.php

# Run the complete test suite
php artisan test

# Verify PHP code style
vendor/bin/pint --test

# Verify SCSS stylelint
npm run lint:css
```
