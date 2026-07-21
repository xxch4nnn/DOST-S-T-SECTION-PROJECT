# Hour 1 — Local runtime scaffold + validation
- [ ] Add `Dockerfile`
- [ ] Validate `compose.yaml`
- [ ] Add `scripts/start.sh`
- [ ] Add `scripts/test.sh`
- [ ] Add `/health` route
- [ ] Verify `docker compose up -d` and scripts

# Hour 2 — CI/QA hardening + checklists
- [ ] Confirm `.github/workflows/test.yml`
- [ ] Add backend/runtime checklist in `docs/checklist.md`
- [ ] Document exact pre-push checklist
- [ ] Confirm branch protection/PR template workflow

## Done when
- `docker compose up -d` boots cleanly
- `scripts/start.sh` ends with a healthy application response
- `scripts/test.sh` passes PHPUnit suite
- Branch protection enforced without failing first PR
