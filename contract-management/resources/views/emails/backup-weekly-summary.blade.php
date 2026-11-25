<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #64748b; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background-color: #f9f9f9; }
        .count { font-size: 40px; font-weight: bold; color: #495057; text-align: center; margin: 10px 0; }
        .period { text-align: center; color: #6c757d; font-size: 13px; }
        .footer { text-align: center; padding: 20px; color: #777; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Weekly Backup Summary</h2>
        </div>
        <div class="content">
            <div class="count">{{ $skippedCount }}</div>
            <p>{{ $skippedCount }} scheduled backup(s) were skipped in the last 7 days because the database had not changed.</p>
            <p class="period">{{ $periodStart }} — {{ $periodEnd }}</p>
            <p>Smart Mode saves disk space and inbox noise by only storing and emailing a backup when data actually changed. Manual backups always run.</p>
        </div>
        <div class="footer">
            <p>EPSS Contract Management System — automated message, please do not reply.</p>
        </div>
    </div>
</body>
</html>
