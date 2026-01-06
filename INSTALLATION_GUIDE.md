# Contract Management System - Installation Guide

A Laravel-based contract management system to track contracts, performance guarantees, end dates, alerts, and extension history.

## Features

- **Contract Management**: Create, read, update, and delete contracts
- **Performance Guarantee Tracking**: Track performance guarantee amounts and end dates
- **Dynamic Alert System**: Set custom alert periods for both contract and performance guarantee end dates
- **Extension Tracking**: Track and count all extensions/edits to contract end dates and performance guarantees
- **Edit History**: Complete audit trail of all changes with reasons and timestamps
- **Alert Dashboard**: Visual dashboard showing all contracts requiring attention
- **Email Alerts**: Command-line tool to check and send alert notifications

## Installation Steps

### Step 1: Set Up Laravel Project

If the Laravel project doesn't exist yet or composer install failed:

```bash
cd "D:\Contract mangemnet"
composer create-project laravel/laravel contract-management
cd contract-management
```

### Step 2: Configure Database

Edit `.env` file in the project root:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=contract_management
DB_USERNAME=root
DB_PASSWORD=your_password
```

Create the database:

```sql
CREATE DATABASE contract_management;
```

### Step 3: Copy Project Files

Copy all the files from `D:\Contract mangemnet` to the Laravel project structure:

#### Migrations
```bash
# Copy to: database/migrations/
# Rename files with timestamp prefix format: YYYY_MM_DD_HHMMSS_filename.php

cp create_contracts_table_migration.php database/migrations/2024_01_01_000001_create_contracts_table.php
cp create_contract_histories_table_migration.php database/migrations/2024_01_01_000002_create_contract_histories_table.php
```

#### Models
```bash
# Copy to: app/Models/

cp Contract_Model.php app/Models/Contract.php
cp ContractHistory_Model.php app/Models/ContractHistory.php
```

#### Controller
```bash
# Copy to: app/Http/Controllers/

cp ContractController.php app/Http/Controllers/ContractController.php
```

#### Command
```bash
# Copy to: app/Console/Commands/

cp CheckContractAlerts_Command.php app/Console/Commands/CheckContractAlerts.php
```

#### Routes
```bash
# Replace content of: routes/web.php

cp web_routes.php routes/web.php
```

#### Views
```bash
# Copy to: resources/views/

# Create directories
mkdir -p resources/views/layouts
mkdir -p resources/views/contracts

# Copy layout
cp layout_blade.php resources/views/layouts/app.blade.php

# Copy contract views
cp index_blade.php resources/views/contracts/index.blade.php
cp create_blade.php resources/views/contracts/create.blade.php
cp show_blade.php resources/views/contracts/show.blade.php
cp edit_blade.php resources/views/contracts/edit.blade.php
cp extend_contract_end_blade.php resources/views/contracts/extend-contract-end.blade.php
cp extend_performance_guarantee_blade.php resources/views/contracts/extend-performance-guarantee.blade.php
cp alerts_blade.php resources/views/contracts/alerts.blade.php
```

### Step 4: Run Migrations

```bash
php artisan migrate
```

### Step 5: Generate Application Key (if not already set)

```bash
php artisan key:generate
```

### Step 6: Start the Development Server

```bash
php artisan serve
```

The application will be available at: `http://localhost:8000`

## Usage Guide

### Creating a Contract

1. Navigate to "New Contract" from the menu
2. Fill in the contract details:
   - **Contract Name**: Name of the contract
   - **Start Date**: Contract start date
   - **End Date**: Contract end date
   - **Alert Days Before**: How many days before end date to show alert (e.g., 30 days)
   - **Performance Guarantee Amount**: Optional financial guarantee amount
   - **Performance Guarantee End Date**: Optional PG end date
   - **PG Alert Days**: How many days before PG end date to show alert

### Viewing Contracts

- **All Contracts**: View list of all contracts with status badges
- **Filter**: Search by name or filter by alert type
- **Status Indicators**:
  - Green "Active": Contract is active with no alerts
  - Red "Contract Expiring": Contract end date alert is active
  - Yellow "PG Expiring": Performance guarantee alert is active

### Extending Contract End Date

