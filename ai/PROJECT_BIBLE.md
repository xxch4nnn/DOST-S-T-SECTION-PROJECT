# DOST Scholarship Records Digitization System — Project Bible

> **Last Updated:** 2026-07-14
> **Status:** Bootcamp Phase (Pre-Development)
> **Owner:** Backend Developer (You)

---

## 1. Project Overview

**What:** A web-based document management system for digitizing and managing DOST (Department of Science and Technology) scholarship records.

**Why:** Replace manual paper-based record-keeping with a searchable, role-based digital system.

**Who Uses It:**
- **Admin** — Full system access, manages users and roles
- **Encoder** — Uploads and edits scholar records and documents
- **Records Officer** — Searches, views, and generates reports

---

## 2. Tech Stack

| Layer | Technology | Version |
|-------|-----------|---------|
| Language | PHP | 8.3.6 |
| Framework | Laravel | 12.x (latest) |
| Database | MySQL | 8.0.39 |
| ORM | Eloquent | (bundled) |
| Auth | Laravel Breeze | Livewire stack |
| Authorization | Spatie Laravel-Permission | latest |
| Frontend | Livewire + Blade + Bootstrap | — |
| Package Manager | Composer | 2.10.2 |
| Version Control | Git | 2.45.1 |
| Containerization | Docker | 28.4.0 |
| IDE | Antigravity IDE (VS Code fork) | — |

---

## 3. Core Entities

| Entity | Description |
|--------|-------------|
| **User** | System user (Admin, Encoder, Records Officer) |
| **Role** | User role via Spatie (Admin, Encoder, Records Officer) |
| **Permission** | Granular permissions (upload, delete, search, etc.) |
| **Scholar** | DOST scholarship recipient |
| **Document** | Uploaded PDF file belonging to a scholar |

---

## 4. Key Business Rules

1. Every scholar has a unique **SPAS number** (Scholarship Program Assessment System)
2. Documents are **PDFs only**, stored in private storage (not publicly accessible)
3. Only authenticated users can access the system
4. **Encoders** can upload and edit, but cannot delete or manage users
5. **Records Officers** can search and download, but cannot upload or edit
6. **Admins** have full access
7. Each scholar can have multiple document types: Agreement, TOR, Prospectus, Endorsement, etc.
8. All actions should be auditable (who did what, when)

---

## 5. MVP Features (Must Have)

- [ ] User authentication (login/logout)
- [ ] Role-based access control (Spatie)
- [ ] Scholar CRUD (Create, Read, Update, Delete)
- [ ] Document upload (PDF, validated)
- [ ] Search by SPAS number or scholar name
- [ ] Document download (authorized users only)
- [ ] Basic dashboard (scholar count, document count)

---

## 6. Constraints

- Solo developer (you) during bootcamp
- 2-week timeline for learning, then team development starts
- Must work with existing MySQL 8.0 server
- Must eventually run in Docker for deployment
- Team will use Git for collaboration

---

## 7. Decisions Log

| Date | Decision | Rationale |
|------|----------|-----------|
| 2026-07-14 | PHP via WAMP PATH, not Scoop | Already installed, faster setup |
| 2026-07-14 | Livewire stack (not Inertia) | Server-driven, less JavaScript |
| 2026-07-14 | Dedicated MySQL user | Security: `laravel_dev` has access to `laravel_bootcamp` only |
| 2026-07-14 | Agentic engineering workflow | 10 specialized agents with defined pipeline |

---

## 8. Current Phase

**Phase 0: Bootcamp (Weeks 1-2)**
- Week 1: PHP, SQL, Git foundations
- Week 2: Laravel, Eloquent, Breeze, Spatie, File Uploads, Livewire, Docker
- Days 12-14: Capstone mini-project

**Phase 1: DOST System Development (Weeks 3+)**
- Full team development begins
- Architecture review with team's database architect
- Production features built following agentic pipeline

---

## 9. Agent Responsibilities

| Agent | Tool | Responsibility |
|-------|------|----------------|
| Solution Architect | Antigravity | System design, ADRs, ERDs |
| Senior Mentor | ChatGPT/Antigravity | Teaching, concept explanations |
| Laravel Implementer | Antigravity/Copilot | Write production code |
| QA Engineer | Antigravity | Tests and edge cases |
| Documentation | Antigravity | All documentation |
| Security Reviewer | Antigravity | Vulnerability audits |
| Database Reviewer | Antigravity | Migration & schema reviews |
| Code Reviewer | Antigravity | Code quality reviews |
| DevOps Engineer | Antigravity | Docker, deployment |
| Product Owner | Antigravity | Scope, priorities, user stories |
