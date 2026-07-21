# Elevated PowerShell script to register the DOSTorage Tasks Watcher as a boot task.
# Usage: Right-click → Run with PowerShell (Administrator), or from an elevated shell:
#   powershell -ExecutionPolicy Bypass -File C:\Users\Asus\Documents\Personal\Programs\DOSTorage\scripts\register_watcher_boot_task.ps1

<#
.SYNOPSIS
Registers tasks_watcher_service.xml as a Windows Task Scheduler boot task.

.DESCRIPTION
Reads tasks_watcher_service.xml from the DOSTorage scripts directory and imports
it into the local machine task scheduler, configured to run at boot under the
current interactive user token.

.NOTES
Requires: Elevated PowerShell (Run as Administrator).
Task path: \DOSTorage\
#>

param()

$ErrorActionPreference = 'Stop'

# ---------------------------------------------------------------------------
# 1. Verify elevation
# ---------------------------------------------------------------------------
$currentPrincipal = New-Object Security.Principal.WindowsPrincipal(
    [Security.Principal.WindowsIdentity]::GetCurrent()
)
if (-not $currentPrincipal.IsInRole(
    [Security.Principal.WindowsBuiltInRole]::Administrator)) {
    Write-Host "ERROR: This script must be run as Administrator." -ForegroundColor Red
    exit 1
}

# ---------------------------------------------------------------------------
# 2. Locate files
# ---------------------------------------------------------------------------
$scriptDir  = Split-Path -Parent $MyInvocation.MyCommand.Path
$xmlPath    = Join-Path $scriptDir 'tasks_watcher_service.xml'
$batPath    = Join-Path $scriptDir 'tasks_watcher_service.bat'

if (-not (Test-Path $xmlPath)) {
    Write-Host "ERROR: XML task definition not found: $xmlPath" -ForegroundColor Red
    exit 1
}
if (-not (Test-Path $batPath)) {
    Write-Host "ERROR: BAT launcher not found: $batPath" -ForegroundColor Red
    exit 1
}

Write-Host "XML: $xmlPath" -ForegroundColor Cyan
Write-Host "BAT: $batPath" -ForegroundColor Cyan

# ---------------------------------------------------------------------------
# 3. Quick sanity check: <Command> must point to the BAT launcher
# ---------------------------------------------------------------------------
$xml = [xml](Get-Content -Raw -Path $xmlPath)
$command = $xml.Task.Actions.Exec.Command
if (-not $command.EndsWith('tasks_watcher_service.bat', [StringComparison]::OrdinalIgnoreCase)) {
    Write-Host "WARNING: XML <Command> does not point to tasks_watcher_service.bat." -ForegroundColor Yellow
    Write-Host "  Current: $command" -ForegroundColor Yellow
    $proceed = Read-Host "Proceed anyway? (Y/N)"
    if ($proceed -notmatch '^[Yy]$') { exit 1 }
} else {
    Write-Host "Command target verified: $command" -ForegroundColor Green
}

# ---------------------------------------------------------------------------
# 4. Register / update the scheduled task
# ---------------------------------------------------------------------------
$taskName  = 'DOSTorage Tasks Watcher'
$taskPath  = '\DOSTorage\'
$userName  = [Security.Principal.WindowsIdentity]::GetCurrent().Name

Write-Host "Registering boot task '$taskName' for user $userName ..." -ForegroundColor Cyan

# Use /XML so we keep the exact definition from the XML file.
# /F forces overwrite if it already exists.
$proc = Start-Process -FilePath 'schtasks.exe' -ArgumentList @(
    '/Create',
    '/TN',  "$taskPath$taskName",
    '/XML', $xmlPath,
    '/F'
) -NoNewWindow -Wait -PassThru -RedirectStandardError 'C:\Users\Asus\AppData\Local\hermes\scripts\register_watcher_register.err'

if ($proc.ExitCode -ne 0) {
    $err = Get-Content 'C:\Users\Asus\AppData\Local\hermes\scripts\register_watcher_register.err' -Raw
    Write-Host "ERROR: schtasks /Create failed (exit $($proc.ExitCode))" -ForegroundColor Red
    Write-Host $err
    Remove-Item 'C:\Users\Asus\AppData\Local\hermes\scripts\register_watcher_register.err' -Force -ErrorAction SilentlyContinue
    exit 1
}
Remove-Item 'C:\Users\Asus\AppData\Local\hermes\scripts\register_watcher_register.err' -Force -ErrorAction SilentlyContinue

# ---------------------------------------------------------------------------
# 5. Verify
# ---------------------------------------------------------------------------
$out = schtasks.exe /Query /TN "$taskPath$taskName" /FO LIST /V 2>&1
if ($LASTEXITCODE -eq 0) {
    Write-Host "Boot task registered successfully." -ForegroundColor Green
    Write-Host $out
} else {
    Write-Host "ERROR: Verification query failed." -ForegroundColor Red
    Write-Host $out
    exit 1
}

Write-Host "`nNext steps:" -ForegroundColor Cyan
Write-Host "  1. Reboot to confirm the watcher auto-starts."
Write-Host "  2. Run: schtasks.exe /Run /TN `"$taskPath$taskName`" to test immediately."
