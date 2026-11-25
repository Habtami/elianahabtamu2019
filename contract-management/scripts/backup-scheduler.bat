@echo off
REM Runs the Laravel scheduler every minute for server-side backups.
REM Registered as the "Contract Management Backup Scheduler" Windows Task Scheduler job.
setlocal
set "PROJECT=C:\wamp64\www\Contract_mangemnet\contract-management"
set "PHP=C:\wamp64\bin\php\php8.4.15\php.exe"
if not exist "%PHP%" set "PHP=php"
"%PHP%" "%PROJECT%\artisan" schedule:run --quiet 2>> "%PROJECT%\storage\logs\scheduler-error.log"
endlocal
