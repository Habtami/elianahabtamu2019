<?php

return [
    /*
     | Skip a backup when the database fingerprint is unchanged since the
     | newest stored backup. Scheduled runs obey smart_mode; manual runs from
     | the Backups page always check and report "no changes" instead of
     | stacking identical files.
     */
    'smart_mode' => env('BACKUP_SMART_MODE', true),

    'hash_algorithm' => 'sha256',

    /*
     | Folder where dumps are written when storage/app/backup_settings.json has
     | no destination_path yet. The Backups page can override it at runtime;
     | an empty value falls back to storage/app/backups.
     */
    'destination_path' => env('BACKUP_DESTINATION_PATH', 'D:\\a_contract_backup'),

    /*
     | Scheduled times are the operator's wall-clock time (EAT) while the
     | application runs on UTC, so the scheduler events are pinned here.
     */
    'timezone' => env('BACKUP_TIMEZONE', 'Africa/Addis_Ababa'),

    /*
     | When a scheduled run is skipped, admins get one summary email instead
     | of a per-run notification. Both values are read by routes/console.php.
     */
    'weekly_summary_day' => env('BACKUP_WEEKLY_SUMMARY_DAY', 'sunday'),
    'weekly_summary_time' => env('BACKUP_WEEKLY_SUMMARY_TIME', '23:30'),

    /*
     | Optional storage hygiene: gzip completed dumps and drop duplicate
     | copies of the same fingerprint once they are older than the cutoff.
     */
    'compress' => env('BACKUP_COMPRESS', false),
    'cleanup_duplicates_after_days' => (int) env('BACKUP_CLEANUP_DUPLICATES_AFTER_DAYS', 30),

    /*
     | Tables that churn without representing business data: logging, sessions,
     | queues and tokens. Including them would make every run look like a
     | change (the skip itself writes to activity_logs).
     */
    'excluded_tables' => [
        'activity_logs',
        'backups',
        'sessions',
        'cache',
        'cache_locks',
        'jobs',
        'job_batches',
        'failed_jobs',
        'password_reset_tokens',
        'personal_access_tokens',
        'notifications',
    ],
];
