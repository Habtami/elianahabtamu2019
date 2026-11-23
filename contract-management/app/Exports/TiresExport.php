<?php

namespace App\Exports;

use App\Helpers\ExcelExport;
use App\Models\Tire;

class TiresExport
{
    protected $tires;

    public function __construct($tires = null)
    {
        $this->tires = $tires ?: Tire::all();
    }

    public function download(string $filename = 'tires_export.xlsx')
    {
        $excel = new ExcelExport(
            ['ID', 'Tire Type', 'Serial Number', 'Status', 'Current Driver', 'Given KM', 'Last Return KM', 'Created At'],
            'Tires'
        );

        foreach ($this->tires as $tire) {
            $currentGive = $tire->tireGives()->where('is_returned', false)->first();
            $lastReturn = $tire->tireReturns()->latest()->first();

            $excel->addRow([
                $tire->id,
                $tire->tire_type,
                $tire->serial_number,
                $tire->status,
                $currentGive?->driver?->name ?? '',
                $currentGive ? $currentGive->given_kilometer : '',
                $lastReturn ? $lastReturn->current_kilometer : '',
                $tire->created_at ? $tire->created_at->format('Y-m-d H:i:s') : '',
            ]);
        }

        $excel->download($filename);
    }
}
