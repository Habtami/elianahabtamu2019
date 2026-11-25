# Registers "Contract Management Backup Scheduler" so Laravel's scheduler ticks every minute.
# Runs as NT AUTHORITY\SYSTEM with highest privileges, so backups fire even when no user is
# logged on (no stored password needed) and the SMTP notification still has network access.
# The job is launched through wscript so no console window pops up on screen every minute.
# The script self-elevates: double-clicking install-backup-scheduler.bat shows a UAC prompt.

$ErrorActionPreference = 'Stop'

$taskName = 'Contract Management Backup Scheduler'
$project  = 'C:\wamp64\www\Contract_mangemnet\contract-management'
$vbs      = Join-Path $project 'scripts\backup-scheduler.vbs'
$logDir   = Join-Path $project 'storage\logs'
$logFile  = Join-Path $logDir 'task-install.log'

function Write-Log([string] $message) {
    if (-not (Test-Path $logDir)) {
        New-Item -ItemType Directory -Path $logDir -Force | Out-Null
    }
    Add-Content -Path $logFile -Value ('[{0}] {1}' -f (Get-Date -Format 'yyyy-MM-dd HH:mm:ss'), $message)
}

$identity  = [Security.Principal.WindowsIdentity]::GetCurrent()
$isAdmin   = (New-Object Security.Principal.WindowsPrincipal($identity)).IsInRole(
    [Security.Principal.WindowsBuiltInRole]::Administrator
)

if (-not $isAdmin) {
    Write-Log 'Not elevated; relaunching with a UAC prompt.'
    $arguments = @('-NoProfile', '-ExecutionPolicy', 'Bypass', '-File', ('"{0}"' -f $PSCommandPath))
    $elevated = Start-Process -FilePath 'powershell.exe' -ArgumentList $arguments -Verb RunAs -Wait -PassThru
    exit $elevated.ExitCode
}

try {
    $action = New-ScheduledTaskAction -Execute 'wscript.exe' -Argument ('"{0}"' -f $vbs)

    # Fire now (and every minute from now on) even when nobody is signed in...
    $triggerNow = New-ScheduledTaskTrigger -Once -At (Get-Date).AddMinutes(1) `
        -RepetitionInterval (New-TimeSpan -Minutes 1) -RepetitionDuration (New-TimeSpan -Days 3650)
    # ...and keep ticking after every reboot.
    $triggerBoot = New-ScheduledTaskTrigger -AtStartup
    $triggerBoot.Repetition = $triggerNow.Repetition

    $principal = New-ScheduledTaskPrincipal -UserId 'NT AUTHORITY\SYSTEM' `
        -LogonType ServiceAccount -RunLevel Highest

    $settings = New-ScheduledTaskSettingsSet -AllowStartIfOnBatteries -DontStopIfGoingOnBatteries `
        -StartWhenAvailable -MultipleInstances IgnoreNew -ExecutionTimeLimit (New-TimeSpan -Hours 1)

    Register-ScheduledTask -TaskName $taskName -Action $action `
        -Trigger @($triggerNow, $triggerBoot) -Settings $settings -Principal $principal -Force | Out-Null

    $task = Get-ScheduledTask -TaskName $taskName
    Write-Log ("Registered '{0}' ({1}) as {2}" -f $task.TaskName, $task.State, $task.Principal.UserId)
    Write-Host ("Registered '{0}' ({1}) as {2}" -f $task.TaskName, $task.State, $task.Principal.UserId)
    Write-Host 'Remove it later with: Unregister-ScheduledTask -TaskName "Contract Management Backup Scheduler" -Confirm:$false'
} catch {
    Write-Log ('Failed to register the task: ' + $_.Exception.Message)
    Write-Error ('Failed to register the task: ' + $_.Exception.Message)
    exit 1
}
