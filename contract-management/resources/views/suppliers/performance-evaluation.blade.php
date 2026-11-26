@extends('layouts.app')

@section('title', 'Supplier Performance Management')

@section('styles')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    /* Copied/adapted styles from provided design for consistent UI */
    * { box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    body { background: transparent; }
    .container-card { max-width: 1400px; margin: 20px auto; background: #fff; border-radius:12px; box-shadow:0 5px 15px rgba(0,0,0,0.08); overflow:hidden; }
    header.header-card{ background: linear-gradient(135deg,#1a6dcc,#0d4d9c); color:#fff; padding:20px 24px; }
    header.header-card h1{ margin:0; font-size:22px }
    .content { padding:24px }
    .table-responsive { width:100%; overflow-x:auto; -webkit-overflow-scrolling:touch; }
    .table-responsive .dataTables_filter { display:flex; justify-content:flex-end; align-items:center; gap:8px; margin-bottom:12px; font-weight:600; color:#555; }
    .table-responsive .dataTables_filter input { width:280px; max-width:100%; padding:8px 12px; border:1px solid #ddd; border-radius:6px; margin-left:0; }
    .table-responsive .dataTables_filter input:focus { outline:none; border-color:#1a6dcc; box-shadow:0 0 0 3px rgba(26,109,204,0.12); }
    .tabs { display:flex; gap:6px; border-bottom:1px solid #e1e8f0; margin-bottom:18px }
    .tab{ padding:10px 18px; background:none; border:0; cursor:pointer; font-weight:600; color:#666 }
    .tab.active{ color:#1a6dcc; border-bottom:3px solid #1a6dcc }
    .tab-content{ display:none } .tab-content.active{ display:block }
    .stats-container{ display:flex; gap:16px; flex-wrap:wrap; margin-bottom:18px }
    .stat-card{ flex:1; min-width:160px; background:#fff; border-radius:8px; padding:14px; border-top:4px solid #1a6dcc }
    .stat-value{ font-size:20px; font-weight:700; color:#1a6dcc }
    table.performance-table{ width:100% !important; min-width:940px; table-layout:fixed; border-collapse:collapse }
    table.performance-table th{ background:#f8fafc; padding:8px 10px; text-align:left; font-weight:600; font-size:13px; white-space:normal; vertical-align:middle }
    table.performance-table td{ padding:8px 10px; border-bottom:1px solid #eee; font-size:13px; white-space:normal; word-wrap:break-word; vertical-align:middle }
    table.performance-table th:nth-child(1){ width:18% }
    table.performance-table th:nth-child(2){ width:9% }
    table.performance-table th:nth-child(3){ width:13% }
    table.performance-table th:nth-child(4){ width:10% }
    table.performance-table th:nth-child(5){ width:12% }
    table.performance-table th:nth-child(6){ width:11% }
    table.performance-table th:nth-child(7){ width:13% }
    table.performance-table th:nth-child(8){ width:14% }
    table.performance-table td:last-child .action-btn{ white-space:nowrap; padding:6px 8px; }
    .supplier-name{ font-weight:600; color:#1a6dcc }
    .contracts{ color:#888; font-size:13px }
    .performance-status{ padding:6px 10px; border-radius:20px; font-weight:600 }
    .top-performer{ background:#e8f5e9; color:#2e7d32 }
    .average{ background:#fff3e0; color:#ef6c00 }
    .needs-improvement{ background:#ffebee; color:#c62828 }
    .no-data{ color:#999; font-style:italic }
    .action-btn{ padding:6px 12px; background:#1a6dcc; color:#fff; border-radius:6px; border:0; cursor:pointer }
    .action-btn:hover{ background:#0d5bb5 }
    /* modal */
    .modal{ display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); align-items:center; justify-content:center; z-index:2000 }
    .modal .modal-content{ background:#fff; width:92%; max-width:900px; border-radius:12px; max-height:90vh; overflow:auto }
    .modal .modal-header{ background:linear-gradient(135deg,#1a6dcc,#0d4d9c); color:#fff; padding:18px }
    .modal .modal-body{ padding:18px }
    .criteria-row{ display:flex; justify-content:space-between; align-items:center; padding:10px 0; border-bottom:1px solid #f0f0f0 }
    .rating-star{ font-size:22px; color:#ddd; cursor:pointer }
    .rating-star.active{ color:#ffc107 }
    .overall-rating-summary{ padding:14px; border-radius:8px; background:#f9fbfd; margin-top:12px; text-align:center }
    @media (max-width:768px){ .tabs{flex-direction:column} }
</style>
@endsection

@section('content')
<div class="container-card">
    <header class="header-card">
        <h1>Supplier Performance Management</h1>
        <div class="subtitle">Rate and monitor supplier performance across key metrics</div>
    </header>
    <div class="content">
        <div class="tabs">
            <button class="tab active" data-tab="overview">Performance Overview</button>
            <button class="tab" data-tab="rating">Rate Suppliers</button>
        </div>

        <div id="overview" class="tab-content active">
            <div class="stats-container">
                <div class="stat-card">
                    <h6 class="text-muted">Total Suppliers</h6>
                    <div class="stat-value">{{ $totalSuppliers }}</div>
                    <div class="small text-muted">In database</div>
                </div>
                <div class="stat-card">
                    <h6 class="text-muted">Average Rating</h6>
                    <div class="stat-value">{{ $avgRating > 0 ? number_format($avgRating,1).'/5' : 'N/A' }}</div>
                    <div class="small text-muted">Across all suppliers</div>
                </div>
                <div class="stat-card">
                    <h6 class="text-muted">Top Performers</h6>
                    <div class="stat-value">{{ $topPerformers }}</div>
                    <div class="small text-muted">Rated "Excellent"</div>
                </div>
                <div class="stat-card">
                    <h6 class="text-muted">Needs Improvement</h6>
                    <div class="stat-value">{{ $needsImprovement }}</div>
                    <div class="small text-muted">Require attention</div>
                </div>
            </div>

            <div style="margin-bottom:12px; display:flex; justify-content:flex-end; gap:12px; align-items:center;">
                <div style="display:flex;gap:8px">
                    <button type="button" class="table-tip-btn" data-table-tip aria-expanded="false" aria-controls="tipSupplierPerformance"><i class="fas fa-info-circle"></i> TIP: info</button>
                    <button id="exportData" class="action-btn"><i class="fas fa-download"></i>&nbsp;Export</button>
                    <button id="resetFilters" class="action-btn" style="background:#6c757d">Reset</button>
                </div>
            </div>

            <div id="tipSupplierPerformance" class="table-tip-panel no-print"><h4><i class="fas fa-info-circle mr-1"></i>How this table works</h4><ul><li>Two tabs: Performance Overview holds the stats and table; Rate Suppliers explains rating and links back to the overview.</li><li>Stat cards show total suppliers, average rating, top performers rated Excellent, and suppliers flagged as needing improvement.</li><li>Columns list supplier name and contract count, Quality and On-Time Delivery as percentages, and Cost and Communication out of five.</li><li>Overall Rating percent plus a coloured status badge from the star average: 4.5+ Excellent, 3.5+ Good, 2.5+ Average, lower Needs Improvement.</li><li>Search filters suppliers, the page-length menu paginates ten rows, Reset clears search, and Export downloads the table to Excel.</li><li>Rate Supplier opens a star modal; ratings average into an overall score, and only contract creators can rate while others see View Only.</li></ul></div>

            <div class="table-responsive">
                <table id="supplierTable" class="performance-table display">
                    <thead>
                        <tr>
                            <th>Supplier Name</th>
                            <th>Quality</th>
                            <th>On-Time Delivery</th>
                            <th>Cost Rating</th>
                            <th>Communication</th>
                            <th>Overall Rating</th>
                            <th>Performance Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <div id="rating" class="tab-content">
            <div style="text-align:center;padding:40px 20px">
                <i class="fas fa-edit" style="font-size:48px;color:#1a6dcc;margin-bottom:16px"></i>
                <h3>Rate Suppliers</h3>
                <p style="color:#666;max-width:680px;margin:8px auto">Select a supplier from the Performance Overview table and click "Rate Supplier" to provide ratings across quality, on-time delivery, cost, and communication. Note: only users who created contracts may rate — others see view-only.</p>
                <button class="action-btn" onclick="document.querySelector('.tab[data-tab=\'overview\']').click()">Go to Performance Overview</button>
            </div>
        </div>
    </div>
    <footer style="padding:12px 18px;border-top:1px solid #eee;text-align:center;color:#888">Supplier Performance Rating System</footer>
</div>

<!-- Modal -->
<div class="modal" id="ratingModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 style="margin:0">Rate Supplier</h3>
        </div>
        <div class="modal-body">
            <div style="display:flex;gap:12px;align-items:center;margin-bottom:12px">
                <div style="width:60px;height:60px;background:#f0f4f8;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#1a6dcc;font-size:24px">
                    <i class="fas fa-industry"></i>
                </div>
                <div>
                    <div id="modalSupplierName" style="font-size:18px;font-weight:600;color:#1a6dcc">Supplier Name</div>
                    <div id="modalSupplierContracts" style="color:#777;font-size:13px">0 contracts</div>
                </div>
            </div>

            <div class="rating-criteria" id="criteriaContainer"></div>

            <div class="overall-rating-summary">
                <div class="overall-rating-label">Overall Rating</div>
                <div class="overall-rating-value" id="overallRatingValue">0.0</div>
                <div class="overall-rating-stars" id="overallRatingStars"></div>
                <div class="overall-performance" id="overallPerformance">Not Rated</div>
            </div>

            <div style="margin-top:12px">
                <label>Additional Comments</label>
                <textarea id="ratingComments" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px;min-height:80px"></textarea>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:12px">
                <button id="cancelRating" class="action-btn" style="background:#f0f4f8;color:#333">Cancel</button>
                <button id="submitRating" class="action-btn">Submit Rating</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
    // Build suppliers array from server-side data
    const suppliers = [
        @foreach($suppliers as $supplier)
        {
            id: {{ $supplier->id }},
            name: "{!! addslashes($supplier->name) !!}",
            contracts: {{ $supplier->contracts_count }},
            quality: {{ $supplier->quality_rating ?? 'null' }},
            delivery: {{ $supplier->delivery_rating ?? 'null' }},
            cost: {{ $supplier->cost_rating ?? 'null' }},
            communication: {{ $supplier->communication_rating ?? 'null' }},
            overall: {{ $supplier->overall_rating ?? 'null' }},
            status: "{{ $supplier->performance_status ?? '' }}",
            hasData: {{ $supplier->overall_rating ? 'true' : 'false' }},
            canRate: {{ $supplier->can_rate ? 'true' : 'false' }}
        },
        @endforeach
    ];

    const ratingCriteria = [
        { id: 'quality', name: 'Quality', description: 'Quality of materials/products provided' },
        { id: 'delivery', name: 'On-Time Delivery', description: 'Adherence to delivery schedules' },
        { id: 'cost', name: 'Cost Rating', description: 'Competitiveness and value for money' },
        { id: 'communication', name: 'Communication', description: 'Responsiveness and clarity of communication' }
    ];

    let supplierTable, currentSupplier = null;
    let currentRatings = { quality:0, delivery:0, cost:0, communication:0 };

    $(function(){
        // Tabs
        $('.tab').on('click', function(){
            $('.tab').removeClass('active'); $(this).addClass('active');
            $('.tab-content').removeClass('active'); $('#' + $(this).data('tab')).addClass('active');
        });

        // DataTable
        supplierTable = $('#supplierTable').DataTable({
            data: suppliers.map(s => [
                `<div class="supplier-name">${s.name}</div><div class="contracts">${s.contracts} contracts</div>`,
                s.hasData ? `${s.quality}%` : '<span class="no-data">-</span>',
                s.hasData ? `${s.delivery}%` : '<span class="no-data">-</span>',
                s.hasData ? `${s.cost}/5` : '<span class="no-data">-</span>',
                s.hasData ? `${s.communication}/5` : '<span class="no-data">-</span>',
                s.hasData ? `${s.overall}%` : '<span class="no-data">-</span>',
                s.hasData ? `<span class="performance-status ${s.status.toLowerCase().replace(/ /g,'-')}">${s.status}</span>` : '<span class="no-data">-</span>',
                s.canRate ? `<button class="action-btn" onclick="openRatingModal(${s.id})">${s.hasData ? 'Update Rating':'Rate Supplier'}</button>` : `<span class="text-muted small"><i class="fas fa-eye me-1"></i>View Only</span>`
            ]),
            columns:[{title:'Supplier Name'},{title:'Quality'},{title:'On-Time Delivery'},{title:'Cost Rating'},{title:'Communication'},{title:'Overall Rating'},{title:'Performance Status'},{title:'Action',orderable:false}],
            dom: '<"top"f>rt<"bottom"lip><"clear">',
            autoWidth: false,
            pageLength:10,
            buttons:[{extend:'excel',text:'Excel'},{extend:'print',text:'Print'}]
        });

        // Search and controls
        $('.dataTables_filter input').attr('placeholder', 'Search suppliers...');
        $('#resetFilters').on('click', function(){ supplierTable.search('').columns().search('').draw(); $('.dataTables_filter input').val(''); });
        $('#exportData').on('click', function(){ supplierTable.button('.buttons-excel').trigger(); });

        // Modal handlers
        $('#cancelRating').on('click', closeModal);
        $(window).on('click', function(e){ if (e.target.id === 'ratingModal') closeModal(); });
    });

    function openRatingModal(id){
        const s = suppliers.find(x => x.id === id);
        if (!s) return;
        if (!s.canRate) { alert('You are not permitted to rate this supplier (view-only).'); return; }
        currentSupplier = s;
        // set modal header
        $('#modalSupplierName').text(s.name);
        $('#modalSupplierContracts').text(`${s.contracts} contracts`);

        // build criteria
        const container = $('#criteriaContainer').empty();
        ratingCriteria.forEach(c => {
            const val = s.hasData && typeof s[c.id] === 'number' ? (c.id==='cost' || c.id==='communication' ? s[c.id] : Math.round(s[c.id]/20)) : 0;
            const stars = [1,2,3,4,5].map(i => `<i class="fas fa-star rating-star ${i<=val? 'active':''}" data-crit="${c.id}" data-val="${i}"></i>`).join('');
            container.append(`<div class="criteria-row"><div><div style="font-weight:600">${c.name}</div><div style="font-size:13px;color:#777">${c.description}</div></div><div class="criteria-rating" data-criteria="${c.id}">${stars}<div class="rating-value" style="margin-left:12px">${val>0?val.toFixed(1):'0.0'}/5</div></div></div>`);
        });

        // set currentRatings from supplier
        currentRatings = {
            quality: s.hasData ? (s.quality/20) : 0,
            delivery: s.hasData ? (s.delivery/20) : 0,
            cost: s.hasData ? s.cost : 0,
            communication: s.hasData ? s.communication : 0
        };
        updateOverallRatingDisplay();

        // star clicks
        $('.rating-star').off('click').on('click', function(){
            const crit = $(this).data('crit'); const val = parseInt($(this).data('val'));
            currentRatings[crit] = val;
            $(this).parent().find('.rating-star').each(function(i){ $(this).toggleClass('active', i<val); });
            $(this).parent().find('.rating-value').text(val.toFixed(1) + '/5');
            updateOverallRatingDisplay();
        });

        $('#ratingModal').css('display','flex');
    }

    function closeModal(){ currentSupplier = null; currentRatings = {quality:0,delivery:0,cost:0,communication:0}; $('#ratingModal').hide(); }

    function updateOverallRatingDisplay(){
        const vals = Object.values(currentRatings).filter(v => v>0);
        if (vals.length===0){ $('#overallRatingValue').text('0.0'); $('#overallPerformance').text('Not Rated'); $('#overallRatingStars').html(''); return; }
        const avg = vals.reduce((a,b)=>a+b,0)/vals.length;
        $('#overallRatingValue').text(avg.toFixed(1));
        // stars
        let stars=''; const full = Math.floor(avg); const half = avg%1>=0.5;
        for(let i=1;i<=5;i++){ if(i<=full) stars += '<i class="fas fa-star" style="color:#ffc107"></i>'; else if(i===full+1 && half) stars += '<i class="fas fa-star-half-alt" style="color:#ffc107"></i>'; else stars += '<i class="far fa-star" style="color:#ddd"></i>'; }
        $('#overallRatingStars').html(stars);
        // performance label
        let perf='Poor'; if(avg>=4.5) perf='Excellent'; else if(avg>=3.5) perf='Good'; else if(avg>=2.5) perf='Average'; else if(avg>=1.5) perf='Needs Improvement';
        $('#overallPerformance').text(perf);
    }

    // Submit via AJAX
    $('#submitRating').on('click', function(){
        if(!currentSupplier){ alert('No supplier selected'); return; }
        const filled = Object.values(currentRatings).filter(v=>v>0);
        if(filled.length===0){ alert('Provide at least one rating'); return; }
        const overall = (filled.reduce((a,b)=>a+b,0)/filled.length) * 20;
        const payload = {
            _token: '{{ csrf_token() }}',
            quality_rating: currentRatings.quality * 20,
            delivery_rating: currentRatings.delivery * 20,
            cost_rating: currentRatings.cost,
            communication_rating: currentRatings.communication,
            overall_rating: overall,
            performance_status: $('#overallPerformance').text(),
            comments: $('#ratingComments').val()
        };
        $.ajax({ url: `/suppliers/${currentSupplier.id}/rate`, method:'POST', data: payload, success(){ alert('Rating submitted'); location.reload(); }, error(xhr){ alert('Error: ' + (xhr.responseJSON?.error || xhr.statusText)); } });
    });
</script>

@endsection
