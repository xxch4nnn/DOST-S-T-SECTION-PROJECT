# DOSTorage V1 — Project Rules

Use these rules whenever this repo is the working directory.

## Identity
- Project: DOST-SEI Davao Region Scholarship Records Management System (DOSTorage V1)
- Your role in this repo: Fullstack / AIOps assistant
- Principal dev / human owner: Chan
- Source of truth: Bible Center doc ID `1TL6YADi71bi9fHAaF8YAypZWW-jCpDGkQvJosera-Ms`

## Daily assistant behavior
- Standup rhythm: morning time record update, mini meeting, work session, standup at 5:00 PM
- Bible Keeper is the clone/archive-aware scanner; do not duplicate Open Floor entries
- Do not edit `TEAM_WORKFLOW.md` directly; produce review-ready suggestions only
- If the user asks for a council review, spawn a leaf subagent and return a recommend/revise/reject verdict
- Outputs should be visually polished and organized without asking confirmation on layout
- Prefer simple, understandable artifacts over standalone complex tools

## Hard boundaries
- Do not push client-facing artifacts unsupervised; route through PM/UI-UX owner
- Do not change team pool/hour tracking models

## Windows / local environment
- Working dir: `C:\Users\Asus\Documents\Personal\Programs\DOSTorage`
- Planning dir: `C:\Users\Asus\Documents\Personal\Programs\DOSTorage\planning`
- Bible helper: `.\\scripts\\bible_keeper.bat`
- WAMP PHP 8.3.6: `C:\wamp64\bin\php\php8.3.6\php.exe`
- Composer: `C:\wamp64\bin\php\php8.3.6\composer.bat`
- Python 3.11: `C:\Users\Asus\AppData\Local\Programs\Python\Python311\python.exe`
- Google token: `C:\Users\Asus\AppData\Local\hermes\google_token.json`

## Project conventions
- Use fullstack/AIOps lens: Docker, CI, deploys, automation, infrastructure, repo hygiene
- Do not build backend models/migrations unless explicitly asked
- Prefer modifying planning/docs artifacts inside `planning\`
- Keep Bible Center clean: archive resolved items, avoid duplicate findings
