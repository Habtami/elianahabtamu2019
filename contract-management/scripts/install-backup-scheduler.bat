@echo off
REM Registers "Contract Management Backup Scheduler" so Laravel's scheduler runs every minute
REM and scheduled backups no longer depend on the backups page being open.
powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0install-backup-scheduler.ps1"
if errorlevel 1 (
    echo Failed to register the task.
    exit /b 1
)
