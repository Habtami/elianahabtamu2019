@extends('layouts.app')

@section('title', 'Give Tire')

@section('page-title', 'Give Tire to Driver2019')

@section('styles')
<style>
    :root {
        --epss-blue: #1C75BC;
        --success: #10B981;
        --warning: #F59E0B;
        --danger: #EF4444;
        --epss-blue: #1C75BC;
    }

    /* ---------- HEADER BAR ---------- */
    .header-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        padding: 1rem 1.5rem;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        border: 1px solid #EDF2F7;
        margin-bottom: 1.5rem;
    }
    .header-title h1 { font-size: 1.5rem; }
    .header-user {
        display: flex;
        align-items: center;
        gap: 1rem;
        font-size: 0.9rem;
    }
    .user-name {
        color: #1C75BC;
        font-weight: 600;
    }
    .user-role {
        color: #64748B;
        font-size: 0.85rem;
        padding: 0.25rem 0.75rem;
        background: #F1F5F9;
        border-radius: 9999px;
    }

    a { text-decoration: none !important; }

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
    .photo-preview {
        width: 100px;
        height: 100px;
        border-radius: 0.5rem;
        object-fit: cover;
        border: 2px dashed #D1D5DB;
    }
    @media (max-width: 768px) {
        .main-content { margin-left: 0 !important; }
        .page-header { margin-left: 0 !important; }
        .page-footer { margin-left: 0 !important; }
    }
</style>
@endsection

