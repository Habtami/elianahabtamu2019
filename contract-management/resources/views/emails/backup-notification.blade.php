<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #1C75BC; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background-color: #f9f9f9; }
        .details { margin: 20px 0; }
        .details table { width: 100%; border-collapse: collapse; }
        .details th, .details td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        .details th { width: 40%; color: #555; }
        .footer { text-align: center; padding: 20px; color: #777; font-size: 12px; }
        .success-badge { display: inline-block; padding: 5px 10px; background-color: #198754; color: white; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Database Backup Completed</h2>
        </div>
        <div class="content">
            <p>Hello,</p>

            <p>A database backup of the EPSS Contract Management System was created successfully.</p>

            <div class="details">
                <table>
                    <tr>
                        <th>File name:</th>
                        <td>{{ $filename }}</td>
                    </tr>
                    <tr>
                        <th>Backup time:</th>
                        <td>{{ $backupTime }}</td>
                    </tr>
                    <tr>
                        <th>Backup type:</th>
                        <td><span class="success-badge">{{ ucfirst($backupType) }}</span></td>
                    </tr>
                    <tr>
                        <th>Size:</th>
                        <td>{{ $backupSize }}</td>
                    </tr>
                    <tr>
                        <th>Created by:</th>
                        <td>{{ $createdBy }}</td>
                    </tr>
                    <tr>
                        <th>Destination:</th>
                        <td>{{ $destinationPath ?: 'Storage only (storage/app/backups)' }}</td>
                    </tr>
                </table>
            </div>

            <p>The backup is available from the Database Backups page of the system.</p>
        </div>
        <div class="footer">
            <p>EPSS Contract Management System — automated message, please do not reply.</p>
        </div>
    </div>
</body>
</html>