1. Go to contract details page
2. Click "Extend Contract End Date"
3. Enter:
   - New end date (must be after current end date)
   - Reason for extension (required)
   - Your name (optional)
4. The system will:
   - Update the end date
   - Increment the extension counter
   - Log the change in history with reason

### Extending Performance Guarantee

1. Go to contract details page
2. Click "Extend Performance Guarantee"
3. Enter:
   - New amount
   - New end date
   - Reason for extension (required)
   - Your name (optional)
4. The system will:
   - Update amount and end date
   - Increment the extension counter
   - Log the change in history

### Viewing Alerts

The "Alerts" page shows all contracts that need attention:
- Contracts approaching end dates
- Performance guarantees approaching end dates
- Quick links to extend directly from alert dashboard

### Checking Alerts via Command Line

Run the alert checking command:

```bash
# Check all alerts
php artisan contracts:check-alerts

# Send alerts via email
php artisan contracts:check-alerts --email=admin@example.com
```

### Setting Up Automated Alert Checks

Add to Laravel scheduler in `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Check alerts daily at 9 AM
    $schedule->command('contracts:check-alerts --email=admin@example.com')
             ->dailyAt('09:00');
}
```

Then set up cron job:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## Database Schema

### Contracts Table
- `id`: Primary key
- `name`: Contract name
- `start_date`: Contract start date
- `end_date`: Contract end date
- `alert_days_before`: Days before end date to alert
- `performance_guarantee_amount`: PG amount (nullable)
- `performance_guarantee_end_date`: PG end date (nullable)
- `performance_guarantee_alert_days`: Days before PG end to alert
- `contract_end_date_edit_count`: Number of contract extensions
- `performance_guarantee_edit_count`: Number of PG extensions
- `timestamps`: Created and updated timestamps
- `deleted_at`: Soft delete timestamp

### Contract Histories Table
- `id`: Primary key
- `contract_id`: Foreign key to contracts
- `field_name`: Which field was changed ('contract_end_date' or 'performance_guarantee')
- `old_value`: Previous value (JSON for PG)
- `new_value`: New value (JSON for PG)
- `edited_by`: Name of person who made change
- `reason`: Reason for change
- `timestamps`: Created and updated timestamps

## API Routes

- `GET /` - Redirect to contracts index
- `GET /contracts` - List all contracts
- `GET /contracts/create` - Show create form
- `POST /contracts` - Store new contract
- `GET /contracts/{id}` - Show contract details
- `GET /contracts/{id}/edit` - Show edit form
- `PUT /contracts/{id}` - Update contract
- `DELETE /contracts/{id}` - Delete contract
- `GET /alerts` - Show alerts dashboard
- `GET /contracts/{id}/extend-contract-end` - Show extend form
- `POST /contracts/{id}/update-contract-end` - Update contract end date
- `GET /contracts/{id}/extend-performance-guarantee` - Show PG extend form
- `POST /contracts/{id}/update-performance-guarantee` - Update PG

## Customization

### Changing Alert Logic

Edit `app/Models/Contract.php`:
- `shouldAlertContractEndDate()` - Modify contract alert logic
- `shouldAlertPerformanceGuarantee()` - Modify PG alert logic

### Changing Email Template

Create `resources/views/emails/contract-alerts.blade.php` for custom email templates.

### Adding Authentication

To require login:

```bash
composer require laravel/breeze --dev
php artisan breeze:install
php artisan migrate
npm install && npm run dev
```

Then add authentication middleware to routes in `routes/web.php`:

```php
Route::middleware(['auth'])->group(function () {
    // All contract routes
});
```

## Troubleshooting

### Composer Install Fails

If you have network issues:
1. Try: `composer install --prefer-dist --no-dev`
2. Or manually download dependencies
3. Or use a different network/VPN

### Migration Errors

- Check database connection in `.env`
- Ensure database exists
- Check MySQL is running

### Vendor Directory Missing

Run: `composer install` or `composer dump-autoload`

### Permission Errors (Linux/Mac)

```bash
chmod -R 775 storage bootstrap/cache
```

## Support

For issues or questions, refer to:
- Laravel Documentation: https://laravel.com/docs
- Project files in `D:\Contract mangemnet`

## License

This project is built with Laravel framework (MIT License).
