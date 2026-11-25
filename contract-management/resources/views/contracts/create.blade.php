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
    
    /* Autocomplete Styles */
    .autocomplete-container {
        position: relative;
    }
    
    .autocomplete-results {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #E5E7EB;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        max-height: 200px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
    }
    
    .autocomplete-item {
        padding: 0.75rem 1rem;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    
    .autocomplete-item:hover {
        background-color: #F3F4F6;
    }
    
    .autocomplete-item.selected {
        background-color: #EFF6FF;
        border-left: 3px solid #1C75BC;
    }
    
    .supplier-info {
        font-size: 0.875rem;
        color: #6B7280;
        margin-top: 0.25rem;
    }
    
    .new-supplier-notice {
        background-color: #F0FDF4;
        border: 1px solid #BBF7D0;
        border-radius: 0.5rem;
        padding: 0.75rem;
        margin-top: 0.5rem;
        display: none;
    }
    
    .supplier-actions {
        display: flex;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }
    
    .btn-sm {
        padding: 0.25rem 0.75rem;
        font-size: 0.875rem;
        border-radius: 0.375rem;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .btn-primary {
        background-color: #1C75BC;
        color: white;
        border: 1px solid #1C75BC;
    }
    
    .btn-primary:hover {
        background-color: #1664a0;
    }
    
    .btn-secondary {
        background-color: #6B7280;
        color: white;
        border: 1px solid #6B7280;
    }
    
    .btn-secondary:hover {
        background-color: #4B5563;
    }
    
    .hidden {
        display: none;
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
                    @if ($errors->any())
                        <div class="alert alert-danger mb-6" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('contracts.store') }}" id="contractForm">
                        @csrf
                        
                        <!-- Hidden field for supplier ID -->
                        <input type="hidden" id="supplier_id" name="supplier_id" value="{{ old('supplier_id') }}">
                        
                        <!-- Supplier Name with Autocomplete -->
                        <div class="mb-6">
                            <label for="supplier_name" class="form-label required">Supplier Name</label>
                            <div class="autocomplete-container">
                                <input type="text" 
                                       id="supplier_name" 
                                       name="supplier_name" 
                                       value="{{ old('supplier_name') }}"
                                       class="form-input"
                                       placeholder="Start typing supplier name..."
                                       autocomplete="off"
                                       required>
                                <div id="autocomplete-results" class="autocomplete-results"></div>
                            </div>
                            <div id="new-supplier-notice" class="new-supplier-notice">
                                <p class="text-sm text-green-700">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    This appears to be a new supplier. A new supplier record will be created.
                                </p>
                            </div>
                            <div class="supplier-actions hidden" id="supplier-actions">
                                <button type="button" id="use-suggested-supplier" class="btn-sm btn-primary">
                                    <i class="fas fa-check mr-1"></i> Use Selected Supplier
                                </button>
                                <button type="button" id="create-new-supplier" class="btn-sm btn-secondary">
                                    <i class="fas fa-plus mr-1"></i> Create New Anyway
                                </button>
                            </div>
                            @error('supplier_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            @error('supplier_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
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
                        
                        @include('contracts.partials.material-group')
                        
                        <!-- Contract Amount -->
                        <div class="mb-6">
                            <label for="contract_amount" class="form-label">Contract Amount (ETB)</label>
                            <input type="number"
                                   id="contract_amount"
                                   name="contract_amount"
                                   min="0"
                                   step="0.01"
                                   class="form-input"
                                   value="{{ old('contract_amount') }}"
                                   placeholder="e.g. 250000.00">
                            @error('contract_amount')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        @include('contracts.partials.department')

                        @include('contracts.partials.contract-type')
                        
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
                                <div class="invalid-feedback d-block" id="start_date_error"></div>
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
                                <div class="invalid-feedback d-block" id="end_date_error"></div>
                                @error('end_date')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        @include('contracts.partials.date-sequence')
                        
                        <!-- Alert Days -->
                        <div class="mb-6">
                            <label for="alert_days" class="form-label required">Alert Days Before Contract End</label>
                            <input type="number"
                                   id="alert_days"
                                   name="alert_days"
                                   value="{{ old('alert_days') }}"
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
                        @include('contracts.partials.performance-bond-type')

                        <!-- Issued By -->
                        <div class="mb-6">
                            <label for="issued_by" class="form-label">Issued By</label>
                            <input type="text"
                                   id="issued_by"
                                   name="issued_by"
                                   value="{{ old('issued_by') }}"
                                   class="form-input"
                                   placeholder="which institution or bank">
                            @error('issued_by')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Performance Guarantee Section -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Performance Guarantee (Optional)</h3>

                            @include('contracts.partials.currency-type')

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
                                <div class="invalid-feedback d-block" id="performance_guarantee_end_date_error"></div>
                                @error('performance_guarantee_end_date')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Alert Days -->
                            <div class="mb-6">
                                <label for="alert_days_before_guarantee_end" class="form-label">{{ __('performance_guarantee.alert_days_label') }}</label>
                                <input type="number" 
                                       id="alert_days_before_guarantee_end" 
                                       name="alert_days_before_guarantee_end" 
                                       value="{{ old('alert_days_before_guarantee_end') }}"
                                       min="0" 
                                       max="365"
                                       class="form-input"
                                       placeholder="{{ __('performance_guarantee.alert_days_placeholder') }}">
                                <p class="text-sm text-gray-500 mt-1">{{ __('performance_guarantee.alert_days_help') }}</p>
                                @error('alert_days_before_guarantee_end')
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
                                <h4 class="font-medium text-gray-800">Supplier Selection</h4>
                                <p class="text-sm text-gray-600 mt-1">Type supplier name - system will suggest existing suppliers or create new one</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="bg-green-100 p-2 rounded-lg mr-3">
                                <i class="fas fa-building text-green-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800">Supplier Management</h4>
                                <p class="text-sm text-gray-600 mt-1">If supplier exists, select from list. New suppliers are automatically created.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="bg-yellow-100 p-2 rounded-lg mr-3">
                                <i class="fas fa-calendar-alt text-yellow-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800">Date Validation</h4>
                                <p class="text-sm text-gray-600 mt-1">End date must be after start date</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="bg-purple-100 p-2 rounded-lg mr-3">
                                <i class="fas fa-shield-alt text-purple-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800">Performance Guarantee</h4>
                                <p class="text-sm text-gray-600 mt-1">Optional field for contracts requiring performance bonds</p>
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

        // Supplier Autocomplete Logic
        const supplierInput = document.getElementById('supplier_name');
        const supplierIdInput = document.getElementById('supplier_id');
        const autocompleteResults = document.getElementById('autocomplete-results');
        const newSupplierNotice = document.getElementById('new-supplier-notice');
        const supplierActions = document.getElementById('supplier-actions');
        const useSuggestedBtn = document.getElementById('use-suggested-supplier');
        const createNewBtn = document.getElementById('create-new-supplier');
        
        let selectedSupplier = null;
        let isExistingSupplier = false;
        let searchTimeout = null;

        // Function to search suppliers
        function searchSuppliers(query) {
            if (query.length < 2) {
                hideAutocomplete();
                return;
            }

            fetch(`/api/suppliers/search?q=${encodeURIComponent(query)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                displayAutocompleteResults(data);
            })
            .catch(error => {
                console.error('Error searching suppliers:', error);
            });
        }

        // Function to display autocomplete results
        function displayAutocompleteResults(suppliers) {
            autocompleteResults.innerHTML = '';
            
            if (suppliers.length === 0) {
                const item = document.createElement('div');
                item.className = 'autocomplete-item';
                item.innerHTML = `
                    <div class="font-medium">No matching suppliers found</div>
                    <div class="supplier-info">This will be created as a new supplier</div>
                `;
                autocompleteResults.appendChild(item);
                showAutocomplete();
                return;
            }
            
            suppliers.forEach(supplier => {
                const item = document.createElement('div');
                item.className = 'autocomplete-item';
                item.dataset.id = supplier.id;
                item.dataset.name = supplier.name;
                item.innerHTML = `
                    <div class="font-medium">${supplier.name}</div>
                    ${supplier.email ? `<div class="supplier-info">${supplier.email}</div>` : ''}
                    ${supplier.phone ? `<div class="supplier-info">${supplier.phone}</div>` : ''}
                `;
                
                item.addEventListener('click', function() {
                    selectSupplier(supplier.id, supplier.name);
                });
                
                autocompleteResults.appendChild(item);
            });
            
            showAutocomplete();
        }

        // Function to select a supplier
        function selectSupplier(id, name) {
            supplierInput.value = name;
            supplierIdInput.value = id;
            selectedSupplier = { id, name };
            isExistingSupplier = true;
            
            hideAutocomplete();
            newSupplierNotice.style.display = 'none';
            supplierActions.classList.add('hidden');
            
            // Show success message
            showMessage(`Using existing supplier: ${name}`, 'success');
        }

        // Function to show/hide autocomplete
        function showAutocomplete() {
            autocompleteResults.style.display = 'block';
        }

        function hideAutocomplete() {
            autocompleteResults.style.display = 'none';
        }

        // Function to show message
        function showMessage(text, type) {
            // You can implement a toast notification here
            console.log(`${type}: ${text}`);
        }

        // Event listeners for supplier input
        supplierInput.addEventListener('input', function() {
            const query = this.value.trim();
            
            // Clear previous timeout
            if (searchTimeout) {
                clearTimeout(searchTimeout);
            }
            
            // Hide actions and notice
            supplierActions.classList.add('hidden');
            newSupplierNotice.style.display = 'none';
            
            // Reset supplier ID if user starts typing again
            if (query.length > 0 && supplierIdInput.value) {
                supplierIdInput.value = '';
                selectedSupplier = null;
                isExistingSupplier = false;
            }
            
            // Search after delay
            searchTimeout = setTimeout(() => {
                if (query.length >= 2) {
                    searchSuppliers(query);
                } else {
                    hideAutocomplete();
                }
            }, 300);
        });

        supplierInput.addEventListener('focus', function() {
            const query = this.value.trim();
            if (query.length >= 2) {
                searchSuppliers(query);
            }
        });

        // Click outside to close autocomplete
        document.addEventListener('click', function(event) {
            if (!autocompleteResults.contains(event.target) && 
                !supplierInput.contains(event.target)) {
                hideAutocomplete();
            }
        });

        // Keyboard navigation for autocomplete
        supplierInput.addEventListener('keydown', function(event) {
            const items = autocompleteResults.querySelectorAll('.autocomplete-item');
            let currentIndex = -1;
            
            items.forEach((item, index) => {
                if (item.classList.contains('selected')) {
                    currentIndex = index;
                    item.classList.remove('selected');
                }
            });
            
            if (event.key === 'ArrowDown') {
                event.preventDefault();
                currentIndex = (currentIndex + 1) % items.length;
                items[currentIndex].classList.add('selected');
                items[currentIndex].scrollIntoView({ block: 'nearest' });
            } else if (event.key === 'ArrowUp') {
                event.preventDefault();
                currentIndex = currentIndex <= 0 ? items.length - 1 : currentIndex - 1;
                items[currentIndex].classList.add('selected');
                items[currentIndex].scrollIntoView({ block: 'nearest' });
            } else if (event.key === 'Enter' && currentIndex >= 0) {
                event.preventDefault();
                items[currentIndex].click();
            }
        });

        // Function to check if supplier might be existing
        function checkForExistingSupplier(name) {
            fetch(`/api/suppliers/check?name=${encodeURIComponent(name)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.similar.length > 0) {
                        // Show suggestions
                        displaySimilarSuppliers(data.similar);
                    } else {
                        // Show new supplier notice
                        newSupplierNotice.style.display = 'block';
                        supplierActions.classList.add('hidden');
                    }
                });
        }

        // Function to display similar suppliers
        function displaySimilarSuppliers(similarSuppliers) {
            autocompleteResults.innerHTML = '';
            
            similarSuppliers.forEach(supplier => {
                const item = document.createElement('div');
                item.className = 'autocomplete-item';
                item.dataset.id = supplier.id;
                item.dataset.name = supplier.name;
                item.innerHTML = `
                    <div class="font-medium">${supplier.name}</div>
                    <div class="supplier-info">Similar to what you typed. Click to select.</div>
                `;
                
                item.addEventListener('click', function() {
                    selectSupplier(supplier.id, supplier.name);
                });
                
                autocompleteResults.appendChild(item);
            });
            
            showAutocomplete();
        }

        // When user stops typing (on blur), check for similar suppliers
        supplierInput.addEventListener('blur', function() {
            const query = this.value.trim();
            if (query.length >= 3 && !supplierIdInput.value) {
                setTimeout(() => {
                    checkForExistingSupplier(query);
                }, 500);
            }
        });

        // Button event listeners
        if (useSuggestedBtn) {
            useSuggestedBtn.addEventListener('click', function() {
                // This would be triggered when we show similar suppliers
                // For now, just hide the actions
                supplierActions.classList.add('hidden');
            });
        }

        if (createNewBtn) {
            createNewBtn.addEventListener('click', function() {
                // Force create new supplier
                supplierIdInput.value = '';
                isExistingSupplier = false;
                supplierActions.classList.add('hidden');
                newSupplierNotice.style.display = 'block';
                showMessage('New supplier will be created', 'info');
            });
        }

        // Update form submission to handle supplier logic
        const form = document.getElementById('contractForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                // First, run validation on all fields
                validateAlertDays();

                // Validate supplier
                const supplierName = supplierInput.value.trim();
                if (!supplierName) {
                    e.preventDefault();
                    alert('Supplier Name is required.');
                    supplierInput.focus();
                    return false;
                }

                // If no supplier ID is set but name exists, we'll create a new supplier
                if (!supplierIdInput.value && supplierName) {
                    // Add a flag to indicate new supplier
                    const newSupplierFlag = document.createElement('input');
                    newSupplierFlag.type = 'hidden';
                    newSupplierFlag.name = 'create_new_supplier';
                    newSupplierFlag.value = '1';
                    form.appendChild(newSupplierFlag);
                }

                // Continue with other validations...
                const startDate = new Date(startDateInput.value);
                const endDate = new Date(endDateInput.value);

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
                const pgAlertDays = document.getElementById('alert_days_before_guarantee_end').value;

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

                return true;
            });
        }

        // Function to validate Alert Days when dates change or user inputs value
        function validateAlertDays() {
            let startDate = new Date(document.getElementById("start_date").value);
            let endDate = new Date(document.getElementById("end_date").value);
            let alertDaysInput = document.getElementById("alert_days");
            let alertDaysError = document.getElementById("alert_days_error");

            // Clear previous validation
            if (alertDaysInput) alertDaysInput.classList.remove('border-red-500');
            if (alertDaysError) {
                alertDaysError.style.display = 'none';
                alertDaysError.textContent = '';
            }

            if (startDate && endDate && endDate >= startDate && alertDaysInput && alertDaysInput.value !== '') {
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
        if (startDateInput) startDateInput.addEventListener('change', validateAlertDays);
        if (endDateInput) endDateInput.addEventListener('change', validateAlertDays);

        // Add event listeners for alert days input
        const alertDaysInputField = document.getElementById('alert_days');
        if (alertDaysInputField) {
            alertDaysInputField.addEventListener('input', validateAlertDays);
            alertDaysInputField.addEventListener('blur', validateAlertDays);
        }
        
        // Auto-fill performance guarantee alert days if end date is provided
        const pgEndDateInput = document.getElementById('performance_guarantee_end_date');
        const pgAlertDaysInput = document.getElementById('alert_days_before_guarantee_end');
        
        if (pgEndDateInput && pgAlertDaysInput) {
            pgEndDateInput.addEventListener('change', function() {
                if (this.value && !pgAlertDaysInput.value) {
                    pgAlertDaysInput.value = 30;
                }
            });
        }
        
        // Department auto-selection based on user role
        const userRole = "{{ auth()->user()->role }}";
        const departmentSelect = document.getElementById('department');

        if (form && departmentSelect) {
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
        }
    });
</script>
@endsection