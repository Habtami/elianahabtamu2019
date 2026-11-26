<?php

namespace App\Helpers;

use App\Models\User;

class ReportTypes
{
    /**
     * Report types the user may open, keyed by stable identifier.
     *
     * @return array<string, array{label: string, url: string}>
     */
    public static function forUser(?User $user = null): array
    {
        $user = $user ?: auth()->user();

        if (! $user) {
            return [];
        }

        $managerRoles = [
            User::ROLE_SUPER_ADMIN,
            User::ROLE_ADMIN,
            User::ROLE_FINANCE,
            User::ROLE_GS_MANAGER,
            User::ROLE_CMD_MANAGER,
        ];

        $supplierRoles = array_merge($managerRoles, [
            User::ROLE_GENERAL_SERVICE,
            User::ROLE_CMD,
        ]);

        $types = [
            'contract-analytics' => ['label' => 'Contract Analytics', 'route' => 'contracts.reports', 'visible' => in_array($user->role, $managerRoles, true)],
            'contracts' => ['label' => 'Contracts Register', 'route' => 'contracts.index', 'visible' => $user->canViewAllContracts()],
            'contract-closures' => ['label' => 'Contract Closings', 'route' => 'contract-closures.index', 'visible' => $user->canViewContractClosures()],
            'procurement-report' => ['label' => 'Procurement Report', 'route' => 'procurement-report.index', 'visible' => $user->canViewProcurementReport()],
            'purchase-orders' => ['label' => 'Purchase Orders', 'route' => 'purchase-orders.index', 'visible' => $user->canViewPurchaseOrders()],
            'payment-requests' => ['label' => 'Payment Requests', 'route' => 'payment-requests.index', 'visible' => $user->canViewPaymentRequests()],
            'purchasing-activities' => ['label' => 'Purchasing Activities', 'route' => 'purchasing-activities.index', 'visible' => $user->canViewPurchasingActivities()],
            'supplier-performance' => ['label' => 'Supplier Performance', 'route' => 'suppliers.performance-evaluation', 'visible' => in_array($user->role, $supplierRoles, true)],
            'tire-reports' => ['label' => 'Tire Reports', 'route' => 'tires.reports', 'visible' => $user->canViewTireReports()],
            'tires' => ['label' => 'Tire Tracking', 'route' => 'tires.index', 'visible' => $user->canAccessTireTracking()],
            'battery-report' => ['label' => 'Battery Report', 'route' => 'batteries.report', 'visible' => $user->canViewBatteryReports()],
            'batteries' => ['label' => 'Batteries Register', 'route' => 'batteries.index', 'visible' => $user->canViewBatteries()],
        ];

        $options = [];

        foreach ($types as $key => $type) {
            if ($type['visible']) {
                $options[$key] = ['label' => $type['label'], 'url' => route($type['route'])];
            }
        }

        return $options;
    }

    public static function currentKey(): ?string
    {
        $map = [
            'contracts.reports' => 'contract-analytics',
            'contracts.index' => 'contracts',
            'contracts.create' => 'contracts',
            'contract-closures.*' => 'contract-closures',
            'procurement-report.*' => 'procurement-report',
            'purchase-orders.*' => 'purchase-orders',
            'payment-requests.*' => 'payment-requests',
            'purchasing-activities.*' => 'purchasing-activities',
            'suppliers.performance-evaluation' => 'supplier-performance',
            'tires.reports' => 'tire-reports',
            'tires.*' => 'tires',
            'batteries.report' => 'battery-report',
            'batteries.*' => 'batteries',
        ];

        foreach ($map as $pattern => $key) {
            if (request()->routeIs($pattern)) {
                return $key;
            }
        }

        return null;
    }
}
