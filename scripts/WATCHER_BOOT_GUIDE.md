# DOSTorage Tasks Watcher – Boot Registration Guide

This guide registers `tasks_watcher_service.bat` in Windows Task Scheduler so the watcher starts automatically at boot.
It is split into two parts: elevation and registration.

**Files**
- Watcher service: `C:\Users\Asus\Documents\Personal\Programs\DOSTorage\scripts\tasks_watcher_service.bat`
- Task schema: `C:\Users\Asus\Documents\Personal\Programs\DOSTorage\scripts\tasks_watcher_service.xml`
- Python: `C:\Users\Asus\AppData\Local\Programs\Python\Python311\python.exe`

---

## 1. Elevate once

The watcher itself does **not** need administrator privileges, but adding a **boot-triggered** task to Task Scheduler does.
Use one of these methods:

- Option A – Explorer: right-click `C:\Users\Asus\Documents\Personal\Programs\DOSTorage\scripts\tasks_watcher_service.bat` and choose **Run as administrator** once if you want to install the task interactively via `schtasks.exe`.
- Option B – schtasks requires an elevated command prompt.
  Open **Start menu** → type `cmd` → **Run as administrator**.
  Then run the commands in section 2 from that elevated shell.

> If your shell is not elevated, the commands below will fail with `ERROR: Access is denied.`
> Do not attempt privileged registration from a non-elevated shell.

---

## 2. Register the task

From an **elevated** Command Prompt or elevated Git Bash:

```powershell
schtasks.exe /create /tn "DOSTorageTasksWatcher" /xml "C:\Users\Asus\Documents\Personal\Programs\DOSTorage\scripts\tasks_watcher_service.xml"
```

Expected result:

```
SUCCESS: The scheduled task "DOSTorageTasksWatcher" has been created.
```

To verify:

```powershell
schtasks.exe /query /tn "DOSTorageTasksWatcher" /fo list /v
```

To delete if needed:

```powershell
schtasks.exe /delete /tn "DOSTorageTasksWatcher" /f
```

---

## 3. What happens at boot

- The XML triggers the `.bat` at boot using `InteractiveToken` logon.
- The `.bat` starts `tasks_watcher.py` with the system Python 3.11.
- The watcher monitors `C:\Users\Asus\Documents\Personal\Programs\DOSTorage\planning\` for `team_*.csv` and `dostorage-v1-project-checklist.md`.

---

## 4. Notification + Gantt behavior in this guide

The watcher after this change does two extra things when the tasks refresh succeeds:

1. If `scripts\generate_gantt.py` exists, it runs it.
2. If any webhook is configured, it posts a success notification after the refresh.

Do **not** store secrets in code or XML. Use one of:

- Env vars in the bat:
  ```bat
  set WEBHOOK_SLACK_URL=https://...
  set WEBHOOK_DISCORD_URL=https://...
  set WEBHOOK_BELL_TOKEN=...
  set WEBHOOK_BELL_CHAT_ID=...
  ```
- `scripts\webhooks.json` with `.gitignore` permissions.

---

## 5. Troubleshooting

- **Task not running:** confirm task history in Task Scheduler → **History** tab for `DOSTorageTasksWatcher`.
- **Python not found:** confirm `C:\Users\Asus\AppData\Local\Programs\Python\Python311\python.exe` exists.
- **Bible keeper not found:** confirm `bible_keeper.py` exists at the hardcoded path in `tasks_watcher.py`.
- **Watcher dies silently:** the `.bat` uses `start`, so console output is not captured. Add logging if you need stdout/stderr:
  ```bat
  start "DOSTorage Tasks Watcher" /B cmd /c ""%PYTHON%" "%WATCHER%" 1>>"%USERPROFILE%\watcher.log" 2>&1"
  ```
- **Webhooks always skipped:** set only one provider at a time, confirm URL/token is valid, check outbound internet, and verify `webhooks.json` is valid JSON.