@section('content')
<div class="page-container py-8">
    <div class="container mx-auto px-4">
        {{-- ========== HEADER BAR ========== --}}
 

        {{-- ========== PAGE SUBTITLE & BACK LINK ========== --}}
        <div class="mb-6 flex justify-between items-center">
            <p class="text-gray-600">Register a tire give operation to a driver</p>
            <a href="{{ route('tires.index') }}" class="text-gray-600 hover:text-gray-800 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Tires
            </a>
        </div>

         <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2">
                    <div class="form-card p-6">
                        <form method="POST" action="{{ route('tires.give.store') }}" id="giveForm" enctype="multipart/form-data">
                        @csrf

                         <!-- Driver Name (Autocomplete) -->
                         <div class="mb-6">
                             <label for="driver_name" class="form-label required">Driver Name</label>
                             <div class="autocomplete-container">
                                 <input type="text" id="driver_name" name="driver_name" value="{{ old('driver_name') }}" class="form-input" placeholder="Start typing driver name..." autocomplete="off" required>
                                 <input type="hidden" id="driver_id" name="driver_id" value="{{ old('driver_id') }}">
                                 <div id="driver-autocomplete-results" class="autocomplete-results"></div>
                             </div>
                             @error('driver_id')
                                 <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                             @enderror
                         </div>

                         <!-- Tire Serial (New Serial Only) -->
                         <div class="mb-6">
                             <label for="tire_serial" class="form-label required">Tire Serial Number</label>
                             <input type="text" id="tire_serial" name="tire_serial" value="{{ old('tire_serial') }}" class="form-input" placeholder="Enter new serial number..." autocomplete="off" required>
                             <p class="text-sm text-gray-500 mt-1">Enter a new serial number. Each serial number is unique.</p>
                             @error('tire_serial')
                                 <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                             @enderror
                             @error('tire_id')
                                 <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                             @enderror
                         </div>

                        <!-- Tire Type -->
                        <div class="mb-6">
                            <label for="tire_type" class="form-label required">Tire Type</label>
                            <select id="tire_type" name="tire_type" class="form-input" required>
                                <option value="">Select Tire Type</option>
                                @foreach($tireTypes->groupBy('category') as $category => $types)
                                    <optgroup label="{{ $category ?: 'All Types' }}">
                                        @foreach($types as $type)
                                            <option value="{{ $type->name }}" {{ old('tire_type') == $type->name ? 'selected' : '' }}>
                                                {{ $type->name }} {{ $type->season ? ' ('.$type->season.')' : '' }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                                @php
                                    $hasOther = $tireTypes->contains('name', 'Other');
                                @endphp
                                @if(!$hasOther)
                                <option value="Other" {{ old('tire_type') == 'Other' ? 'selected' : '' }}>Other</option>
                                @endif
                            </select>
                            @error('tire_type')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                         <!-- Plate Number (Autocomplete) -->
                         <div class="mb-6">
                             <label for="plate_number" class="form-label">Plate Number</label>
                             <div class="autocomplete-container">
                                 <input type="text" id="plate_number" name="plate_number" value="{{ old('plate_number') }}" class="form-input" placeholder="Type to search plate numbers..." autocomplete="off">
                                 <div id="plate-autocomplete-results" class="autocomplete-results"></div>
                             </div>
                             <p class="text-sm text-gray-500 mt-1">Type to search existing plate numbers or enter a new one</p>
                             @error('plate_number')
                                 <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                             @enderror
                         </div>

                        <!-- Give Date -->
                        <div class="mb-6">
                            <label for="give_date" class="form-label required">Give Date</label>
                            <input type="date" id="give_date" name="give_date" value="{{ old('give_date', now()->format('Y-m-d')) }}" class="form-input" required>
                            @error('give_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Kilometer -->
                        <div class="mb-6">
                            <label for="given_kilometer" class="form-label required">Kilometer</label>
                            <input type="number" id="given_kilometer" name="given_kilometer" value="{{ old('given_kilometer') }}" class="form-input" placeholder="Enter current kilometer" min="0" required>
                            @error('given_kilometer')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                          <!-- Tire Photo -->
                          <div class="mb-6">
                              <label for="photo" class="form-label required">Tire Photo</label>
                              <input type="file" id="photo" name="photo" class="form-input" accept="image/*" required>
                              <p class="text-sm text-gray-500 mt-1">Upload tire photo as evidence (required)</p>
                              <div id="photo-preview-container" class="mt-3 hidden">
                                  <label class="form-label mb-2">Preview:</label>
                                  <div class="relative inline-block">
                                      <img id="photo-preview" src="" alt="Photo Preview" class="w-32 h-32 object-cover rounded-lg border-2 border-gray-200">
                                      <div id="photo-full-overlay" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50 hidden">
                                          <img id="photo-full" src="" alt="Full Photo" class="max-w-[80vw] max-h-[80vh] object-contain rounded-lg">
                                          <button type="button" id="photo-full-close" class="absolute top-4 right-4 text-white text-2xl">&times;</button>
                                      </div>
                                  </div>
                              </div>
                              @error('photo')
                                  <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                              @enderror
                          </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                            <a href="{{ route('tires.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                                Cancel
                            </a>
                            <button type="submit" class="bg-[#1C75BC] hover:bg-[#1664a0] text-white font-medium px-6 py-2 rounded-lg transition-colors">
                                Give Tire
                            </button>
                        </div>
                    </form>
                    </div>
                </div>

                <!-- Tire Give Guidelines (Right Side) -->
                <div class="lg:col-span-1">
                    <div class="form-card p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Tire Give Guidelines</h3>
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="bg-blue-100 p-2 rounded-lg mr-3">
                                    <i class="fas fa-user-friends text-blue-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-800">Driver Selection</h4>
                                    <p class="text-sm text-gray-600 mt-1">Select driver from existing list or it will be created.</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="bg-green-100 p-2 rounded-lg mr-3">
                                    <i class="fas fa-fingerprint text-green-600"></i>
                                </div>
                                <div>
                                     <h4 class="font-medium text-gray-800">Serial Number</h4>
                                     <p class="text-sm text-gray-600 mt-1">Serial must be unique. A new serial cannot be used twice.</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="bg-yellow-100 p-2 rounded-lg mr-3">
                                    <i class="fas fa-camera text-yellow-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-800">Photo</h4>
                                    <p class="text-sm text-gray-600 mt-1">Upload tire photo as evidence (required).</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
         </div>
    </div>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const driverInput = document.getElementById('driver_name');
    const driverIdInput = document.getElementById('driver_id');
    const driverResults = document.getElementById('driver-autocomplete-results');
    const plateInput = document.getElementById('plate_number');
    const plateResults = document.getElementById('plate-autocomplete-results');

    let driverTimeout, plateTimeout;
    driverInput.addEventListener('input', function() {
        const q = this.value.trim();
        clearTimeout(driverTimeout);
        if (q.length < 2) { driverResults.style.display = 'none'; return; }
        driverTimeout = setTimeout(() => {
            fetch(`/api/drivers/search?q=${encodeURIComponent(q)}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => displayDrivers(data));
        }, 300);
    });

    function displayDrivers(drivers) {
        driverResults.innerHTML = '';
        if (!drivers.length) {
            // Add option to create new driver
            const item = document.createElement('div');
            item.className = 'autocomplete-item';
            item.innerHTML = `<div class="font-medium text-green-600 flex items-center">Add new: "${driverInput.value}"</div>`;
            item.onclick = () => selectDriver({id: 'new', name: driverInput.value});
            driverResults.appendChild(item);
        } else {
            drivers.forEach(d => {
                const item = document.createElement('div');
                item.className = 'autocomplete-item';
                item.innerHTML = `<div class="font-medium">${d.name}</div><div class="text-xs text-gray-500">${d.license_number || ''}</div>`;
                item.onclick = () => selectDriver(d);
                driverResults.appendChild(item);
            });
            // Add option to create new driver at the end
            const newItem = document.createElement('div');
            newItem.className = 'autocomplete-item';
            newItem.innerHTML = `<div class="font-medium text-green-600">Add new: "${driverInput.value}"</div>`;
            newItem.onclick = () => selectDriver({id: 'new', name: driverInput.value});
            driverResults.appendChild(newItem);
        }
        driverResults.style.display = 'block';
    }

    function selectDriver(d) {
        driverInput.value = d.name;
        if (d.id === 'new') {
            // New driver - will be created on form submit
            driverIdInput.value = '';
        } else {
            driverIdInput.value = d.id;
        }
        driverResults.style.display = 'none';
    }

    // Plate autocomplete
    if (plateInput) {
        plateInput.addEventListener('input', function() {
            const q = this.value.trim();
            clearTimeout(plateTimeout);
            if (q.length < 1) {
                plateResults.style.display = 'none';
                return;
            }
            plateTimeout = setTimeout(() => {
                fetch(`/api/tires/search?q=${encodeURIComponent(q)}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                })
                .then(r => r.json())
                .then(data => displayPlates(data));
            }, 300);
        });

        function displayPlates(tires) {
            plateResults.innerHTML = '';
            const uniquePlates = [...new Set(tires.map(t => t.plate_number).filter(Boolean))];
            if (!uniquePlates.length) {
                const item = document.createElement('div');
                item.className = 'autocomplete-item';
                item.innerHTML = `<div class="font-medium text-green-600">New plate: "${plateInput.value}"</div>`;
                item.onclick = () => selectPlate(plateInput.value);
                plateResults.appendChild(item);
            } else {
                uniquePlates.forEach(p => {
                    const item = document.createElement('div');
                    item.className = 'autocomplete-item';
                    item.innerHTML = `<div class="font-medium">${p}</div>`;
                    item.onclick = () => selectPlate(p);
                    plateResults.appendChild(item);
                });
                const newItem = document.createElement('div');
                newItem.className = 'autocomplete-item';
                newItem.innerHTML = `<div class="font-medium text-green-600">New plate: "${plateInput.value}"</div>`;
                newItem.onclick = () => selectPlate(plateInput.value);
                plateResults.appendChild(newItem);
            }
            plateResults.style.display = 'block';
        }

        function selectPlate(plate) {
            plateInput.value = plate;
            plateResults.style.display = 'none';
        }
    }

    // Photo preview with hover to view full
    const photoInput = document.getElementById('photo');
    const photoPreviewContainer = document.getElementById('photo-preview-container');
    const photoPreview = document.getElementById('photo-preview');
    const photoFullOverlay = document.getElementById('photo-full-overlay');
    const photoFull = document.getElementById('photo-full');
    const photoFullClose = document.getElementById('photo-full-close');

    if (photoInput) {
        photoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    photoPreview.src = e.target.result;
                    photoFull.src = e.target.result;
                    photoPreviewContainer.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                photoPreviewContainer.classList.add('hidden');
            }
        });

        photoPreview?.addEventListener('mouseenter', function() {
            if (photoFull.src) {
                photoFullOverlay.classList.remove('hidden');
            }
        });

        photoPreview?.addEventListener('mouseleave', function() {
            photoFullOverlay.classList.add('hidden');
        });

        photoFullClose?.addEventListener('click', function() {
            photoFullOverlay.classList.add('hidden');
        });

        photoFullOverlay?.addEventListener('click', function(e) {
            if (e.target === photoFullOverlay) {
                photoFullOverlay.classList.add('hidden');
            }
        });
    }

    // Click outside to close
    document.addEventListener('click', function(e) {
        if (!driverResults.contains(e.target) && !driverInput.contains(e.target)) driverResults.style.display = 'none';
        if (plateResults && !plateResults.contains(e.target) && !plateInput.contains(e.target)) plateResults.style.display = 'none';
        if (photoFullOverlay && !photoFullOverlay.contains(e.target) && !photoPreview?.contains(e.target)) {
            photoFullOverlay.classList.add('hidden');
        }
    });
});
</script>
@endsection