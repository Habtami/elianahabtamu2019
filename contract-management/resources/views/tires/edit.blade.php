@extends('layouts.app')

@section('title', 'Edit Tire')

@section('page-title', 'Edit Tire')

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
</style>
@endsection

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-800">Edit Tire</h1>
                    <p class="text-gray-600 mt-2">Update tire details</p>
                </div>
                <a href="{{ route('tires.index') }}" class="text-gray-600 hover:text-gray-800 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Tires
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <div class="form-card p-6">
                    <form method="POST" action="{{ route('tires.update', $tire) }}" id="tireForm" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Tire Type -->
                        <div class="mb-6">
                            <label for="tire_type" class="form-label required">Tire Type</label>
                             <select id="tire_type" name="tire_type" class="form-input" required>
                                <option value="">Select Tire Type</option>
                                @php
                                    $hasOther = $tireTypes->contains('name', 'Other');
                                @endphp
                                @foreach($tireTypes->groupBy('category') as $category => $types)
                                    <optgroup label="{{ $category ?: 'All Types' }}">
                                        @foreach($types as $type)
                                            <option value="{{ $type->name }}"
                                                {{ old('tire_type', $tire->tire_type) == $type->name ? 'selected' : '' }}>
                                                {{ $type->name }} {{ $type->season ? ' ('.$type->season.')' : '' }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                                @if(!$hasOther)
                                <option value="Other" {{ old('tire_type', $tire->tire_type) == 'Other' ? 'selected' : '' }}>Other</option>
                                @endif
                            </select>
                            @error('tire_type')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Serial Number (Locked) -->
                        <div class="mb-6">
                            <label for="serial_number" class="form-label required">Serial Number</label>
                            <input type="text" id="serial_number" name="serial_number" value="{{ $tire->serial_number }}" class="form-input" readonly>
                            <p class="text-sm text-gray-500 mt-1">Locked - cannot be edited once recorded</p>
                        </div>

                        <!-- Plate Number -->
                        <div class="mb-6">
                            <label for="plate_number" class="form-label">Plate Number</label>
                            <input type="text" id="plate_number" name="plate_number" value="{{ old('plate_number', $tire->plate_number) }}" class="form-input" placeholder="Enter plate number">
                            @error('plate_number')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Initial Kilometer -->
                        <div class="mb-6">
                            <label for="initial_kilometer" class="form-label required">Initial Kilometer</label>
                            <input type="number" id="initial_kilometer" name="initial_kilometer" value="{{ old('initial_kilometer', $tire->initial_kilometer) }}" class="form-input" placeholder="Enter initial kilometer" min="0" required>
                            @error('initial_kilometer')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Photo -->
                        <div class="mb-6">
                            <label for="photo" class="form-label">Tire Photo</label>
                            <input type="file" id="photo" name="photo" class="form-input" accept="image/*">
                            @if($tire->photo)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $tire->photo) }}" alt="Tire Photo" class="w-20 h-20 object-cover rounded">
                                </div>
                            @endif
                            @error('photo')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Active Status -->
                        <div class="mb-6 flex items-center">
                            <input type="checkbox" id="is_active" name="is_active" value="1" {{ $tire->is_active ? 'checked' : '' }}>
                            <label for="is_active" class="form-label mb-0 ml-2">Active tire</label>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                            <a href="{{ route('tires.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                                Cancel
                            </a>
                            <button type="submit" class="bg-[#1C75BC] hover:bg-[#1664a0] text-white font-medium px-6 py-2 rounded-lg transition-colors">
                                Update Tire
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection