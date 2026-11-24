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
            ['ID', 'Name', 'Email', 'License Number', 'Phone', 'License Expiry', 'Status', 'Active Tires'],
            'Drivers'
        );

        foreach ($this->drivers as $driver) {
            $excel->addRow([
                str_pad((string) $driver->id, 2, '0', STR_PAD_LEFT),
                $driver->name,
                $driver->email ?? '',
                $driver->license_number ?? '',
                $driver->phone ?? '',
                $driver->license_expiry ? $driver->license_expiry->format('Y-m-d') : '',
                $driver->status ? 'Active' : 'Inactive',
                (int) ($driver->active_tires_count ?? 0),
            ]);
        }

        $excel->download($filename);
    }
}
