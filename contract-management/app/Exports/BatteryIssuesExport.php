<?php

namespace App\Exports;

use App\Helpers\ExcelExport;

class BatteryIssuesExport
{
    protected $rows;

    public function __construct($rows)
    {
        $this->rows = $rows;
    }

    public function download(string $filename = 'battery_issues.xlsx')
    {
        $excel = new ExcelExport(
            [
                'Severity', 'Serial No', 'Plate No', 'Battery Type', 'Brand',
                'Driver', 'Status', 'Issue Count', 'Issue(s)',
            ],
            'Battery Issues'
        );

        foreach ($this->rows as $row) {
            $battery = $row['battery'];
            $messages = array_map(function ($issue) {
                return $issue['message'];
            }, $row['issues']);

            $excel->addRow([
                ucfirst($row['severity']),
                $battery->serial_number ?: '',
                $battery->plate_number ?: '',
                $battery->battery_type ?: '',
                $battery->brand ?: '',
                $battery->driver_name ?: '',
                $battery->getStatusDisplayName(),
                count($messages),
                implode('; ', $messages),
            ]);
        }

        $excel->download($filename);
    }
}
