@extends('layouts.app')

@section('title', 'All Contracts - Contract Management')

@section('styles')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<style>
    /* Theme-consistent table styling */
    #contractsTable.dataTable thead th {
        background: linear-gradient(90deg,#1a6dcc,#0d4d9c);
        color: #fff;
        font-weight: 700;
        border-bottom: none;
    }
    #contractsTable.dataTable tbody td { padding: 10px 12px; vertical-align: middle; }
    #contractsTable .badge { padding: 6px 10px; border-radius: 18px; font-weight:600; }
    .dt-button, .buttons-excel, .buttons-print { background:#1a6dcc;color:#fff;border-radius:6px;border:0;padding:6px 10px;margin-right:6px }
    .dataTables_wrapper .dataTables_filter input { border:1px solid #ddd; padding:6px 8px; border-radius:6px }
    .dataTables_wrapper .dataTables_length select { border:1px solid #ddd; padding:6px 8px; border-radius:6px }
    /* make actions column compact */
    td.actions-col { white-space:nowrap; }
    @media (max-width:768px){ #contractsTable { font-size:13px } }
</style>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const defaultPerPage = parseInt('{{ $pp }}', 10) || 5;

        const table = $('#contractsTable').DataTable({
            ajax: {
                url: '{{ route("contracts.data") }}',
                data: function(d) {
                    d.search = $('input[name="search"]').val();
                    d.alert_type = $('select[name="alert_type"]').val();
                },
                dataSrc: 'data'
            },
            columns: [
                { data: 'id' },
                { data: 'name', render: function(data, type, row) { return `<strong><a href="${row.view_url}">${data}</a></strong>`; } },
                { data: 'start_date' },
                { data: 'end_date' },
                { data: 'performance_guarantee_amount', className: 'dt-right' },
                { data: 'performance_guarantee_end_date' },
                { data: 'status', render: function(data){
                    if (data === 'Contract Expiring') return '<span class="badge badge-danger">'+data+'</span>';
                    if (data === 'PG Expiring') return '<span class="badge badge-warning">'+data+'</span>';
                    return '<span class="badge badge-success">'+data+'</span>';
                } },
                { data: null, orderable:false, searchable:false, render: function(data,type,row){
                    return `<div class="actions-col"><a class="btn btn-sm btn-primary" href="${row.view_url}">View</a> <a class="btn btn-sm btn-secondary" href="${row.edit_url}">Edit</a></div>`;
                } }
            ],
            dom: '<"top"fB>rt<"bottom"lp><"clear">',
            buttons: [
                { extend: 'excel', text: 'Export Excel' },
                { extend: 'print', text: 'Print' }
            ],
            responsive: true,
            pageLength: defaultPerPage,
            lengthMenu: [5,10,15,25],
            columnDefs: [
                { width: '5%', targets: 0 },
                { width: '35%', targets: 1 },
                { width: '10%', targets: [2,3] },
                { width: '12%', targets: [4,5] },
                { width: '13%', targets: 6 },
                { width: '10%', targets: 7, className: 'actions-col' }
            ]
        });

        // Hook form filter to reload table
        const form = document.querySelector('form[action="{{ route('contracts.index') }}"]');
        if (form) {
            form.addEventListener('submit', function(e){ e.preventDefault(); table.ajax.reload(); });
        }

        // per_page select should control DataTable page length
        const perPageSelect = document.querySelector('select[name="per_page"]');
        if (perPageSelect) {
            perPageSelect.addEventListener('change', function(){ table.page.len(parseInt(this.value,10)).draw(); });
        }

        // search input binds to DataTable search
        const searchInput = document.querySelector('input[name="search"]');
        if (searchInput) {
            let timeout = null;
            searchInput.addEventListener('input', function(){ clearTimeout(timeout); timeout = setTimeout(()=>{ table.search(this.value).draw(); }, 250); });
        }

        // reset filters button (if present)
        const resetBtn = document.getElementById('resetFilters');
        if (resetBtn) {
            resetBtn.addEventListener('click', function(e){ e.preventDefault(); document.querySelector('select[name="alert_type"]').value=''; if (searchInput) searchInput.value=''; table.ajax.reload(); });
        }
    });
</script>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2>All Contracts</h2>
        <a href="{{ route('contracts.create') }}" class="btn btn-primary">Create New Contract</a>
    </div>

    <div class="mb-3">
        <form action="{{ route('contracts.index') }}" method="GET" class="d-flex" style="gap: 1rem; align-items:center;">
            <input type="text" name="search" class="form-control" placeholder="Search by contract name..." value="{{ request('search') }}" style="max-width: 300px;">
            <select name="alert_type" class="form-control" style="max-width: 200px;">
                <option value="">All Contracts</option>
                <option value="contract_end" {{ request('alert_type') == 'contract_end' ? 'selected' : '' }}>Expiring Contracts</option>
                <option value="performance_guarantee" {{ request('alert_type') == 'performance_guarantee' ? 'selected' : '' }}>Expiring PG</option>
            </select>
            <label style="margin:0 0.5rem; color:#6B7280;">Per page</label>
            <select name="per_page" class="form-control" style="max-width: 120px;">
                @php $pp = request('per_page', 5); @endphp
                <option value="5" {{ $pp == 5 ? 'selected' : '' }}>5</option>
                <option value="10" {{ $pp == 10 ? 'selected' : '' }}>10</option>
                <option value="15" {{ $pp == 15 ? 'selected' : '' }}>15</option>
                <option value="25" {{ $pp == 25 ? 'selected' : '' }}>25</option>
            </select>
            <button type="submit" class="btn btn-secondary">Filter</button>
            <script>
                // auto-submit when per_page changes for quick UX
                (function() {
                    document.addEventListener('DOMContentLoaded', function() {
                        const sel = document.querySelector('select[name="per_page"]');
                        if (!sel) return;
                        sel.addEventListener('change', function() { sel.form.submit(); });
                    });
                })();
            </script>
            @if(request('search') || request('alert_type'))
                <a href="{{ route('contracts.index') }}" class="btn btn-secondary">Clear</a>
            @endif
        </form>
    </div>

    <div>
        <table id="contractsTable" class="display nowrap" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Contract Name</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>PG Amount</th>
                    <th>PG End Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    @else
        <p class="text-muted">No contracts found. <a href="{{ route('contracts.create') }}">Create your first contract</a>.</p>
    @endif
</div>
@endsection
