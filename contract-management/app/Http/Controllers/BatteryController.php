<?php

namespace App\Http\Controllers;

use App\Helpers\ReportMeta;
use App\Models\Battery;
use App\Models\BatteryBrand;
use App\Models\BatteryHistory;
use App\Models\BatteryType;
use App\Models\Driver;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class BatteryController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if (!$user->canViewBatteries()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to access batteries.');
        }

        $query = $this->filteredBatteryQuery($request)->with('creator');

        $perPage = $request->get('per_page', 10);
        if (!in_array((int) $perPage, [5, 10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        $batteries = $query->latest()->paginate($perPage)->appends($request->query());

        $brands = Battery::getBrands();
        $batteryTypes = Battery::getBatteryTypes();
        $batteryStatuses = Battery::getStatuses();
        $drivers = Driver::active()->orderBy('name')->get(['id', 'name']);
        $totalBatteries = Battery::count();
        $returnedBatteries = Battery::returned()->count();
        $inServiceBatteries = Battery::inService()->count();
        $reportMeta = ReportMeta::build(
            $batteries->getCollection(),
            $this->letterheadFilters($request),
            null,
            null,
            'created_at',
            $batteries->total()
        );

        return view('batteries.index', compact(
            'batteries', 'brands', 'batteryTypes', 'batteryStatuses', 'drivers',
            'totalBatteries', 'returnedBatteries', 'inServiceBatteries', 'reportMeta'
        ))->with('statGroups', $this->statGroups());
    }

    /**
     * @return array<int, string|null>
     */
    private function letterheadFilters(Request $request): array
    {
        return [
            $request->filled('search') ? 'Search: '.$request->search : null,
            $request->filled('brand') ? 'Brand: '.$request->brand : null,
        ];
    }

    private function scopeQuery(string $scope)
    {
        $query = Battery::query()->latest();

        if ($scope === 'in_service') {
            $query->inService();
        } elseif ($scope === 'returned') {
            $query->returned();
        } elseif ($scope === 'variance') {
            $query->whereColumn('return_kilometer', '>', 'initial_kilometer');
        }

        return $query;
    }

    /**
     * @return array<string, \Illuminate\Database\Eloquent\Collection>
     */
    private function statGroups(): array
    {
        return [
            'total' => $this->scopeQuery('total')->get(),
            'in_service' => $this->scopeQuery('in_service')->get(),
            'returned' => $this->scopeQuery('returned')->get(),
            'variance' => $this->scopeQuery('variance')->get(),
        ];
    }

    public function exportExcel(Request $request)
    {
        $user = auth()->user();
        if (!$user->canExportBatteries()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to export batteries.');
        }

        $batteries = $this->filteredBatteryQuery($request)->latest()->get();
        $reportMeta = ReportMeta::build($batteries, $this->letterheadFilters($request));

        (new \App\Exports\BatteriesExport($batteries, $reportMeta))
            ->download('battery_given_and_return_' . now()->format('Y-m-d') . '.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $user = auth()->user();
        if (!$user->canExportBatteries()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to export batteries.');
        }

        $batteries = $this->filteredBatteryQuery($request)->latest()->get();
        $reportMeta = ReportMeta::build($batteries, $this->letterheadFilters($request));

        return Pdf::loadView('batteries.pdf', [
            'batteries' => $batteries,
            'search' => $request->search,
            'brand' => $request->brand,
            'reportMeta' => $reportMeta,
        ])->setPaper('a4', 'landscape')
            ->download('battery_given_and_return_' . now()->format('Y-m-d') . '.pdf');
    }

    public function report(Request $request)
    {
        $user = auth()->user();
        if (!$user->canViewBatteryReports()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to access battery reports.');
        }

        $report = $this->buildIssueReport($request);

        $allBatteries = Battery::with(['histories.creator', 'creator'])->orderBy('serial_number')->get();
        [$issueCardRows, $issueCardCounts, $issueCardSeries] = $this->buildIssueRows($allBatteries);
        $inServiceList = $allBatteries->filter(fn (Battery $battery) => !$battery->isReturned())->values();
        $returnedList = $allBatteries->filter(fn (Battery $battery) => $battery->isReturned())->values();

        $perPage = (int) $request->get('per_page', 5);
        if (!in_array($perPage, [5, 10, 25, 50], true)) {
            $perPage = 5;
        }
        $page = max(1, (int) $request->get('issues_page', 1));
        $lastPage = max(1, (int) ceil($issueCardRows->count() / $perPage));
        $page = min($page, $lastPage);

        $issuePaginator = new LengthAwarePaginator(
            $issueCardRows->forPage($page, $perPage)->values(),
            $issueCardRows->count(),
            $perPage,
            $page,
            ['path' => route('batteries.report'), 'pageName' => 'issues_page']
        );
        $issuePaginator->appends($request->only(['date_field', 'from', 'to', 'per_page']));

        return view('batteries.report', [
            'rows' => $issueCardRows,
            'issuePaginator' => $issuePaginator,
            'batteries' => $allBatteries,
            'issueCounts' => $issueCardCounts,
            'issueCardRows' => $issueCardRows,
            'totalBatteries' => $allBatteries->count(),
            'rangeTotal' => $report['batteries']->count(),
            'inServiceBatteries' => $inServiceList->count(),
            'returnedBatteries' => $returnedList->count(),
            'inServiceList' => $inServiceList,
            'returnedList' => $returnedList,
            'dateField' => $report['dateField'],
            'from' => $report['from'],
            'to' => $report['to'],
            'chartData' => [
                'status' => [
                    ['label' => 'In Service', 'value' => $inServiceList->count(), 'color' => '#22c55e'],
                    ['label' => 'Returned', 'value' => $returnedList->count(), 'color' => '#f59e0b'],
                ],
                'severity' => [
                    ['label' => 'High', 'value' => $issueCardCounts['high'], 'color' => '#dc2626'],
                    ['label' => 'Medium', 'value' => $issueCardCounts['medium'], 'color' => '#d97706'],
                    ['label' => 'Low', 'value' => $issueCardCounts['low'], 'color' => '#0284c7'],
                ],
                'categories' => $issueCardSeries,
                'trend' => $report['trendSeries'],
            ],
        ]);
    }

    public function exportReportExcel(Request $request)
    {
        $user = auth()->user();
        if (!$user->canViewBatteryReports()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to export battery reports.');
        }

        $allBatteries = Battery::with(['histories.creator', 'creator'])->orderBy('serial_number')->get();
        [$rows] = $this->buildIssueRows($allBatteries);

        (new \App\Exports\BatteryIssuesExport($rows))
            ->download('battery_issues_' . now()->format('Y-m-d') . '.xlsx');
    }

    public function exportReportPdf(Request $request)
    {
        $user = auth()->user();
        if (!$user->canViewBatteryReports()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to export battery reports.');
        }

        $allBatteries = Battery::with(['histories.creator', 'creator'])->orderBy('serial_number')->get();
        [$rows, $issueCounts] = $this->buildIssueRows($allBatteries);

        return Pdf::loadView('batteries.report_pdf', [
            'rows' => $rows,
            'issueCounts' => $issueCounts,
            'totalBatteries' => $allBatteries->count(),
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape')
            ->download('battery_issues_' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Detect the data issues found on a set of batteries.
     *
     * @return array{0: \Illuminate\Support\Collection, 1: array<string, int>, 2: \Illuminate\Support\Collection}
     */
    private function buildIssueRows(\Illuminate\Support\Collection $batteries): array
    {
        $duplicateSerials = Battery::select('serial_number')
            ->groupBy('serial_number')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('serial_number')
            ->all();

        $severityRank = ['high' => 3, 'medium' => 2, 'low' => 1];
        $categoryCounts = [];

        $rows = $batteries->map(function (Battery $battery) use ($duplicateSerials, &$categoryCounts) {
            $initial = (int) $battery->initial_kilometer;
            $returned = (int) $battery->return_kilometer;
            $issues = [];

            if (trim((string) $battery->serial_number) === '') {
                $issues[] = ['severity' => 'high', 'category' => 'Serial / Identity', 'message' => 'Serial number is missing.'];
            } elseif (in_array((string) $battery->serial_number, $duplicateSerials, true)) {
                $issues[] = ['severity' => 'high', 'category' => 'Serial / Identity', 'message' => 'Serial number appears more than once.'];
            }

            if ($returned > 0 && $returned < $initial) {
                $issues[] = [
                    'severity' => 'high',
                    'category' => 'Mileage',
                    'message' => 'Return KM (' . number_format($returned) . ') is less than Initial KM (' . number_format($initial) . ').',
                ];
            }

            if ($battery->isReturned() && $returned <= 0) {
                $issues[] = ['severity' => 'high', 'category' => 'Mileage', 'message' => 'Marked Returned but no Return KM reading is recorded.'];
            }

            if (!$battery->isReturned() && trim((string) $battery->driver_name) === '') {
                $issues[] = ['severity' => 'medium', 'category' => 'Assignment', 'message' => 'In Service but no driver is assigned.'];
            }

            if (trim((string) $battery->plate_number) === '') {
                $issues[] = ['severity' => 'medium', 'category' => 'Documentation', 'message' => 'Plate number is missing.'];
            }

            if (trim((string) $battery->material_code) === '') {
                $issues[] = ['severity' => 'medium', 'category' => 'Documentation', 'message' => 'Material code is missing.'];
            }

            if (trim((string) $battery->battery_type) === '') {
                $issues[] = ['severity' => 'medium', 'category' => 'Documentation', 'message' => 'Battery type is missing.'];
            }

            if (trim((string) $battery->brand) === '') {
                $issues[] = ['severity' => 'low', 'category' => 'Documentation', 'message' => 'Brand is missing.'];
            }

            if (!$battery->date_of_manufacture) {
                $issues[] = ['severity' => 'low', 'category' => 'Documentation', 'message' => 'Date of manufacture is missing.'];
            }

            if ($battery->histories->isEmpty()) {
                $issues[] = ['severity' => 'low', 'category' => 'History', 'message' => 'No history records found for this battery.'];
            }

            $highest = 'low';
            foreach ($issues as $issue) {
                if ($issue['severity'] === 'high' || ($issue['severity'] === 'medium' && $highest === 'low')) {
                    $highest = $issue['severity'];
                }
                $categoryCounts[$issue['category']] = ($categoryCounts[$issue['category']] ?? 0) + 1;
            }

            return [
                'battery' => $battery,
                'issues' => $issues,
                'severity' => $highest,
            ];
        })->filter(fn ($row) => !empty($row['issues']))
          ->sortByDesc(fn ($row) => $severityRank[$row['severity']] ?? 0)
          ->values();

        $issueCounts = [
            'high' => $rows->where('severity', 'high')->count(),
            'medium' => $rows->where('severity', 'medium')->count(),
            'low' => $rows->where('severity', 'low')->count(),
        ];

        arsort($categoryCounts);
        $categorySeries = collect($categoryCounts)
            ->map(fn ($value, $label) => ['label' => $label, 'value' => $value])
            ->values();

        return [$rows, $issueCounts, $categorySeries];
    }

    private function buildIssueReport(Request $request): array
    {
        $validated = $request->validate([
            'date_field' => 'nullable|in:given,manufacture',
            'from' => 'nullable|date',
            'to' => 'nullable|date',
        ]);

        $dateField = $validated['date_field'] ?? 'given';
        $from = $validated['from'] ?? null;
        $to = $validated['to'] ?? null;

        if ($from && $to && \Carbon\Carbon::parse($to)->lt(\Carbon\Carbon::parse($from))) {
            throw ValidationException::withMessages([
                'to' => 'The "To" date must be on or after the "From" date.',
            ]);
        }

        $dateColumn = $dateField === 'manufacture' ? 'date_of_manufacture' : 'created_at';

        $batteryQuery = Battery::with(['histories.creator', 'creator']);
        if ($from) {
            $batteryQuery->whereDate($dateColumn, '>=', $from);
        }
        if ($to) {
            $batteryQuery->whereDate($dateColumn, '<=', $to);
        }
        $batteries = $batteryQuery->orderBy('serial_number')->get();

        [$rows, $issueCounts, $categorySeries] = $this->buildIssueRows($batteries);

        $groupedByMonth = $batteries->groupBy(function (Battery $battery) use ($dateField) {
            $date = $dateField === 'manufacture' ? $battery->date_of_manufacture : $battery->created_at;

            return $date ? $date->format('Y-m') : null;
        })->reject(fn ($group, $key) => $key === null);

        $trendSeries = [];
        if ($groupedByMonth->isNotEmpty()) {
            $keys = $groupedByMonth->keys()->sort()->values();
            $cursor = \Carbon\Carbon::createFromFormat('Y-m', $keys->first())->startOfMonth();
            $last = \Carbon\Carbon::createFromFormat('Y-m', $keys->last())->startOfMonth();
            $months = 0;

            while ($cursor->lte($last) && $months < 36) {
                $key = $cursor->format('Y-m');
                $trendSeries[] = [
                    'label' => $cursor->format('M Y'),
                    'value' => $groupedByMonth->has($key) ? $groupedByMonth->get($key)->count() : 0,
                ];
                $cursor->addMonth();
                $months++;
            }
        }

        $inServiceList = $batteries->filter(fn (Battery $battery) => !$battery->isReturned())->values();
        $returnedList = $batteries->filter(fn (Battery $battery) => $battery->isReturned())->values();

        return [
            'rows' => $rows,
            'batteries' => $batteries,
            'issueCounts' => $issueCounts,
            'dateField' => $dateField,
            'from' => $from,
            'to' => $to,
            'inServiceList' => $inServiceList,
            'returnedList' => $returnedList,
            'categorySeries' => $categorySeries,
            'trendSeries' => $trendSeries,
        ];
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if (!$user->canGiveBatteries()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to give batteries.');
        }

        $validated = $request->validate([
            'date_of_manufacture' => 'nullable|date',
            'serial_number' => 'required|string|max:255|unique:batteries,serial_number',
            'material_code' => 'nullable|string|max:255',
            'battery_type' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'plate_number' => 'required|string|max:255',
            'driver_name' => 'nullable|string|max:255',
            'initial_kilometer' => 'nullable|integer|min:0',
            'return_kilometer' => 'nullable|integer|min:0',
            'status' => 'nullable|in:in_service,returned',
        ], [
            'serial_number.required' => 'Serial number and plate number are required.',
            'plate_number.required' => 'Serial number and plate number are required.',
        ]);

        if (($validated['material_code'] ?? null) === '__new__') {
            $validated['material_code'] = null;
        }

        $validated['material_code'] = $validated['material_code'] ?? Battery::getMaterialCodeForType($validated['battery_type']);
        $validated['initial_kilometer'] = $validated['initial_kilometer'] ?? 0;
        $validated['return_kilometer'] = $validated['return_kilometer'] ?? 0;
        $validated['status'] = $validated['status'] ?? Battery::STATUS_IN_SERVICE;
        $validated['created_by'] = $user->id;
        $validated['is_active'] = true;

        $this->ensureReturnNotBelowInitial($validated);

        [$validated['battery_type'], $validated['brand']] = $this->resolveTypeAndBrand($request);
        $validated['material_code'] = $validated['material_code'] ?: Battery::getMaterialCodeForType($validated['battery_type']);
        $validated['driver_name'] = $this->resolveDriverName($request, $user);

        $battery = Battery::create($validated);
        $this->recordHistory($battery, 'registered', 'Battery given');

        return redirect()->route('batteries.index')
            ->with('success', 'Battery given successfully.');
    }

    public function update(Request $request, Battery $battery)
    {
        $user = auth()->user();
        if (!$user->canEditBatteries()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to edit batteries.');
        }

        $validated = $request->validate([
            'date_of_manufacture' => 'nullable|date',
            'serial_number' => 'required|string|max:255|unique:batteries,serial_number,' . $battery->id,
            'material_code' => 'nullable|string|max:255',
            'battery_type' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'plate_number' => 'required|string|max:255',
            'driver_name' => 'nullable|string|max:255',
            'initial_kilometer' => 'nullable|integer|min:0',
            'return_kilometer' => 'nullable|integer|min:0',
            'status' => 'nullable|in:in_service,returned',
        ], [
            'serial_number.required' => 'Serial number and plate number are required.',
            'plate_number.required' => 'Serial number and plate number are required.',
        ]);

        if (($validated['material_code'] ?? null) === '__new__') {
            $validated['material_code'] = null;
        }

        $validated['material_code'] = $validated['material_code'] ?? Battery::getMaterialCodeForType($validated['battery_type']);
        $validated['initial_kilometer'] = $validated['initial_kilometer'] ?? 0;
        $validated['return_kilometer'] = $validated['return_kilometer'] ?? 0;
        $validated['status'] = $validated['status'] ?? Battery::STATUS_IN_SERVICE;

        $this->ensureReturnNotBelowInitial($validated);

        if ($validated['serial_number'] !== $battery->serial_number
            && ($battery->isReturned() || $validated['status'] === Battery::STATUS_RETURNED)) {
            throw ValidationException::withMessages([
                'serial_number' => 'Serial number must match the registered battery (' . $battery->serial_number . '). Return rejected.',
            ]);
        }

        [$validated['battery_type'], $validated['brand']] = $this->resolveTypeAndBrand($request);
        $validated['material_code'] = $validated['material_code'] ?: Battery::getMaterialCodeForType($validated['battery_type']);
        $validated['driver_name'] = $this->resolveDriverName($request, $user);

        $trackedFields = [
            'serial_number', 'battery_type', 'brand', 'material_code', 'plate_number',
            'driver_name', 'initial_kilometer', 'return_kilometer', 'status', 'date_of_manufacture',
        ];
        $original = $battery->only($trackedFields);

        $battery->update($validated);

        $changes = array_diff_key($battery->getChanges(), ['updated_at' => true]);
        if (!empty($changes)) {
            $action = array_keys($changes) === ['status'] ? 'status_changed' : 'updated';
            $this->recordHistory($battery, $action, $this->describeChanges($original, $changes));
        }

        return redirect()->route('batteries.index')
            ->with('success', 'Battery updated successfully.');
    }

    /**
     * Resolve the submitted type/brand, registering new ones when requested.
     */
    private function resolveTypeAndBrand(Request $request): array
    {
        $typeName = $request->input('battery_type');
        $brandName = $request->input('brand');

        if ($typeName === '__new__') {
            $codeRules = $request->input('material_code') === '__new__'
                ? ['required', 'string', 'max:255', 'unique:battery_types,material_code']
                : ['nullable', 'string', 'max:255', 'unique:battery_types,material_code'];

            $request->validate([
                'new_battery_type' => 'required|string|max:255',
                'new_battery_type_code' => $codeRules,
            ], [
                'new_battery_type.required' => 'Enter the new battery type name.',
                'new_battery_type_code.required' => 'Enter the new material code.',
                'new_battery_type_code.unique' => 'That material code is already registered to another battery type.',
            ]);

            $newCode = $request->input('new_battery_type_code') ?: null;

            $type = BatteryType::firstOrCreate(
                ['name' => trim($request->input('new_battery_type'))],
                [
                    'material_code' => $newCode,
                    'is_active' => true,
                    'sort_order' => ((int) BatteryType::max('sort_order')) + 1,
                ]
            );

            if (!$type->wasRecentlyCreated && $newCode && !$type->material_code) {
                $type->update(['material_code' => $newCode]);
            }

            $typeName = $type->name;
        }

        if ($brandName === '__new__') {
            $request->validate([
                'new_battery_brand' => 'required|string|max:255',
            ], [
                'new_battery_brand.required' => 'Enter the new battery brand name.',
            ]);

            $brand = BatteryBrand::firstOrCreate(
                ['name' => trim($request->input('new_battery_brand'))],
                [
                    'is_active' => true,
                    'sort_order' => ((int) BatteryBrand::max('sort_order')) + 1,
                ]
            );

            $brandName = $brand->name;
        }

        return [$typeName, $brandName];
    }

    /**
     * Resolve the submitted driver, registering a new one when requested.
     */
    private function resolveDriverName(Request $request, $user): ?string
    {
        $driverName = $request->input('driver_name');

        if ($driverName === '__new__') {
            $request->validate([
                'new_driver_name' => 'required|string|max:255',
            ], [
                'new_driver_name.required' => 'Enter the new driver name.',
            ]);

            $driver = Driver::firstOrCreate(
                ['name' => trim($request->input('new_driver_name'))],
                ['status' => true, 'created_by' => $user->id]
            );

            return $driver->name;
        }

        return $driverName ?: null;
    }

    public function returnBattery(Request $request, Battery $battery)
    {
        $user = auth()->user();
        if (!$user->canReturnBatteries()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to return batteries.');
        }

        if ($battery->isReturned()) {
            return redirect()->route('batteries.index')
                ->with('error', 'Battery ' . $battery->serial_number . ' is already marked as returned.');
        }

        $validated = $request->validate([
            'return_serial' => 'required|string|max:255',
            'returned_kilometer' => 'required|integer|min:0',
        ], [
            'return_serial.required' => 'Enter the serial number of the battery being returned.',
            'returned_kilometer.required' => 'Enter the return KM reading.',
        ]);

        if (trim($validated['return_serial']) !== (string) $battery->serial_number) {
            throw ValidationException::withMessages([
                'return_serial' => 'Serial number does not match the registered battery (' . $battery->serial_number . '). Return rejected.',
            ]);
        }

        if ((int) $validated['returned_kilometer'] < (int) $battery->initial_kilometer) {
            throw ValidationException::withMessages([
                'returned_kilometer' => 'Return KM cannot be less than Initial KM (' . number_format($battery->initial_kilometer) . ').',
            ]);
        }

        $battery->update([
            'status' => Battery::STATUS_RETURNED,
            'return_kilometer' => (int) $validated['returned_kilometer'],
        ]);

        $this->recordHistory(
            $battery,
            'returned',
            'Battery returned. Serial verified: ' . $battery->serial_number . '.'
        );

        return redirect()->route('batteries.index')
            ->with('success', 'Battery ' . $battery->serial_number . ' returned. Serial number verified.');
    }

    public function history(Battery $battery)
    {
        $user = auth()->user();
        if (!$user->canViewBatteries()) {
            abort(403);
        }

        $history = $battery->histories()
            ->with('creator')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get()
            ->map(function (BatteryHistory $entry) {
                return [
                    'date' => $entry->created_at ? $entry->created_at->format('Y-m-d H:i') : '—',
                    'action' => $entry->action_label,
                    'action_key' => $entry->action,
                    'status' => $entry->status_label ?? '—',
                    'driver' => $entry->driver_name ?: '—',
                    'initial_km' => $entry->initial_kilometer,
                    'return_km' => $entry->return_kilometer,
                    'notes' => $entry->notes ?: '',
                    'user' => $entry->creator->name ?? 'System',
                    'email' => $entry->creator->email ?? null,
                    'role' => $entry->creator ? $entry->creator->getRoleDisplayName() : null,
                ];
            });

        return response()->json([
            'serial_number' => $battery->serial_number,
            'history' => $history,
        ]);
    }

    private function filteredBatteryQuery(Request $request)
    {
        $query = Battery::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('serial_number', 'like', '%' . $search . '%')
                  ->orWhere('material_code', 'like', '%' . $search . '%')
                  ->orWhere('battery_type', 'like', '%' . $search . '%')
                  ->orWhere('brand', 'like', '%' . $search . '%')
                  ->orWhere('plate_number', 'like', '%' . $search . '%')
                  ->orWhere('driver_name', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('brand')) {
            $query->where('brand', $request->brand);
        }

        return $query;
    }

    private function ensureReturnNotBelowInitial(array $data): void
    {
        $initial = (int) ($data['initial_kilometer'] ?? 0);
        $returned = (int) ($data['return_kilometer'] ?? 0);

        if ($returned > 0 && $returned < $initial) {
            throw ValidationException::withMessages([
                'return_kilometer' => 'Return KM cannot be less than Initial KM.',
            ]);
        }
    }

    private function recordHistory(Battery $battery, string $action, ?string $notes = null): void
    {
        $battery->histories()->create([
            'action' => $action,
            'status' => $battery->status,
            'driver_name' => $battery->driver_name,
            'initial_kilometer' => $battery->initial_kilometer,
            'return_kilometer' => $battery->return_kilometer,
            'notes' => $notes,
            'created_by' => auth()->id(),
        ]);
    }

    private function describeChanges(array $original, array $changes): string
    {
        $labels = [
            'serial_number' => 'Serial No',
            'battery_type' => 'Battery Type',
            'brand' => 'Brand',
            'material_code' => 'Material Code',
            'plate_number' => 'Plate No',
            'driver_name' => 'Driver',
            'initial_kilometer' => 'Initial KM',
            'return_kilometer' => 'Return KM',
            'status' => 'Status',
            'date_of_manufacture' => 'Date Mfg',
        ];

        $parts = [];
        foreach ($changes as $field => $new) {
            $label = $labels[$field] ?? ucfirst(str_replace('_', ' ', $field));
            $old = $original[$field] ?? null;

            if ($field === 'status') {
                $statuses = Battery::getStatuses();
                $old = $statuses[$old] ?? $old;
                $new = $statuses[$new] ?? $new;
            } elseif (in_array($field, ['initial_kilometer', 'return_kilometer'], true)) {
                $old = number_format((int) $old);
                $new = number_format((int) $new);
            } else {
                if ($old instanceof \DateTimeInterface) {
                    $old = $old->format('Y-m-d');
                }
                if ($new instanceof \DateTimeInterface) {
                    $new = $new->format('Y-m-d');
                }
            }

            $parts[] = $label . ': '
                . (($old === null || $old === '') ? '—' : $old)
                . ' → '
                . (($new === null || $new === '') ? '—' : $new);
        }

        return implode('; ', $parts);
    }

    public function destroy(Battery $battery)
    {
        $user = auth()->user();
        if (!$user->canDeleteBatteries()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to delete batteries.');
        }

        $battery->histories()->delete();
        $battery->delete();

        return redirect()->route('batteries.index')
            ->with('success', 'Battery removed successfully.');
    }
}
