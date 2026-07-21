# Checklist

## First PR
- [ ] `docker compose config --quiet` passes
- [ ] `scripts/start.sh` completes from clean clone
- [ ] `scripts/test.sh` passes
- [ ] `/health` returns `{"status":"ok"}`
- [ ] No `.env` or `public/build` in staged diff
- [ ] `vendor/bin/pint` passes
- [ ] Branch protection review-only behavior confirmed

## Runtime
- [ ] Dockerfile builds
- [ ] MySQL healthcheck passes
- [ ] Migrations + seeders run
- [ ] APP_KEY is regenerated separately for each environment

## Repo hygiene
- [ ] README points to docs and planning
- [ ] CODEOWNERS maps ownership paths/usernames
- [ ] dependabot enabled for composer/npm/actions
