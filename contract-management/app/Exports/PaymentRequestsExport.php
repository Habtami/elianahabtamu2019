<?php

namespace App\Exports;

use App\Helpers\ExcelExport;

class PaymentRequestsExport
{
    protected $requests;
    protected $search;

    public function __construct($requests, string $search = '')
    {
        $this->requests = $requests;
        $this->search = $search;
    }

    public function download(string $filename = 'payment_requests_export.xlsx')
    {
        $excel = new ExcelExport(
            [
                'Request No.',
                'Payment Request Date',
                'GS (1st Approval)',
                'GS Approver',
                'GS Approved At',
                'Finance (2nd Approval)',
                'Finance Approver',
                'Finance Completed At',
                'Payment Status',
                'Remark',
            ],
            'Payment Requests'
        );

        foreach ($this->requests as $request) {
            $excel->addRow([
                $request->getReference(),
                $request->payment_request_date ? $request->payment_request_date->format('Y-m-d') : '',
                $request->isGsApproved() ? 'Approved' : 'Pending',
                $request->gsApprover->name ?? '',
                $request->gs_approved_at ? $request->gs_approved_at->format('Y-m-d H:i') : '',
                $request->getFinanceStatusLabel(),
                $request->financeApprover->name ?? '',
                $request->finance_approved_at ? $request->finance_approved_at->format('Y-m-d H:i') : '',
                $request->getStatusLabel(),
                $request->remarks ?? '',
            ]);
        }

        $excel->download($filename);
    }
}
