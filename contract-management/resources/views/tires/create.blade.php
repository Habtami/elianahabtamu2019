@extends('layouts.app')

@section('title', 'Register New Tire')

@section('page-title', 'Register New Tire')

@section('styles')
<style>
    :root {
        --epss-blue: #1C75BC;
        --epss-blue-light: #E8F4FD;
        --epss-blue-dark: #1664a0;
        --success: #10B981;
        --warning: #F59E0B;
        --danger: #EF4444;
        --info: #3B82F6;
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

    .photo-preview {
        width: 100px;
        height: 100px;
        border-radius: 0.5rem;
        object-fit: cover;
        border: 2px dashed #D1D5DB;
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
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-800">Register New Tire</h1>
                    <p class="text-gray-600 mt-2">Enter tire details to register it into the system</p>
                </div>
                <a href="{{ route('tires.index') }}" class="text-gray-600 hover:text-gray-800 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Tires
                </a>
            </div>
        </div>

        <!-- Form -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <div class="form-card p-6">
                    <form method="POST" action="{{ route('tires.store') }}" id="tireForm" enctype="multipart/form-data">
                        @csrf

                        <!-- Tire Type -->
                        <div class="mb-6">
                            <label for="tire_type" class="form-label required">Tire Type</label>
                            <div class="flex gap-2">
                                <select id="tire_type" name="tire_type" class="form-input flex-1" required>
                                    <option value="">Select Tire Type</option>
                                @php
                                    $hasOther = $tireTypes->contains('name', 'Other');
                                @endphp
                                @foreach($tireTypes->groupBy('category') as $category => $types)
                                    <optgroup label="{{ $category ?: 'All Types' }}">
                                        @foreach($types as $type)
                                            <option value="{{ $type->name }}" 
                                                data-spec="{{ $type->formatted_spec ?? ($type->width ? $type->width . '/' . $type->aspect_ratio . 'R' . $type->rim_diameter : '') }}"
                                                data-code="{{ $type->code }}"
                                                {{ old('tire_type') == $type->name ? 'selected' : (isset($tire) && $tire->tire_type == $type->name ? 'selected' : '') }}>
                                                {{ $type->name }} {{ $type->season ? ' ('.$type->season.')' : '' }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                                @if(!$hasOther)
                                <option value="Other" {{ old('tire_type') == 'Other' ? 'selected' : '' }}>Other</option>
                                @endif
                                </select>
                                <div class="flex gap-1">
                                    <button type="button" id="addTypeBtn" class="px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition-colors"
                                        title="Add new tire type" {{ auth()->user()->canManageTires() ? '' : 'style="display:none"' }}>
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <div id="tireTypeSpec" class="mt-2 text-sm text-gray-500 hidden">
                                <span id="specText"></span>
                                <span id="codeText" class="text-gray-400"></span>
                            </div>
                            <p class="text-sm text-gray-500 mt-1">
                                <a href="{{ route('tires.types.index') }}" class="text-[#1C75BC] hover:text-[#1664a0]">Manage tire types</a>
                            </p>
                            @error('tire_type')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const typeSelect = document.getElementById('tire_type');
                            const specDiv = document.getElementById('tireTypeSpec');
                            const specText = document.getElementById('specText');
                            const codeText = document.getElementById('codeText');

                            function updateSpec() {
                                const selected = typeSelect.options[typeSelect.selectedIndex];
                                const spec = selected.dataset.spec;
                                const code = selected.dataset.code;
                                if (spec) {
                                    specText.textContent = spec;
                                    specDiv.classList.remove('hidden');
                                    if (code) {
                                        codeText.textContent = ' (' + code + ')';
                                    } else {
                                        codeText.textContent = '';
                                    }
                                } else {
                                    specDiv.classList.add('hidden');
                                }
                            }

                            typeSelect.addEventListener('change', updateSpec);
                            updateSpec();

                            document.getElementById('addTypeBtn').addEventListener('click', function() {
                                window.location.href = "{{ route('tires.types.create') }}";
                            });
                        });
                        </script>

                        <!-- Serial Number -->
                        <div class="mb-6">
                            <label for="serial_number" class="form-label required">Serial Number</label>
                            <input type="text" id="serial_number" name="serial_number" value="{{ old('serial_number') }}" class="form-input" placeholder="Enter unique serial number" required>
                            <p class="text-sm text-gray-500 mt-1">This will be locked once recorded and cannot be edited later</p>
                            @error('serial_number')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Plate Number -->
                        <div class="mb-6">
                            <label for="plate_number" class="form-label">Plate Number</label>
                            <input type="text" id="plate_number" name="plate_number" value="{{ old('plate_number') }}" class="form-input" placeholder="Enter plate number">
                            @error('plate_number')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Initial Kilometer -->
                        <div class="mb-6">
                            <label for="initial_kilometer" class="form-label required">Initial Kilometer</label>
                            <input type="number" id="initial_kilometer" name="initial_kilometer" value="{{ old('initial_kilometer', 0) }}" class="form-input" placeholder="Enter initial kilometer" min="0" required>
                            @error('initial_kilometer')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Photo -->
                        <div class="mb-6">
                            <label for="photo" class="form-label">Tire Photo</label>
                            <input type="file" id="photo" name="photo" class="form-input" accept="image/*">
                            @error('photo')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Active Status -->
                        <div class="mb-6 flex items-center">
                            <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label for="is_active" class="form-label mb-0 ml-2">Active tire</label>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                            <a href="{{ route('tires.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                                Cancel
                            </a>
                            <button type="submit" class="bg-[#1C75BC] hover:bg-[#1664a0] text-white font-medium px-6 py-2 rounded-lg transition-colors">
                                Register Tire
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Help Section -->
            <div class="lg:col-span-1">
                <div class="form-card p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Tire Registration Guidelines</h3>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="bg-blue-100 p-2 rounded-lg mr-3">
                                <i class="fas fa-info-circle text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800">Serial Number</h4>
                                <p class="text-sm text-gray-600 mt-1">Must be unique. Once recorded, cannot be edited.</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="bg-green-100 p-2 rounded-lg mr-3">
                                <i class="fas fa-tire text-green-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800">Tire Type</h4>
                                <p class="text-sm text-gray-600 mt-1">Select from common brands or use Other.</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="bg-yellow-100 p-2 rounded-lg mr-3">
                                <i class="fas fa-camera text-yellow-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800">Photo</h4>
                                <p class="text-sm text-gray-600 mt-1">Upload tire photo for reference (optional).</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection