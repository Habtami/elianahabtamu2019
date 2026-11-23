@extends('layouts.app')

@section('title', 'Edit Contract - ' . $contract->name)

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
</style>
@endsection

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-800">Edit Contract</h1>
                    <p class="text-gray-600 mt-2">Update the contract details below</p>
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
                <div class="form-card p-6">
                    <form method="POST" action="{{ route('contracts.update', $contract) }}" id="contractForm">
                        @csrf
                        @method('PUT')

                        <!-- Contract Name -->
                        <div class="mb-6">
                            <label for="name" class="form-label required">Contract Name</label>
                            <input type="text"
                                   id="name"
                                   name="name"
                                   value="{{ old('name', $contract->name) }}"
                                   class="form-input"
                                   placeholder="Enter contract name"
                                   required>
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-6">
                            <label for="description" class="form-label">Description (Optional)</label>
                            <textarea id="description"
                                      name="description"
                                      rows="3"
                                      class="form-input"
                                      placeholder="Enter contract description">{{ old('description', $contract->description) }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        @include('contracts.partials.material-group', ['materialGroupValue' => $contract->material_group])

                        <!-- Contract Amount -->
                        <div class="mb-6">
                            <label for="contract_amount" class="form-label">Contract Amount (ETB)</label>
                            <input type="number"
                                   id="contract_amount"
                                   name="contract_amount"
                                   min="0"
                                   step="0.01"
                                   class="form-input"
                                   value="{{ old('contract_amount', $contract->contract_amount) }}"
                                   placeholder="e.g. 250000.00">
                            @error('contract_amount')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        @include('contracts.partials.department', ['departmentValue' => $contract->department])

                        @include('contracts.partials.contract-type', ['contractTypeValue' => $contract->contract_type])

                        <!-- Supplier Name -->
                        <div class="mb-6">
                            <label for="supplier_name" class="form-label">Supplier Name</label>
                            <input type="text"
                                   id="supplier_name"
                                   name="supplier_name"
                                   value="{{ old('supplier_name', $contract->supplier_name) }}"
                                   class="form-input"
                                   placeholder="Enter supplier name">
                            @error('supplier_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Dates Row -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Start Date -->
                            <div>
                                <label for="start_date" class="form-label required">Start Date</label>
                                <input type="date"
                                       id="start_date"
                                       name="start_date"
                                       value="{{ old('start_date', $contract->start_date->format('Y-m-d')) }}"
                                       class="form-input"
                                       required>
                                @error('start_date')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- End Date -->
                            <div>
                                <label for="end_date" class="form-label required">End Date</label>
                                <input type="date"
                                       id="end_date"
                                       name="end_date"
                                       value="{{ old('end_date', $contract->end_date->format('Y-m-d')) }}"
                                       class="form-input"
                                       required>
                                <p class="text-sm text-gray-500 mt-1">Use the "Extend Contract End Date" feature to track extensions properly</p>
                                @error('end_date')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                                <p id="endDateSequenceError" class="text-red-500 text-sm mt-1 hidden"></p>
                            </div>
                        </div>

                        @include('contracts.partials.date-sequence')

                        <!-- Alert Days -->
                        <div class="mb-6">
                            <label for="alert_days" class="form-label required">Alert Days Before Contract End</label>
                            <input type="number"
                                   id="alert_days"
                                   name="alert_days"
                                   value="{{ old('alert_days', $contract->alert_days) }}"
                                   min="0"
                                   class="form-input"
                                   placeholder="Enter number of days before contract end"
                                   required>
                            <p class="text-sm text-gray-500 mt-1">Number of days before contract end date to trigger alert (must be between 0 and contract duration)</p>
                            <div id="alert_days_error" class="text-sm text-red-500 mt-1" style="display: none;"></div>
                            @error('alert_days')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Types of Performance Bonds -->
                        <div class="mb-6">
                            <label for="performance_bond_type" class="form-label">Types of Performance Bonds</label>
                            <select id="performance_bond_type"
                                    name="performance_bond_type"
                                    class="form-input">
                                <option value="">Select Performance Bond Type</option>
                                <option value="CPO" {{ old('performance_bond_type', $contract->performance_bond_type) == 'CPO' ? 'selected' : '' }}>CPO</option>
                                <option value="Bank Guarantee" {{ old('performance_bond_type', $contract->performance_bond_type) == 'Bank Guarantee' ? 'selected' : '' }}>Bank Guarantee</option>
                                <option value="Insurance Bond" {{ old('performance_bond_type', $contract->performance_bond_type) == 'Insurance Bond' ? 'selected' : '' }}>Insurance Bond</option>
                                <option value="Small Enterprise Letter" {{ old('performance_bond_type', $contract->performance_bond_type) == 'Small Enterprise Letter' ? 'selected' : '' }}>Small Enterprise Letter</option>
                            </select>
                            @error('performance_bond_type')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Issued By -->
                        <div class="mb-6">
                            <label for="issued_by" class="form-label">Issued By</label>
                            <input type="text"
                                   id="issued_by"
                                   name="issued_by"
                                   value="{{ old('issued_by', $contract->issued_by) }}"
                                   class="form-input"
                                   placeholder="which institution or bank">
                            @error('issued_by')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>



                        <!-- Performance Guarantee Section -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Performance Guarantee (Optional)</h3>

                            <!-- Currency Type -->
                            <div class="mb-6">
                                <label for="currency_type" class="form-label">Currency Type</label>
                                <select id="currency_type"
                                        name="currency_type"
                                        class="form-input">
                                    <option value="">Select Currency</option>
                                    <option value="Dollar" {{ old('currency_type', $contract->currency_type) == 'Dollar' ? 'selected' : '' }}>Dollar</option>
                                    <option value="ETB" {{ old('currency_type', $contract->currency_type) == 'ETB' ? 'selected' : '' }}>ETB</option>
                                </select>
                                @error('currency_type')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Amount -->
                            <div class="mb-6">
                                <label for="performance_guarantee_amount" class="form-label">Performance Guarantee Amount</label>
                                <input type="number"
                                       id="performance_guarantee_amount"
                                       name="performance_guarantee_amount"
                                       value="{{ old('performance_guarantee_amount', $contract->performance_guarantee_amount) }}"
                                       min="0"
                                       step="0.01"
                                       class="form-input"
                                       placeholder="Enter amount">
                                @error('performance_guarantee_amount')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- End Date -->
                            <div class="mb-6">
                                <label for="performance_guarantee_end_date" class="form-label">Performance Guarantee End Date</label>
                                <input type="date"
                                       id="performance_guarantee_end_date"
                                       name="performance_guarantee_end_date"
                                       value="{{ old('performance_guarantee_end_date', $contract->performance_guarantee_end_date?->format('Y-m-d')) }}"
                                       class="form-input">
                                <p class="text-sm text-gray-500 mt-1">Use the "Extend Performance Guarantee" feature to track extensions properly</p>
                                @error('performance_guarantee_end_date')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Alert Days -->
                            <div class="mb-6">
                                <label for="performance_guarantee_alert_days" class="form-label">Alert Days Before Performance Guarantee End</label>
                                <input type="number"
                                       id="performance_guarantee_alert_days"
                                       name="performance_guarantee_alert_days"
                                       value="{{ old('performance_guarantee_alert_days', $contract->performance_guarantee_alert_days) }}"
                                       min="1"
                                       max="365"
                                       class="form-input"
                                       placeholder="Number of days before performance guarantee end date to trigger alert">
                                <p class="text-sm text-gray-500 mt-1">Number of days before performance guarantee end date to trigger alert</p>
                                @error('performance_guarantee_alert_days')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                            <a href="{{ route('contracts.show', $contract) }}"
                               class="px-6 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                                Cancel
                            </a>
                            <button type="submit"
                                    class="bg-[#1C75BC] hover:bg-[#1664a0] text-white font-medium px-6 py-2 rounded-lg transition-colors">
                                Update Contract
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Help Section -->
            <div class="lg:col-span-1">
                <div class="form-card p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Contract Editing Guidelines</h3>

                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="bg-blue-100 p-2 rounded-lg mr-3">
                                <i class="fas fa-info-circle text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800">Contract Details</h4>
                                <p class="text-sm text-gray-600 mt-1">Update the required fields marked with *</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="bg-green-100 p-2 rounded-lg mr-3">
                                <i class="fas fa-calendar-alt text-green-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800">Date Validation</h4>
                                <p class="text-sm text-gray-600 mt-1">End date must be after start date</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="bg-yellow-100 p-2 rounded-lg mr-3">
                                <i class="fas fa-bell text-yellow-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800">Alert System</h4>
                                <p class="text-sm text-gray-600 mt-1">System will notify you before contract and performance guarantee expiry</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="bg-purple-100 p-2 rounded-lg mr-3">
                                <i class="fas fa-shield-alt text-purple-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800">Performance Guarantee</h4>
                                <p class="text-sm text-gray-600 mt-1">Optional field for contracts requiring performance bonds or guarantees</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="bg-orange-100 p-2 rounded-lg mr-3">
                                <i class="fas fa-history text-orange-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800">Extension Tracking</h4>
                                <p class="text-sm text-gray-600 mt-1">Use dedicated extension features to properly track contract modifications</p>
                            </div>
                        </div>
                    </div>

                    <!-- Contract Info -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <h4 class="font-medium text-gray-800 mb-3">Contract Information</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">ID:</span>
                                <span class="text-sm font-medium">#{{ $contract->id }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Created:</span>
                                <span class="text-sm font-medium">{{ $contract->created_at->format('M d, Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Extensions:</span>
                                <span class="text-sm font-medium">{{ $contract->extended_count ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Status:</span>
                                <span class="text-sm font-medium">
                                    <span class="badge
                                        @if($contract->status === 'active') bg-success
                                        @elseif($contract->status === 'expiring') bg-warning text-dark
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
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Function to validate Alert Days when dates change or user inputs value
        function validateAlertDays() {
            let startDate = new Date(document.getElementById("start_date").value);
            let endDate = new Date(document.getElementById("end_date").value);
            let alertDaysInput = document.getElementById("alert_days");
            let alertDaysError = document.getElementById("alert_days_error");

            // Clear previous validation
            alertDaysInput.classList.remove('border-red-500');
            alertDaysError.style.display = 'none';
            alertDaysError.textContent = '';

            if (startDate && endDate && endDate >= startDate && alertDaysInput.value !== '') {
                let alertDays = parseInt(alertDaysInput.value);

                // Check if it's a valid number
                if (isNaN(alertDays)) {
                    alertDaysInput.classList.add('border-red-500');
                    alertDaysError.textContent = 'Alert days must be a valid number.';
                    alertDaysError.style.display = 'block';
                    return;
                }

                let contractDuration = Math.floor((endDate - startDate) / (1000 * 60 * 60 * 24));

                if (alertDays < 0) {
                    alertDaysInput.classList.add('border-red-500');
                    alertDaysError.textContent = 'Alert days cannot be negative.';
                    alertDaysError.style.display = 'block';
                } else if (alertDays > contractDuration) {
                    alertDaysInput.classList.add('border-red-500');
                    alertDaysError.textContent = `Alert days (${alertDays}) cannot be greater than the contract duration (${contractDuration} days).`;
                    alertDaysError.style.display = 'block';
                }
            }
        }

        // Add event listeners for date changes
        document.getElementById('start_date').addEventListener('change', validateAlertDays);
        document.getElementById('end_date').addEventListener('change', validateAlertDays);

        // Add event listeners for alert days input
        document.getElementById('alert_days').addEventListener('input', validateAlertDays);
        document.getElementById('alert_days').addEventListener('blur', validateAlertDays);

        // Form validation
        const form = document.getElementById('contractForm');
        form.addEventListener('submit', function(e) {
            // First, run validation on all fields
            validateAlertDays();

            const startDate = new Date(document.getElementById('start_date').value);
            const endDate = new Date(document.getElementById('end_date').value);

            if (endDate <= startDate) {
                e.preventDefault();
                alert('End date must be after start date.');
                document.getElementById('end_date').focus();
                return false;
            }

            // Check for any inline errors after validation
            const alertDaysError = document.getElementById('alert_days_error').style.display === 'block';

            if (alertDaysError) {
                e.preventDefault();
                alert('Please fix the validation errors before submitting.');
                document.getElementById('alert_days').focus();
                return false;
            }

            // Final validation: double-check alert_days against contract duration
            const alertDaysInput = document.getElementById('alert_days');
            const alertDays = parseInt(alertDaysInput.value);

            if (startDate && endDate && endDate > startDate && !isNaN(alertDays)) {
                const contractDuration = Math.floor((endDate - startDate) / (1000 * 60 * 60 * 24));
                if (alertDays > contractDuration) {
                    e.preventDefault();
                    alert(`Alert days (${alertDays}) cannot be greater than the contract duration (${contractDuration} days).`);
                    alertDaysInput.focus();
                    return false;
                }
            }

            // Performance guarantee validation
            const pgAmount = document.getElementById('performance_guarantee_amount').value;
            const currencyType = document.getElementById('currency_type').value;
            const pgEndDate = document.getElementById('performance_guarantee_end_date').value;
            const pgAlertDays = document.getElementById('performance_guarantee_alert_days').value;

            if (pgAmount && !currencyType) {
                e.preventDefault();
                alert('Currency type is required when entering performance guarantee amount.');
                document.getElementById('currency_type').focus();
                return false;
            }

            if ((pgAmount || pgEndDate || pgAlertDays) && (!pgAmount || !pgEndDate)) {
                e.preventDefault();
                alert('If you provide performance guarantee details, both amount and end date are required.');
                return false;
            }

            if (pgEndDate && new Date(pgEndDate) <= startDate) {
                e.preventDefault();
                alert('Performance guarantee end date must be after contract start date.');
                return false;
            }

            return true;
        });

        // Auto-fill performance guarantee alert days if end date is provided
        const pgEndDateInput = document.getElementById('performance_guarantee_end_date');
        const pgAlertDaysInput = document.getElementById('performance_guarantee_alert_days');

        pgEndDateInput.addEventListener('change', function() {
            if (this.value && !pgAlertDaysInput.value) {
                pgAlertDaysInput.value = 30;
            }
        });
    });
</script>
@endsection
