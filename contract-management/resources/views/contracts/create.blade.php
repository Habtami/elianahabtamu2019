@extends('layouts.app')

@section('title', 'Create New Contract')

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
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-800">Create New Contract</h1>
                    <p class="text-gray-600 mt-2">Fill in the contract details below</p>
                </div>
                <a href="{{ route('contracts.index') }}" class="text-gray-600 hover:text-gray-800 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Contracts
                </a>
            </div>
        </div>

        <!-- Form -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Form -->
            <div class="lg:col-span-2">
                <div class="form-card p-6">
                    <form method="POST" action="{{ route('contracts.store') }}" id="contractForm">
                        @csrf
                        
                        <!-- Contract Name -->
                        <div class="mb-6">
                            <label for="name" class="form-label required">Contract Name</label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}"
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
                                      placeholder="Enter contract description">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Department -->
                        <div class="mb-6">
                            <label for="department" class="form-label required">Department</label>
                            <select id="department" 
                                    name="department" 
                                    class="form-input"
                                    required>
                                <option value="">Select Department</option>
                                @foreach($departments as $key => $value)
                                    <option value="{{ $key }}" {{ old('department') == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department')
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
                                       value="{{ old('start_date') }}"
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
                                       value="{{ old('end_date') }}"
                                       class="form-input"
                                       required>
                                @error('end_date')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Alert Days -->
                        <div class="mb-6">
                            <label for="alert_days" class="form-label required">Alert Days Before Contract End</label>
                            <input type="number" 
                                   id="alert_days" 
                                   name="alert_days" 
                                   value="{{ old('alert_days', 30) }}"
                                   min="1" 
                                   max="365"
                                   class="form-input"
                                   placeholder="Number of days before contract end date to trigger alert"
                                   required>
                            <p class="text-sm text-gray-500 mt-1">Number of days before contract end date to trigger alert</p>
                            @error('alert_days')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Performance Guarantee Section -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Performance Guarantee (Optional)</h3>
                            
                            <!-- Amount -->
                            <div class="mb-6">
                                <label for="performance_guarantee_amount" class="form-label">Performance Guarantee Amount</label>
                                <input type="number" 
                                       id="performance_guarantee_amount" 
                                       name="performance_guarantee_amount" 
                                       value="{{ old('performance_guarantee_amount') }}"
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
                                       value="{{ old('performance_guarantee_end_date') }}"
                                       class="form-input">
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
                                       value="{{ old('performance_guarantee_alert_days', 30) }}"
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
                            <a href="{{ route('contracts.index') }}" 
                               class="px-6 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="bg-[#1C75BC] hover:bg-[#1664a0] text-white font-medium px-6 py-2 rounded-lg transition-colors">
                                Create Contract
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Help Section -->
            <div class="lg:col-span-1">
                <div class="form-card p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Contract Creation Guidelines</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="bg-blue-100 p-2 rounded-lg mr-3">
                                <i class="fas fa-info-circle text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800">Contract Details</h4>
                                <p class="text-sm text-gray-600 mt-1">Fill in all required fields marked with *</p>
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
                    </div>
                    
                    <!-- User Info -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <h4 class="font-medium text-gray-800 mb-3">Your Information</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Role:</span>
                                <span class="text-sm font-medium">{{ auth()->user()->getRoleDisplayName() }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Department:</span>
                                <span class="text-sm font-medium">{{ auth()->user()->getDepartmentDisplayName() }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Date:</span>
                                <span class="text-sm font-medium">{{ now()->format('M d, Y') }}</span>
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
        // Set default dates
        const today = new Date().toISOString().split('T')[0];
        const oneYearFromNow = new Date();
        oneYearFromNow.setFullYear(oneYearFromNow.getFullYear() + 1);
        const oneYearFromNowStr = oneYearFromNow.toISOString().split('T')[0];
        
        // Set default dates if not already set
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        
        if (!startDateInput.value) {
            startDateInput.value = today;
        }
        
        if (!endDateInput.value) {
            endDateInput.value = oneYearFromNowStr;
        }
        
        // Form validation
        const form = document.getElementById('contractForm');
        form.addEventListener('submit', function(e) {
            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);
            
            if (endDate <= startDate) {
                e.preventDefault();
                alert('End date must be after start date.');
                endDateInput.focus();
                return false;
            }
            
            // Performance guarantee validation
            const pgAmount = document.getElementById('performance_guarantee_amount').value;
            const pgEndDate = document.getElementById('performance_guarantee_end_date').value;
            const pgAlertDays = document.getElementById('performance_guarantee_alert_days').value;
            
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
        
        // Department auto-selection based on user role
        const userRole = "{{ auth()->user()->role }}";
        const departmentSelect = document.getElementById('department');
        const form = document.getElementById('contractForm');

        if (userRole === 'general_service') {
            departmentSelect.value = 'general_service';
            departmentSelect.disabled = true;
            // Add hidden input to ensure value is submitted
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'department';
            hiddenInput.value = 'general_service';
            form.appendChild(hiddenInput);
        } else if (userRole === 'cmd') {
            departmentSelect.value = 'cmd';
            departmentSelect.disabled = true;
            // Add hidden input to ensure value is submitted
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'department';
            hiddenInput.value = 'cmd';
            form.appendChild(hiddenInput);
        } else if (userRole === 'finance') {
            departmentSelect.value = 'finance';
            departmentSelect.disabled = true;
            // Add hidden input to ensure value is submitted
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'department';
            hiddenInput.value = 'finance';
            form.appendChild(hiddenInput);
        }
        // For managers and admins, leave the select enabled to choose any department
    });
</script>
@endsection