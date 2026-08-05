# Open PR review — mother repo (2026-08-04)

Baseline: `origin/master` @ `142b90f` (PR #28 merged).  
All six open PRs are Dependabot; CI green (lint / lint-css / test / backup-restore-smoke).

## Verdict summary

| PR | Title | Verdict | Action |
|----|-------|---------|--------|
| [#29](https://github.com/xxch4nnn/DOST-S-T-SECTION-PROJECT/pull/29) | actions/setup-node 4 → 7 | **Approve / merge** | Safe Actions bump; CI green |
| [#30](https://github.com/xxch4nnn/DOST-S-T-SECTION-PROJECT/pull/30) | sass 1.101.3 → 1.102.0 | **Approve / merge** | Patch; CI green |
| [#31](https://github.com/xxch4nnn/DOST-S-T-SECTION-PROJECT/pull/31) | concurrently 9 → 10 | **Hold** | Major; Node ≥22 / ESM — smoke `composer dev` / npm scripts before merge |
| [#32](https://github.com/xxch4nnn/DOST-S-T-SECTION-PROJECT/pull/32) | laravel/sail 1.63 → 1.64 | **Approve / merge** | Dev-only; CI green |
| [#33](https://github.com/xxch4nnn/DOST-S-T-SECTION-PROJECT/pull/33) | laravel/framework 13.21 → 13.23 | **Approve / merge** | Patch/minor within L13; CI green |
| [#34](https://github.com/xxch4nnn/DOST-S-T-SECTION-PROJECT/pull/34) | livewire/volt 1.10.5 → 1.11.1 | **Approve / merge** | Minor; CI green |

## Not an open PR (but reviewed)

| Ref | Verdict |
|-----|---------|
| `origin/db-integration` @ `c7ffec9` | **Reject merge** — ships flat `files` table, absolute Windows `FileSeeder` paths, `ScholarshipProgram*` parallel naming. Cherry-pick UI later after remediation. |
| Merged #28 FS-08 | Good baseline for stitch |
| Merged #24 policies, #15 UI, #14 auth | Already on master |

## Stitch PR (incoming)

Open from `feat/be-stitch-backend-to-mother`: plan + changelog institutionalization + additive taxonomy (PR-A/C). Must stay free of `files` / DomPDF / FileObserver.
