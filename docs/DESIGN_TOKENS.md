# DOSTorage V1 — Design System & Token Documentation

## Overview
This document serves as the official source of truth for the DOST-S-T design system tokens, color palettes, status indicators, and typography used throughout DOSTorage V1.

---

## 1. Typography
* **Primary Font Family:** `Zalando Sans`
* **Fallback Stack:** `system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif`
* **Local Hosting:** Self-hosted via `@font-face` in `resources/css/app.scss` with zero external online dependencies.

---

## 2. Core Color Palette & Bootstrap Mappings

| Color Token Name | Hex Code | Bootstrap 5 Override Variable | Utility Class |
| :--- | :--- | :--- | :--- |
| **DOST-Dark Blue** | `#052c65` | `$primary` | `.bg-dost-dark-blue`, `.text-dost-dark-blue` |
| **DOST-Blue** | `#0a58ca` | N/A | `.bg-dost-blue`, `.text-dost-blue` |
| **DOST-Main Blue** | `#0099ff` | `$info` | `.bg-dost-main-blue`, `.text-dost-main-blue` |
| **DOST-Yellow** | `#e1ad03` | `$warning` | `.bg-dost-yellow`, `.text-dost-yellow` |
| **Cleared** | `#28a745` | `$success` | `.badge-status-cleared` |
| **Not Cleared** | `#dc3545` | `$danger` | `.badge-status-not-cleared` |

---

## 3. Detailed Color Scale Tokens (`:root` CSS Variables)

### Dark Blue Scale
* `--blue-50`: `#e6eaf0`
* `--blue-100`: `#b2becf`
* `--blue-200`: `#8c9eb8`
* `--blue-300`: `#587298`
* `--blue-400`: `#375684`
* `--blue-500`: `#052c65` (DOST-Dark Blue)
* `--blue-600`: `#05285c`
* `--blue-700`: `#041f48`
* `--blue-800`: `#031838`
* `--blue-900`: `#02122a`

### Main Blue Scale
* `--main-blue-50`: `#e6f5ff`
* `--main-blue-100`: `#b0dfff`
* `--main-blue-200`: `#8ad0ff`
* `--main-blue-300`: `#54bbff`
* `--main-blue-400`: `#33adff`
* `--main-blue-500`: `#0099ff` (DOST-Main Blue)
* `--main-blue-600`: `#008be8`
* `--main-blue-700`: `#006db5`
* `--main-blue-800`: `#00548c`
* `--main-blue-900`: `#00406b`

### Yellow Scale
* `--yellow-50`: `#fcf7e6`
* `--yellow-100`: `#f6e6b1`
* `--yellow-200`: `#f1d98b`
* `--yellow-300`: `#ebc856`
* `--yellow-400`: `#e7bd35`
* `--yellow-500`: `#e1ad03` (DOST-Yellow)
* `--yellow-600`: `#cd9d03`
* `--yellow-700`: `#a07b02`
* `--yellow-800`: `#7c5f02`
* `--yellow-900`: `#5f4901`

### Neutral Grays
* `--gray-100`: `#f8f9fa`
* `--gray-200`: `#e9ecef`
* `--gray-300`: `#dee2e6`
* `--gray-400`: `#ced4da`
* `--gray-500`: `#adb5bd`
* `--gray-600`: `#6c757d`
* `--gray-700`: `#495057`
* `--gray-800`: `#343a40`
* `--gray-900`: `#212529`

---

## 4. Usage Rules
1. **Prefer Bootstrap theme utilities** (`bg-primary`, `btn-primary`, `text-primary`, `navbar-dark bg-primary`) as they map directly to DOST-Dark Blue.
2. **Use custom DOST classes** (`bg-dost-yellow`, `badge-status-cleared`, etc.) for specific scholar status badges or DOST brand accents.
