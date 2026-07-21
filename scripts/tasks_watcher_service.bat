@echo off
REM tasks_watcher_service.bat
REM Starts the tasks_watcher.py daemon with the system Python.
REM Run this at startup via Windows Task Scheduler for true daemonization.

set "PYTHON=C:\Users\Asus\AppData\Local\Programs\Python\Python311\python.exe"
set "WATCHER=C:\Users\Asus\Documents\Personal\Programs\DOSTorage\scripts\tasks_watcher.py"

REM Explicit Project Bible IDs so the watcher and its --tasks subprocess
REM always resolve the correct doc/tab even when launched by Task Scheduler.
set "PROJECT_BIBLE_DOC_ID=1TL6YADi71bi9fHAaF8YAypZWW-jCpDGkQvJosera-Ms"
set "PROJECT_TASKS_TAB_ID=t.cm79ati3cwhz"
set "PROJECT_MEETINGS_TAB_ID=t.2776ikdxmxv2"

start "DOSTorage Tasks Watcher" /B "%PYTHON%" "%WATCHER%"
