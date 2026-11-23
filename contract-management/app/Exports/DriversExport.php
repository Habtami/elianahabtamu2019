<?php

namespace App\Exports;

use App\Helpers\ExcelExport;
use App\Models\Driver;

class DriversExport
{
    protected $drivers;

    public function __construct($drivers = null)
    {
        $this->drivers = $drivers ?: Driver::all();
    }

    public function download(string $filename = 'drivers_export.xlsx')
    {
        $excel = new ExcelExport(
            ['ID', 'Name', 'Phone', 'License Number', 'License Expiry', 'Status', 'Created At'],
            'Drivers'
        );

        foreach ($this->drivers as $driver) {
            $excel->addRow([
                $driver->id,
                $driver->name,
                $driver->phone ?? '',
                $driver->license_number ?? '',
                $driver->license_expiry ? $driver->license_expiry->format('Y-m-d') : '',
                $driver->status ? 'Active' : 'Inactive',
                $driver->created_at ? $driver->created_at->format('Y-m-d H:i:s') : '',
            ]);
        }

        $excel->download($filename);
    }
}
