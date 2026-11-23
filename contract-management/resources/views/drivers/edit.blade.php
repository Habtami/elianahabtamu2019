@extends('layouts.app')

@section('title', 'Edit Driver')

@section('page-title', 'Edit Driver')

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
    .required::after {
        content: ' *';
        color: #EF4444;
    }
</style>
@endsection

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-800">Edit Driver</h1>
                    <p class="text-gray-600 mt-2">Update driver details</p>
                </div>
                <a href="{{ route('drivers.index') }}" class="text-gray-600 hover:text-gray-800 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Drivers
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <div class="form-card p-6">
                    <form method="POST" action="{{ route('drivers.update', $driver) }}" id="driverForm" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div class="mb-6">
                            <label for="name" class="form-label required">Full Name</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $driver->name) }}" class="form-input" placeholder="Enter driver name" required>
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- License Number -->
                        <div class="mb-6">
                            <label for="license_number" class="form-label">License Number</label>
                            <input type="text" id="license_number" name="license_number" value="{{ old('license_number', $driver->license_number) }}" class="form-input" placeholder="Enter license number">
                            @error('license_number')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div class="mb-6">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $driver->phone) }}" class="form-input" placeholder="Enter phone number">
                            @error('phone')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-6">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $driver->email) }}" class="form-input" placeholder="Enter email address">
                            @error('email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- License Expiry -->
                        <div class="mb-6">
                            <label for="license_expiry" class="form-label">License Expiry Date</label>
                            <input type="date" id="license_expiry" name="license_expiry" value="{{ old('license_expiry', $driver->license_expiry ? $driver->license_expiry->format('Y-m-d') : '') }}" class="form-input">
                            @error('license_expiry')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Photo -->
                        <div class="mb-6">
                            <label for="photo" class="form-label">Driver Photo</label>
                            <input type="file" id="photo" name="photo" class="form-input" accept="image/*">
                            @if($driver->photo)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $driver->photo) }}" alt="Driver Photo" class="w-20 h-20 rounded-full object-cover">
                                </div>
                            @endif
                            @error('photo')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Active Status -->
                        <div class="mb-6 flex items-center">
                            <input type="checkbox" id="status" name="status" value="1" {{ $driver->status ? 'checked' : '' }}>
                            <label for="status" class="form-label mb-0 ml-2">Active driver</label>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                            <a href="{{ route('drivers.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                                Cancel
                            </a>
                            <button type="submit" class="bg-[#1C75BC] hover:bg-[#1664a0] text-white font-medium px-6 py-2 rounded-lg transition-colors">
                                Update Driver
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection