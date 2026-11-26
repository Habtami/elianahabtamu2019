<?php

namespace App\Exports;

use App\Helpers\ExcelExport;

class ContractClosuresExport
{
    protected $closures;
    protected $search;

    public function __construct($closures, string $search = '')
    {
        $this->closures = $closures;
        $this->search = $search;
    }

    public function download(string $filename = 'contract_closures_export.xlsx')
    {
        $excel = new ExcelExport(
            [
                'Contract',
                'Supplier',
                'Department',
                'Contract End Date',
                'Closing Date',
                'Status',
                'Completed By',
                'Completed At',
                'Requested By',
                'Remarks',
            ],
            'Contract Closings'
        );

        foreach ($this->closures as $closure) {
            $excel->addRow([
                $closure->contract->name ?? 'Deleted contract',
                $closure->contract->supplier_name ?? '',
                $closure->contract ? $closure->contract->getDepartmentDisplayName() : '',
                $closure->contract?->end_date ? $closure->contract->end_date->format('Y-m-d') : '',
                $closure->closing_date ? $closure->closing_date->format('Y-m-d') : '',
                $closure->getStatusLabel(),
                $closure->completer->name ?? '',
                $closure->completed_at ? $closure->completed_at->format('Y-m-d H:i') : '',
                $closure->creator->name ?? '',
                $closure->remarks ?? '',
            ]);
        }

        $excel->download($filename);
    }
}
