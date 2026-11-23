@extends('layouts.app')

@section('title', 'Extend Contract End Date - ' . $contract->name)

@section('styles')
<style>
    .form-card {
        background: white;
        border-radius: 0.75rem;
        border: 1px solid #E5E7EB;
    }

    .form-input {
        border: 1px solid #D1D5DB;
        border-radius: 0.5rem;
        padding: 0.75rem 1rem;
        width: 100%;
        transition: all 0.2s;
    }

    .form-input:focus {
        outline: none;
        border-color: #1C75BC;
        box-shadow: 0 0 0 3px rgba(28, 117, 188, 0.1);
    }

    .form-label {
        display: block;
        font-weight: 500;
        color: #374151;
        margin-bottom: 0.5rem;
    }

    .required::after {
        content: ' *';
        color: #EF4444;
    }

    .info-card {
        background: linear-gradient(135deg, #DBEAFE 0%, #BFDBFE 100%);
        border: 1px solid #93C5FD;
        border-radius: 0.75rem;
        padding: 1.5rem;
    }

    .history-table {
        background: white;
        border-radius: 0.75rem;
        border: 1px solid #E5E7EB;
        overflow: hidden;
    }

    .table-header {
        background: linear-gradient(135deg, #F8FAFC 0%, #F1F5F9 100%);
        border-bottom: 2px solid #E5E7EB;
    }

    .table-row {
        border-bottom: 1px solid #F1F5F9;
    }

    .table-cell {
        padding: 1rem;
        text-align: left;
        vertical-align: middle;
    }

    .badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        font-size: 0.875rem;
        font-weight: 500;
        border-radius: 9999px;
    }

    .bg-success {
        background-color: #D1FAE5;
        color: #065F46;
    }

    .bg-warning {
        background-color: #FEF3C7;
        color: #92400E;
    }

    .bg-danger {
        background-color: #FEE2E2;
        color: #991B1B;
    }

    .bg-secondary {
        background-color: #E5E7EB;
        color: #374151;
    }
</style>
@endsection

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-800">Extend Contract End Date</h1>
                    <p class="text-gray-600 mt-2">Modify the contract's end date with proper tracking</p>
                </div>
                <a href="{{ route('contracts.show', $contract) }}" class="text-gray-600 hover:text-gray-800 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Contract
                </a>
            </div>
        </div>

        <!-- Form -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Form -->
            <div class="lg:col-span-2">
                <!-- Contract Info -->
                <div class="info-card mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        Contract Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <span class="text-sm font-medium text-gray-600">Contract:</span>
                            <p class="text-gray-800 font-medium">{{ $contract->name }}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-600">Current End Date:</span>
                            <p class="text-gray-800 font-medium">{{ $contract->end_date->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-600">Previous Extensions:</span>
                            <p class="text-gray-800 font-medium">{{ $contract->extended_count ?? 0 }} times</p>
                        </div>
                    </div>
                </div>

                <!-- Extension Form -->
                <div class="form-card p-6">
                    <form method="POST" action="{{ route('contracts.update-contract-end', $contract) }}" id="extendForm">
                        @csrf

                        <!-- New End Date -->
                        <div class="mb-6">
                            <label for="new_end_date" class="form-label required">New End Date</label>
                            <input type="date"
                                   id="new_end_date"
                                   name="new_end_date"
                                   value="{{ old('new_end_date') }}"
                                   min="{{ $contract->end_date->format('Y-m-d') }}"
                                   class="form-input"
                                   required>
                            <p class="text-sm text-gray-500 mt-1">New end date must be after the current end date ({{ $contract->end_date->format('M d, Y') }})</p>
                            @error('new_end_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Reason -->
                        <div class="mb-6">
                            <label for="reason" class="form-label required">Reason for Extension</label>
                            <textarea id="reason"
                                      name="reason"
                                      rows="4"
                                      class="form-input"
                                      placeholder="Please provide a detailed reason for this extension"
                                      required>{{ old('reason') }}</textarea>
                            @error('reason')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Extended By -->
                        <div class="mb-6">
                            <label class="form-label">Extended By</label>
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                                <span class="font-medium text-gray-800">{{ auth()->user()->name }}</span>
                                <span class="text-gray-500">({{ auth()->user()->getDepartmentDisplayName() }})</span>
                            </div>
                            <p class="text-sm text-gray-500 mt-1">This extension will be recorded under your account</p>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                            <a href="{{ route('contracts.show', $contract) }}"
                               class="px-6 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                                Cancel
                            </a>
                            <button type="submit"
                                    class="bg-yellow-500 hover:bg-yellow-600 text-white font-medium px-6 py-2 rounded-lg transition-colors flex items-center">
                                <i class="fas fa-calendar-plus mr-2"></i>
                                Extend Contract End Date
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Help Section -->
            <div class="lg:col-span-1">
                <div class="form-card p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Extension Guidelines</h3>

                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="bg-yellow-100 p-2 rounded-lg mr-3">
                                <i class="fas fa-calendar-plus text-yellow-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800">Date Extension</h4>
                                <p class="text-sm text-gray-600 mt-1">Choose a new end date that extends beyond the current expiration</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="bg-blue-100 p-2 rounded-lg mr-3">
                                <i class="fas fa-comment text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800">Detailed Reason</h4>
                                <p class="text-sm text-gray-600 mt-1">Provide comprehensive justification for the extension</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="bg-green-100 p-2 rounded-lg mr-3">
                                <i class="fas fa-history text-green-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800">Audit Trail</h4>
                                <p class="text-sm text-gray-600 mt-1">All extensions are logged with timestamps and reasons</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="bg-purple-100 p-2 rounded-lg mr-3">
                                <i class="fas fa-bell text-purple-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800">Alert Updates</h4>
                                <p class="text-sm text-gray-600 mt-1">System alerts will automatically adjust to the new end date</p>
                            </div>
                        </div>
                    </div>

                    <!-- Extension Stats -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <h4 class="font-medium text-gray-800 mb-3">Extension Statistics</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Total Extensions:</span>
                                <span class="text-sm font-medium">{{ $contract->extended_count ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Status:</span>
                                <span class="text-sm font-medium">
                                    <span class="badge 
                                        @if($contract->status === 'active') bg-success
                                        @elseif($contract->status === 'expiring') bg-warning
                                        @elseif($contract->status === 'expired') bg-danger
                                        @else bg-secondary @endif">
                                        {{ ucfirst($contract->status) }}
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Previous Extensions History -->
        @php
            // Safely get the histories with null coalescing
            $histories = $contract->contractEndDateHistories ?? collect();
        @endphp

        @if($histories->count() > 0)
        <div class="mt-8">
            <div class="history-table">
                <div class="table-header px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-800">Previous Extensions History</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="table-cell font-semibold text-gray-700">Date & Time</th>
                                <th class="table-cell font-semibold text-gray-700">Old End Date</th>
                                <th class="table-cell font-semibold text-gray-700">New End Date</th>
                                <th class="table-cell font-semibold text-gray-700">Extended By</th>
                                <th class="table-cell font-semibold text-gray-700">Reason</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($histories as $history)
                            <tr class="table-row hover:bg-gray-50">
                                <td class="table-cell text-gray-600">
                                    @if($history->created_at)
                                        {{ $history->created_at->format('M d, Y H:i') }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="table-cell text-gray-600">{{ $history->old_value ?? 'N/A' }}</td>
                                <td class="table-cell text-gray-600 font-medium text-green-600">{{ $history->new_value ?? 'N/A' }}</td>
                                <td class="table-cell text-gray-600">{{ $history->edited_by ?? 'N/A' }}</td>
                                <td class="table-cell text-gray-600">{{ $history->reason ?? 'N/A' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @else
        <!-- Show empty state if no history exists -->
        <div class="mt-8">
            <div class="history-table">
                <div class="table-header px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-800">Previous Extensions History</h3>
                </div>
                <div class="p-6 text-center">
                    <div class="inline-block p-4 bg-gray-50 rounded-lg">
                        <i class="fas fa-history text-gray-400 text-3xl mb-3"></i>
                        <p class="text-gray-600">No extension history available</p>
                        <p class="text-sm text-gray-500 mt-1">This will be the first extension for this contract</p>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Form validation
        const form = document.getElementById('extendForm');
        form.addEventListener('submit', function(e) {
            const newEndDate = document.getElementById('new_end_date').value;
            const currentEndDate = '{{ $contract->end_date->format('Y-m-d') }}';
            const reason = document.getElementById('reason').value.trim();

            if (new Date(newEndDate) <= new Date(currentEndDate)) {
                e.preventDefault();
                alert('New end date must be after the current end date.');
                document.getElementById('new_end_date').focus();
                return false;
            }

            if (reason.length < 10) {
                e.preventDefault();
                alert('Please provide a detailed reason (at least 10 characters).');
                document.getElementById('reason').focus();
                return false;
            }

            return true;
        });

        // Set minimum date on new_end_date field
        const newEndDateField = document.getElementById('new_end_date');
        if (newEndDateField) {
            const tomorrow = new Date('{{ $contract->end_date }}');
            tomorrow.setDate(tomorrow.getDate() + 1);
            newEndDateField.min = tomorrow.toISOString().split('T')[0];
        }
    });
</script>
@endsection