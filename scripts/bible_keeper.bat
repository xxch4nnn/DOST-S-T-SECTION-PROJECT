@echo off
set "PYTHON=C:\Users\Asus\AppData\Local\Programs\Python\Python311\python.exe"
set "SKILL=C:\Users\Asus\AppData\Local\hermes\skills\project-management\project-bible-keeper\scripts\bible_keeper.py"
if not exist "%PYTHON%" (
echo Python not found at %PYTHON%
pause
exit /b 1
)
if not exist "%SKILL%" (
echo Keeper script not found at %SKILL%
pause
exit /b 1
)
"%PYTHON%" "%SKILL%" %*
