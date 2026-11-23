<?php

namespace App\Exports;

use App\Helpers\ExcelExport;

class PurchaseOrdersExport
{
    protected $orders;
    protected $search;

    public function __construct($orders, string $search = '')
    {
        $this->orders = $orders;
        $this->search = $search;
    }

    public function download(string $filename = 'purchase_orders_export.xlsx')
    {
        $excel = new ExcelExport(
            [
                'PO Number',
                'Amount (ETB)',
                'Approval Date',
                '1st Approval (GS Manager)',
                'GS Manager',
                '1st Approved At',
                '2nd Approval (DDG)',
                'DDG Approver',
                '2nd Approved At',
                'Status',
            ],
            'Purchase Orders'
        );

        foreach ($this->orders as $order) {
            $excel->addRow([
                $order->po_number,
                $order->amount !== null ? number_format((float) $order->amount, 2, '.', '') : '',
                $order->approval_date ? $order->approval_date->format('Y-m-d') : '',
                $order->isGsApproved() ? 'Approved' : 'Pending',
                $order->gsManagerApprover->name ?? '',
                $order->gs_manager_approved_at ? $order->gs_manager_approved_at->format('Y-m-d H:i') : '',
                $order->getSecondApprovalStatusLabel(),
                $order->ddgApprover->name ?? '',
                $order->ddg_approved_at ? $order->ddg_approved_at->format('Y-m-d H:i') : '',
                $order->getStatusLabel(),
            ]);
        }

        $excel->download($filename);
    }
}
