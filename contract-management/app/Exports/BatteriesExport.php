<?php

namespace App\Exports;

use App\Helpers\ExcelExport;
use App\Helpers\ReportMeta;
use App\Models\Battery;

class BatteriesExport
{
    protected $batteries;

    protected $reportMeta;

    public function __construct($batteries, ?array $reportMeta = null)
    {
        $this->batteries = $batteries;
        $this->reportMeta = $reportMeta;
    }

    public function download(string $filename = 'battery_given_and_return.xlsx')
    {
        $excel = new ExcelExport(
            [
                'Date Mfg', 'Serial No', 'Material Code', 'Battery Type', 'Brand',
                'Plate No', 'Driver', 'Initial KM', 'Return KM', 'Difference', 'Status',
            ],
            'Batteries'
        );

        $excel->setMeta(ReportMeta::sheetLines($this->reportMeta, 'Batteries Register'));

        foreach ($this->batteries as $battery) {
            $initial = (int) $battery->initial_kilometer;
            $returned = (int) $battery->return_kilometer;
            $difference = '';

            if ($battery->status === Battery::STATUS_RETURNED || $returned > $initial) {
                $difference = ($returned - $initial >= 0 ? '+' : '') . number_format($returned - $initial);
            }

            $excel->addRow([
                $battery->date_of_manufacture ? $battery->date_of_manufacture->format('Y-m-d') : '',
                $battery->serial_number,
                $battery->material_code ?: '',
                $battery->battery_type,
                $battery->brand,
                $battery->plate_number,
                $battery->driver_name ?: '',
                $initial,
                $returned,
                $difference,
                $battery->getStatusDisplayName(),
            ]);
        }

        $excel->download($filename);
    }
}
