<?php

namespace App\Exports;

use App\Helpers\ExcelExport;

class TireReturnsExport
{
    protected $gives;

    public function __construct($gives)
    {
        $this->gives = $gives;
    }

    public function download(string $filename = 'tire_returns.xlsx')
    {
        $excel = new ExcelExport(
            [
                'Roll No.',
                'Tire Serial',
                'Tire Specification',
                'Driver',
                'Driver Phone',
                'Plate',
                'Issued By',
                'Give Date',
                'KM at Issue',
                'Days Out',
            ],
            'Outstanding Tires'
        );

        foreach ($this->gives as $give) {
            $daysOut = $give->give_date ? (int) $give->give_date->diffInDays(now()) : 0;
            $plate = $give->plate_number ?: ($give->tire->plate_number ?? null);

            $excel->addRow([
                str_pad((string) $give->id, 2, '0', STR_PAD_LEFT),
                $give->tire->serial_number ?? '—',
                $give->tire->tire_type ?? 'Unknown type',
                $give->driver->name ?? 'Unknown driver',
                $give->driver->phone ?? '',
                $plate ?: 'Not recorded',
                $give->givenBy->name ?? '—',
                $give->give_date ? $give->give_date->format('M d, Y (l)') : '—',
                $give->given_kilometer ? number_format($give->given_kilometer) . ' km' : '—',
                $daysOut . ' ' . ($daysOut === 1 ? 'day' : 'days'),
            ]);
        }

        $excel->download($filename);
    }
}
