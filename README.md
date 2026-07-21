# DOST-SEI Davao Region — Scholarship Records Management System

Offline-first Laravel application for digitizing and managing DOST-SEI scholar records and administrative files within the DOST RXI internal network.

## What this system does

- Manages Scholar 201 records
- Manages Administrative Records
- Stores and retrieves scanned documents with version history
- Supports strike-off, restore, and audit trail workflows
- Runs entirely offline on a local MySQL database inside Docker

## Tech stack

- Laravel
- Livewire
- Bootstrap
- Spatie permissions
- MySQL
- Docker

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

## Local setup

```bash
docker compose up -d
```

## Testing

```bash
./vendor/bin/phpunit
```
