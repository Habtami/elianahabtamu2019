' Runs the Laravel scheduler tick without flashing a console window.
Dim shell
Set shell = CreateObject("WScript.Shell")
shell.Run "cmd.exe /c ""C:\wamp64\www\Contract_mangemnet\contract-management\scripts\backup-scheduler.bat""", 0, False
