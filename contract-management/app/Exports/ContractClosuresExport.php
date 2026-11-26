<?php

namespace App\Exports;

use App\Helpers\ExcelExport;
use App\Helpers\ReportMeta;

class ContractClosuresExport
{
    protected $closures;
    protected $search;
    protected $reportMeta;

    public function __construct($closures, string $search = '', ?array $reportMeta = null)
    {
        $this->closures = $closures;
        $this->search = $search;
        $this->reportMeta = $reportMeta;
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

        $excel->setMeta(ReportMeta::sheetLines($this->reportMeta, 'Contract Closings Register'));

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
