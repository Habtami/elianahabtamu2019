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
            ['Roll No.', 'Serial Number', 'Tire Specification', 'Given To', 'Date', 'Given By', 'Status'],
            'Tire Returns'
        );

        foreach ($this->gives as $give) {
            $excel->addRow([
                str_pad((string) $give->id, 2, '0', STR_PAD_LEFT),
                $give->tire?->serial_number ?? '',
                $give->tire?->tire_type ?? '',
                $give->driver?->name ?? '',
                $give->give_date ? $give->give_date->format('M d, Y') : '',
                $give->givenBy?->name ?? '',
                $give->is_returned ? 'Returned' : 'Pending',
            ]);
        }

        $excel->download($filename);
    }
}
