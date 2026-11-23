<?php

namespace App\Exports;

use App\Helpers\ExcelExport;

class ContractsExport
{
    protected $contracts;

    public function __construct($contracts)
    {
        $this->contracts = $contracts;
    }

    public function download(string $filename = 'contracts_export.xlsx')
    {
        $excel = new ExcelExport(
            ['ID', 'Name', 'Supplier', 'Department', 'Type', 'Start Date', 'End Date', 'PG Amount', 'Currency', 'PG End Date', 'Status', 'Created By'],
            'Contracts'
        );

        foreach ($this->contracts as $contract) {
            $excel->addRow([
                $contract->id,
                $contract->name,
                $contract->supplier_name ?? 'N/A',
                $contract->department ? ucfirst(str_replace('_', ' ', $contract->department)) : '',
                $contract->contract_type ?? '',
                $contract->start_date ? $contract->start_date->format('Y-m-d') : '',
                $contract->end_date ? $contract->end_date->format('Y-m-d') : '',
                $contract->performance_guarantee_amount ? number_format($contract->performance_guarantee_amount, 2) : '',
                $contract->currency_type ?? '',
                $contract->performance_guarantee_end_date ? $contract->performance_guarantee_end_date->format('Y-m-d') : '',
                $contract->getStatusDisplayName(),
                $contract->creator->name ?? 'N/A',
            ]);
        }

        $excel->download($filename);
    }
}
