@extends('layouts.app')

@section('title', 'Return Tire')

@section('page-title', 'Return Tire')

@section('styles')
<style>
    :root {
        --epss-blue: #1C75BC;
        --success: #10B981;
        --warning: #F59E0B;
        --danger: #EF4444;
    }
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
    .form-input[readonly] {
        background-color: #F3F4F6;
        cursor: not-allowed;
    }
    .info-box {
        background: linear-gradient(135deg, #F0F9FF 0%, #E0F2FE 100%);
        border: 1px solid #BAE6FD;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-top: 0.5rem;
    }
</style>
@endsection

@section('content')
<div class="page-container py-8">
    <div class="container mx-auto px-4">
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-800">Return Tire</h1>
                    <p class="text-gray-600 mt-2">Record tire return operation</p>
                </div>
                <a href="{{ route('tires.show', $tireGive->tire) }}" class="text-gray-600 hover:text-gray-800 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Tire
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <div class="form-card p-6">
                    <form method="POST" action="{{ route('tires.return.store', $tireGive) }}" id="returnForm" enctype="multipart/form-data">
                        @csrf

                        <!-- Tire Info (Auto-filled / Read-only) -->
                        <div class="info-box">
                            <h3 class="font-semibold text-gray-800 mb-3">Tire Information</h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                <div>
                                    <label class="text-gray-500">Serial Number</label>
                                    <div class="font-medium text-gray-800">{{ $tireGive->tire->serial_number }}</div>
                                </div>
                                <div>
                                    <label class="text-gray-500">Tire Type</label>
                                    <input type="text" name="tire_type" value="{{ $tireGive->tire->tire_type }}" class="form-input form-input-sm text-sm" readonly>
                                </div>
                                <div>
                                    <label class="text-gray-500">Plate Number</label>
                                    <input type="text" name="plate_number" value="{{ $tireGive->plate_number }}" class="form-input form-input-sm text-sm" readonly>
                                </div>
                                <div>
                                    <label class="text-gray-500">Given To</label>
                                    <div class="font-medium text-gray-800">{{ $tireGive->driver->name }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Return Date -->
                        <div class="mb-6 mt-6">
                            <label for="return_date" class="form-label required">Return Date</label>
                            <input type="date" id="return_date" name="return_date" value="{{ old('return_date', now()->format('Y-m-d')) }}" class="form-input" required>
                            @error('return_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Current Kilometer -->
                        <div class="mb-6">
                            <label for="current_kilometer" class="form-label required">Current Kilometer</label>
                            <input type="number" id="current_kilometer" name="current_kilometer" value="{{ old('current_kilometer') }}" class="form-input" placeholder="Enter current kilometer" min="{{ $tireGive->tire->initial_kilometer ?? 0 }}" required onkeyup="calculateKmDifference()">
                            <p class="text-sm text-gray-500 mt-1">Must be greater than {{ number_format($tireGive->tire->initial_kilometer ?? 0) }} km (initial KM)</p>
                            @error('current_kilometer')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Registered Kilometer (Auto) -->
                        <div class="mb-6">
                            <label for="registered_kilometer" class="form-label">Registered Kilometer</label>
                            <input type="number" id="registered_kilometer" name="registered_kilometer" value="{{ $tireGive->given_kilometer }}" class="form-input" readonly>
                            <p class="text-sm text-gray-500 mt-1">From the tire give record</p>
                        </div>

                        <!-- KM Difference (Auto-calc) -->
                        <div class="mb-6">
                            <label for="km_difference" class="form-label">KM Difference</label>
                            <input type="number" id="km_difference" name="km_difference" value="{{ old('km_difference', 0) }}" class="form-input" readonly>
                            <p class="text-sm text-gray-500 mt-1">Auto-calculated: current - registered</p>
                        </div>

                        <!-- Photo -->
                        <div class="mb-6">
                            <label for="photo" class="form-label required">Returned Tire Photo</label>
                            <input type="file" id="photo" name="photo" class="form-input" accept="image/*" required>
                            <p class="text-sm text-gray-500 mt-1">Upload return photo as evidence (required)</p>
                            @error('photo')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="mb-6">
                            <label for="notes" class="form-label">Notes (Optional)</label>
                            <textarea id="notes" name="notes" rows="3" class="form-input" placeholder="Enter any notes...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                            <a href="{{ route('tires.show', $tireGive->tire) }}" class="px-6 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                                Cancel
                            </a>
                            <button type="submit" class="bg-[#1C75BC] hover:bg-[#1664a0] text-white font-medium px-6 py-2 rounded-lg transition-colors">
                                Complete Return
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
function calculateKmDifference() {
    const current = parseInt(document.getElementById('current_kilometer').value) || 0;
    const registered = parseInt(document.getElementById('registered_kilometer').value) || 0;
    document.getElementById('km_difference').value = current - registered;
}
</script>
@endsection
