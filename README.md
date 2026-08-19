# DOST-SEI Davao Region — Scholarship Records Management System

Offline-first Laravel application for digitizing and managing DOST-SEI scholar records and administrative files within the DOST RXI internal network.

## What this system does

- Manages Scholar 201 records
- Manages Administrative Records
- Stores and retrieves scanned documents with version history
- Supports strike-off, restore, and audit trail workflows
- Runs entirely offline on a local MySQL database inside Docker

## Tech stack

- Laravel 13 · Livewire 4 · Bootstrap 5.3 · Spatie Permission v8 · MySQL 8.4 · Docker · PHP 8.3
- **Official docs (version-pinned for agents):** [`docs/TECH_STACK_DOCS.md`](docs/TECH_STACK_DOCS.md)

## V1 scope

V1 covers Scholar 201 Records and Administrative Records only. Financial Ledger is future scope.

## Key behavior rules

- Documents are never hard-deleted through the application.
- Standard delete is a soft-delete strike-off with undo.
- Super Admin-only permanent delete is performed directly against the database/server, not through normal UI.
- Maximum upload size is 10 MB.
- Accepted file types: PDF, PNG, JPG, JPEG.

## Documentation

See `planning/` for current project docs, task board, team workflow, and MCP server registry.  
Stack API reference for coding agents: [`docs/TECH_STACK_DOCS.md`](docs/TECH_STACK_DOCS.md).

## Local setup

```bash
docker compose up -d
```

## Testing

```bash
./vendor/bin/phpunit
```

## Third-Party Licenses

### philippines-province-city-barangay-database
* **Author:** Aimer Sherdan Ong
* **Source:** [https://github.com/skyvstigreo/philippines-province-city-barangay-database.git](https://github.com/skyvstigreo/philippines-province-city-barangay-database.git)
* **License:** [MIT License

Copyright (c) 2021 Aimer Sherdan Ong

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.]
