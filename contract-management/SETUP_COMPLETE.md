# Contract Management System - Setup Complete!

## Installation Summary

Your Contract Management System has been successfully installed and configured!

### What Was Set Up:

1. ✅ **Laravel Project**: Fresh Laravel 12 installation
2. ✅ **Database Migrations**:
   - contracts table
   - contract_histories table
   - Default Laravel tables (users, cache, jobs)
3. ✅ **Models**:
   - Contract model with all business logic
   - ContractHistory model for audit trails
4. ✅ **Controller**: ContractController with full CRUD + extensions
5. ✅ **Routes**: All routes configured in routes/web.php
6. ✅ **Views**: 7 Blade templates with professional styling
7. ✅ **Command**: CheckContractAlerts for scheduled alerts
8. ✅ **Database**: MySQL configured and migrations run successfully

## Access Your Application

The server is currently running at:

**🌐 http://localhost:8000** or **http://127.0.0.1:8000**

The homepage automatically redirects to the contracts list page.

## Available Pages:

- **Home** → http://localhost:8000
- **All Contracts** → http://localhost:8000/contracts
- **Create Contract** → http://localhost:8000/contracts/create
- **Alerts Dashboard** → http://localhost:8000/alerts

## Features Overview:

### 1. Contract Management
- Create contracts with name, start date, end date
- Set custom alert days before contract expiry
- View, edit, and delete contracts
- Soft delete support (recoverable)

### 2. Performance Guarantee Tracking
- Optional PG amount and end date
- Separate alert configuration for PG
- Full tracking alongside contract details

### 3. Dynamic Alerts
- User-defined alert periods (e.g., 30 days before expiry)
- Visual badges on contract list
- Dedicated alerts dashboard
- Real-time status indicators

### 4. Extension Tracking
- Dedicated forms for extending contract end dates
- Dedicated forms for extending performance guarantees
- Required reason field for accountability
- Automatic counter incrementation
- Full audit trail

### 5. Edit History
- Complete change log for all extensions
- Captures old value, new value, reason, and editor name
- Timestamps for all changes
- Separate views for contract and PG history

### 6. Command-Line Alerts
Run manual alert checks:
```bash
php artisan contracts:check-alerts
```

Send email alerts:
```bash
php artisan contracts:check-alerts --email=admin@example.com
```

### 7. Scheduled Alerts (Optional Setup)
Add to `app/Console/Kernel.php`:
```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('contracts:check-alerts --email=your@email.com')
             ->dailyAt('09:00');
}
```

## Quick Start Guide:

### Create Your First Contract:

1. Go to http://localhost:8000/contracts/create
2. Fill in contract details:
   - **Contract Name**: e.g., "Office Lease Agreement"
   - **Start Date**: 2025-01-01
   - **End Date**: 2025-12-31
   - **Alert Days Before**: 30
3. Optionally add Performance Guarantee:
   - **PG Amount**: e.g., 50000.00
   - **PG End Date**: e.g., 2025-06-30
   - **PG Alert Days**: 15
4. Click "Create Contract"

### Extend a Contract:

1. View a contract details page
2. Click "Extend Contract End Date" or "Extend Performance Guarantee"
3. Enter new date, reason, and your name
4. Submit the form
5. View the updated counter and history

### View Alerts:

1. Go to http://localhost:8000/alerts
2. See all contracts requiring attention
3. Click "Extend" buttons directly from alerts page

## Database Configuration:

Currently using:
- **Database**: MySQL
- **Host**: 127.0.0.1
- **Port**: 3306
- **Database Name**: contract_management
- **Username**: root
- **Password**: (empty)

To change database settings, edit: `D:\Contract mangemnet\contract-management\.env`

## File Locations:

- **Project Root**: `D:\Contract mangemnet\contract-management\`
- **Models**: `app/Models/`
- **Controllers**: `app/Http/Controllers/`
- **Views**: `resources/views/contracts/`
- **Migrations**: `database/migrations/`
- **Commands**: `app/Console/Commands/`

## Server Management:

### To Stop the Server:
- Press `Ctrl+C` in the terminal window running the server

### To Start the Server Again:
```bash
cd "D:\Contract mangemnet\contract-management"
php artisan serve
```

### To Run on Different Port:
```bash
php artisan serve --port=8080
```

## Common Commands:

```bash
# Navigate to project
cd "D:\Contract mangemnet\contract-management"

# Start development server
php artisan serve

# Check alerts
php artisan contracts:check-alerts

# Run migrations
php artisan migrate

# Reset database (⚠️ deletes all data)
php artisan migrate:fresh

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# List all routes
php artisan route:list

# List all artisan commands
php artisan list
```

## Troubleshooting:

### If the server stops:
```bash
cd "D:\Contract mangemnet\contract-management"
php artisan serve
```

### If you see database errors:
1. Check MySQL is running
2. Verify database `contract_management` exists
3. Check `.env` file has correct database credentials

### If pages don't load:
```bash
php artisan cache:clear
php artisan config:clear
composer dump-autoload
```

### If you need to reset everything:
```bash
php artisan migrate:fresh
```

## Next Steps:

1. **Create Sample Contracts**: Add a few test contracts to explore features
2. **Test Alerts**: Set alert dates close to today to test alert functionality
3. **Try Extensions**: Practice extending contracts and viewing history
4. **Customize Styling**: Edit `resources/views/layouts/app.blade.php` to change colors/styles
5. **Add Authentication** (Optional): Install Laravel Breeze for user login
6. **Configure Email**: Set up mail settings in `.env` for email alerts
7. **Set Up Cron**: Configure scheduled alert checks

## System Requirements Met:

✅ Contract management with full CRUD
✅ Performance guarantee tracking
✅ Dynamic alert system with user-defined periods
✅ Alert form that updates dynamically
✅ Extension tracking with counters
✅ Complete edit history with reasons
✅ Professional UI with responsive design
✅ Working alert dashboard
✅ Command-line alert checking

## Support:

For Laravel documentation: https://laravel.com/docs
For project files reference: `D:\Contract mangemnet\INSTALLATION_GUIDE.md`

---

**Congratulations! Your Contract Management System is ready to use!** 🎉

Visit http://localhost:8000 to get started.
