<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Permission Modules
    |--------------------------------------------------------------------------
    | Groupings used to organise the permission matrix on the admin page.
    */
    'modules' => [
        'contract' => [
            'label' => 'Contract Management',
            'color' => '#dc3545',
            'icon' => 'fa-file-contract',
        ],
        'tire' => [
            'label' => 'Tyre Tracking',
            'color' => '#28a745',
            'icon' => 'fa-bolt',
        ],
        'battery' => [
            'label' => 'Battery Given and Return',
            'color' => '#0f766e',
            'icon' => 'fa-car-battery',
        ],
        'procurement' => [
            'label' => 'Purchase Order Processing',
            'color' => '#1C75BC',
            'icon' => 'fa-shopping-cart',
        ],
        'payment' => [
            'label' => 'Payment Request Processing',
            'color' => '#7c3aed',
            'icon' => 'fa-money-bill-wave',
        ],
        'closing' => [
            'label' => 'Contract Closing',
            'color' => '#0d9488',
            'icon' => 'fa-file-signature',
        ],
        'report' => [
            'label' => 'Procurement Reports',
            'color' => '#0ea5e9',
            'icon' => 'fa-chart-pie',
        ],
        'purchasing' => [
            'label' => 'Purchasing Daily Activity',
            'color' => '#b45309',
            'icon' => 'fa-clipboard-list',
        ],
        'admin' => [
            'label' => 'Administration',
            'color' => '#17a2b8',
            'icon' => 'fa-user-shield',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Permission Catalog
    |--------------------------------------------------------------------------
    | key => [
    |     'label'       => display name shown on the admin matrix,
    |     'module'      => module key from the list above,
    |     'description' => short explanation of what the permission allows,
    |     'roles'       => roles granted the permission by default (seeded
    |                      into the role_permissions table and used as the
    |                      fallback until an administrator saves changes),
    | ]
    */
    'permissions' => [

        // Contract Management
        'view_all_contracts' => [
            'label' => 'View All Contracts',
            'module' => 'contract',
            'description' => 'View contracts belonging to every department.',
            'roles' => ['super_admin', 'admin', 'general_service', 'cmd', 'finance', 'gs_manager', 'cmd_manager'],
        ],
        'create_contracts' => [
            'label' => 'Create Contracts',
            'module' => 'contract',
            'description' => 'Create new contracts for the user department.',
            'roles' => ['general_service', 'cmd', 'finance'],
        ],
        'edit_all_contracts' => [
            'label' => 'Edit All Contracts',
            'module' => 'contract',
            'description' => 'Edit contracts regardless of who created them.',
            'roles' => ['super_admin', 'admin', 'finance'],
        ],
        'view_reports' => [
            'label' => 'View Reports',
            'module' => 'contract',
            'description' => 'Access contract reports and analytics.',
            'roles' => ['super_admin', 'admin', 'finance', 'gs_manager', 'cmd_manager'],
        ],

        // Tyre Tracking
        'access_tire_tracking' => [
            'label' => 'Access Tyre Tracking',
            'module' => 'tire',
            'description' => 'Open the tyre tracking module and view the tyre list.',
            'roles' => ['super_admin', 'admin', 'general_service', 'cmd', 'finance', 'gs_manager', 'cmd_manager'],
        ],
        'manage_tires' => [
            'label' => 'Manage Tyres',
            'module' => 'tire',
            'description' => 'Create, edit and delete tyres and tyre types.',
            'roles' => ['super_admin', 'admin'],
        ],
        'perform_tire_operations' => [
            'label' => 'Give / Return Tyres',
            'module' => 'tire',
            'description' => 'Give tyres to drivers and record returned tyres.',
            'roles' => ['super_admin', 'admin', 'general_service', 'cmd', 'finance'],
        ],
        'edit_drivers' => [
            'label' => 'Edit Drivers',
            'module' => 'tire',
            'description' => 'Create, edit and delete driver records.',
            'roles' => ['super_admin', 'admin', 'general_service', 'cmd', 'finance'],
        ],
        'view_tire_reports' => [
            'label' => 'View Tyre Reports',
            'module' => 'tire',
            'description' => 'Access tyre dashboards, history and printable reports.',
            'roles' => ['super_admin', 'admin', 'gs_manager', 'cmd_manager'],
        ],

        // Battery Given and Return
        'view_batteries' => [
            'label' => 'View Battery Register',
            'module' => 'battery',
            'description' => 'Open the battery register, search batteries and view their history.',
            'roles' => ['super_admin', 'admin', 'general_service', 'cmd', 'finance', 'gs_manager', 'cmd_manager'],
        ],
        'give_batteries' => [
            'label' => 'Give Batteries',
            'module' => 'battery',
            'description' => 'Register a battery and give it to a driver.',
            'roles' => ['super_admin', 'admin'],
        ],
        'return_batteries' => [
            'label' => 'Return Batteries',
            'module' => 'battery',
            'description' => 'Record a battery return with a verified serial number and return KM.',
            'roles' => ['super_admin', 'admin'],
        ],
        'edit_batteries' => [
            'label' => 'Edit Batteries',
            'module' => 'battery',
            'description' => 'Edit any battery record, including its status and KM readings.',
            'roles' => ['super_admin', 'admin'],
        ],
        'delete_batteries' => [
            'label' => 'Delete Batteries',
            'module' => 'battery',
            'description' => 'Remove battery records from the register.',
            'roles' => ['super_admin', 'admin'],
        ],
        'export_batteries' => [
            'label' => 'Export Battery Register',
            'module' => 'battery',
            'description' => 'Download the battery register as Excel or PDF.',
            'roles' => ['super_admin', 'admin', 'general_service', 'cmd', 'finance', 'gs_manager', 'cmd_manager'],
        ],
        'view_battery_reports' => [
            'label' => 'View Battery Reports',
            'module' => 'battery',
            'description' => 'Access the Battery Given and Return reports, charts and exports.',
            'roles' => ['super_admin', 'admin', 'general_service', 'cmd', 'finance', 'gs_manager', 'cmd_manager'],
        ],

        // Purchase Order Processing
        'view_purchase_orders' => [
            'label' => 'View Purchase Orders',
            'module' => 'procurement',
            'description' => 'Open the purchase order processing page and see approval status.',
            'roles' => ['super_admin', 'admin', 'general_service', 'cmd', 'finance', 'gs_manager', 'cmd_manager', 'ddg'],
        ],
        'create_purchase_orders' => [
            'label' => 'Create Purchase Orders',
            'module' => 'procurement',
            'description' => 'Register purchase orders with a PO number and approval date.',
            'roles' => ['super_admin', 'admin', 'general_service', 'cmd', 'finance'],
        ],
        'approve_purchase_orders_gs' => [
            'label' => '1st Approval (GS Manager)',
            'module' => 'procurement',
            'description' => 'Give the first approval on a purchase order as GS Manager.',
            'roles' => ['super_admin', 'admin', 'gs_manager'],
        ],
        'approve_purchase_orders_ddg' => [
            'label' => '2nd Approval (DDG)',
            'module' => 'procurement',
            'description' => 'Give the second and final approval on a purchase order as DDG.',
            'roles' => ['super_admin', 'admin', 'ddg'],
        ],

        // Payment Request Processing
        'view_payment_requests' => [
            'label' => 'View Payment Requests',
            'module' => 'payment',
            'description' => 'Open the payment request page and see the GS and Finance approval status.',
            'roles' => ['super_admin', 'admin', 'general_service', 'cmd', 'finance', 'gs_manager', 'cmd_manager', 'ddg'],
        ],
        'create_payment_requests' => [
            'label' => 'Create Payment Requests',
            'module' => 'payment',
            'description' => 'Register a payment request with its request date.',
            'roles' => ['super_admin', 'admin', 'general_service', 'cmd', 'finance'],
        ],
        'approve_payment_requests_gs' => [
            'label' => 'Payment Request 1st Approval (GS)',
            'module' => 'payment',
            'description' => 'Give the GS first approval on a payment request.',
            'roles' => ['super_admin', 'admin', 'gs_manager'],
        ],
        'complete_payment_requests_finance' => [
            'label' => 'Complete Payment (Finance)',
            'module' => 'payment',
            'description' => 'Give the Finance approval that completes a payment request.',
            'roles' => ['super_admin', 'admin', 'finance'],
        ],

        // Contract Closing
        'view_contract_closures' => [
            'label' => 'View Contract Closings',
            'module' => 'closing',
            'description' => 'Open the contract closing page, daily status and closing history.',
            'roles' => ['super_admin', 'admin', 'general_service', 'cmd', 'finance', 'gs_manager', 'cmd_manager'],
        ],
        'create_contract_closures' => [
            'label' => 'Request Contract Closing',
            'module' => 'closing',
            'description' => 'Register a closing request for a contract with its closing date.',
            'roles' => ['super_admin', 'admin', 'general_service', 'cmd', 'finance'],
        ],
        'complete_contract_closures' => [
            'label' => 'Complete Contract Closing',
            'module' => 'closing',
            'description' => 'Mark a contract closure as complete.',
            'roles' => ['super_admin', 'admin', 'finance'],
        ],

        // Procurement Reports
        'view_procurement_report' => [
            'label' => 'View Procurement Report',
            'module' => 'report',
            'description' => 'View total procurement amounts by purchase and by tender.',
            'roles' => ['super_admin', 'admin', 'finance', 'gs_manager', 'cmd_manager'],
        ],

        // Purchasing Daily Activity
        'view_purchasing_activities' => [
            'label' => 'View Purchasing Daily Activity',
            'module' => 'purchasing',
            'description' => 'Open the purchasing daily activity register of proforma and quotation requests.',
            'roles' => ['super_admin', 'admin', 'general_service', 'cmd', 'finance', 'gs_manager', 'cmd_manager'],
        ],
        'create_purchasing_activities' => [
            'label' => 'Create Purchasing Activity',
            'module' => 'purchasing',
            'description' => 'Register a proforma or request for quotation with its material group, unit and procurement type.',
            'roles' => ['super_admin', 'admin', 'general_service', 'cmd', 'finance'],
        ],
        'complete_purchasing_activities' => [
            'label' => 'Complete Purchasing Activity',
            'module' => 'purchasing',
            'description' => 'Mark a purchasing daily activity as complete.',
            'roles' => ['super_admin', 'admin', 'finance'],
        ],

        // Administration
        'admin_panel' => [
            'label' => 'Access Admin Panel',
            'module' => 'admin',
            'description' => 'Reach the administration area of the system.',
            'roles' => ['super_admin', 'admin'],
        ],
        'manage_users' => [
            'label' => 'Manage Users',
            'module' => 'admin',
            'description' => 'Create, edit, activate and deactivate user accounts.',
            'roles' => ['super_admin', 'admin'],
        ],
        'manage_permissions' => [
            'label' => 'Manage Permissions',
            'module' => 'admin',
            'description' => 'Change which permissions each role holds.',
            'roles' => ['super_admin', 'admin'],
        ],
    ],

];
