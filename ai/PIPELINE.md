# Development Pipeline

> **The Rule:** No AI may write code until another AI has reviewed the design.
> **The Exception:** The **Orchestrator Agent** can evaluate any request and dynamically bypass steps or customize the pipeline to match the task's complexity.

---

## Dynamic Adaptation & Growth Loop

The Orchestrator is designed to grow along with your team and skills:

1. **Auto-Discovery of Agents:** The Orchestrator scans `ai/PROJECT_BIBLE.md` and the workspace environment to discover available agents. As you add new agents (e.g., performance tuning, automated testing, UI design), the Orchestrator immediately learns their capabilities and weaves them into the appropriate lifecycle stage.
2. **Post-Pipeline Retrospectives:** After a feature is merged, the Orchestrator evaluates the performance of the pipeline. If a bottleneck occurred or a quality gap was found, it adjusts future pathways and suggests prompt updates or new agent specializations.

---

## Pipeline Selection & Bypassing


The Orchestrator chooses or designs the best pipeline for each specific task to balance safety, quality, and velocity.

### 1. The Learning/Bootcamp Pipeline (For Weeks 1-2)
*Use when completing exercises or studying.*
- **Steps:** Learn Concept → Write Code → Ask Mentor to Review.
- **Bypasses:** Product Owner, Architect, Database, QA, Security, Code Reviewer, Documentation.

### 2. The Quick Fix / Patch Pipeline
*Use for minor bug fixes, config tweaks, typo fixes, or small refactors.*
- **Steps:** Laravel Implementer (code) → QA (regression test) → Code Reviewer (sanity check) → Merge.
- **Bypasses:** Product Owner, Architect, Database, Security, Documentation.

### 3. The Database-Only Pipeline
*Use when modifying indexes, adding seeders, or database schema-only updates.*
- **Steps:** Solution Architect (design) → Database Reviewer (schema verify) → Implementer (migration/seeder) → Merge.
- **Bypasses:** Product Owner, QA, Security, Code Reviewer.

### 4. The Full Feature Pipeline
*Use for complex features (e.g., Scholar Document Upload, Spatie Role assignment).*
- **Steps:** Product Owner → Architect → Database Reviewer → Implementer → QA → Security → Code Reviewer → Human Merge → Documentation.
- **Bypasses:** None. (Requires the full quality and security assurance pipeline).

---

## Feature Development Pipeline Reference (Full Feature)

```
Product Owner (Scope/User Story) → Solution Architect (Design) → Database Reviewer (Schema Check) → Laravel Implementer (Code) → QA Engineer (Tests) → Security Reviewer (Audit) → Code Reviewer (Code Quality) → Human Review (Merge) → Documentation Agent (Docs)
```

| Task | Agent to Invoke |
|------|-----------------|
| Plan feature & write user stories | Product Owner |
| Design application structure & database schemas | Solution Architect |
| Audit migrations & database changes | Database Reviewer |
| Write logic, models, controllers, templates | Laravel Implementer |
| Write feature/unit tests, check edge cases | QA Engineer |
| Audit security, check inputs, file upload MIME | Security Reviewer |
| Review code conventions, SOLID, DRY principles | Code Reviewer |
| Resolve conceptual questions, teach frameworks | Senior Mentor |
| Manage Docker configuration and deployment | DevOps Engineer |
| Write manuals, API docs, update schemas docs | Documentation Agent |

---

## Pipeline for Bug Fixes

```
Bug Report → Reproduce → Root Cause → Fix → Test → Review → Merge
                ↑                              ↑        ↑
           QA Agent                        QA Agent  Code Reviewer
```

---

## Pipeline for Bootcamp Exercises

During the bootcamp (Weeks 1-2), the full pipeline is overkill. Use this simplified flow:

```
Learn Concept → Write Exercise → Ask Mentor to Review → Iterate
                                      ↑
                              Senior Mentor Agent
```

---

## Pipeline for Capstone Project (Days 12-14)

Use the full pipeline for the capstone. This is your rehearsal for production development:

```
User Story → Architecture → Database → Code → Test → Review → Merge
```
