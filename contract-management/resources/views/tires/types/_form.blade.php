<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Name *</label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $tireType->name ?? '') }}" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Code</label>
        <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
               value="{{ old('code', $tireType->code ?? '') }}" placeholder="e.g., MIC">
        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Category</label>
        <input type="text" name="category" class="form-control"
               value="{{ old('category', $tireType->category ?? '') }}" placeholder="e.g., Truck, Bus">
    </div>

    <div class="col-md-3">
        <label class="form-label">Width (mm)</label>
        <input type="number" name="width" class="form-control" step="0.1"
               value="{{ old('width', $tireType->width ?? '') }}">
    </div>

    <div class="col-md-3">
        <label class="form-label">Aspect Ratio</label>
        <input type="number" name="aspect_ratio" class="form-control" step="0.1"
               value="{{ old('aspect_ratio', $tireType->aspect_ratio ?? '') }}">
    </div>

    <div class="col-md-3">
        <label class="form-label">Rim Diameter</label>
        <input type="number" name="rim_diameter" class="form-control" step="0.1"
               value="{{ old('rim_diameter', $tireType->rim_diameter ?? '') }}">
    </div>

    <div class="col-md-3">
        <label class="form-label">Load Index</label>
        <input type="number" name="load_index" class="form-control"
               value="{{ old('load_index', $tireType->load_index ?? '') }}">
    </div>

    <div class="col-md-3">
        <label class="form-label">Season</label>
        <select name="season" class="form-select">
            <option value="">Select...</option>
            <option value="All-Season" {{ old('season', $tireType->season ?? '') == 'All-Season' ? 'selected' : '' }}>All-Season</option>
            <option value="Summer" {{ old('season', $tireType->season ?? '') == 'Summer' ? 'selected' : '' }}>Summer</option>
            <option value="Winter" {{ old('season', $tireType->season ?? '') == 'Winter' ? 'selected' : '' }}>Winter</option>
            <option value="All-Weather" {{ old('season', $tireType->season ?? '') == 'All-Weather' ? 'selected' : '' }}>All-Weather</option>
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label">Sort Order</label>
        <input type="number" name="sort_order" class="form-control"
               value="{{ old('sort_order', $tireType->sort_order ?? 0) }}">
    </div>

    <div class="col-md-3 d-flex align-items-end">
        <div class="form-check">
            <input type="checkbox" name="is_active" value="1" id="is_active"
                   {{ old('is_active', $tireType->is_active ?? true) ? 'checked' : '' }} class="form-check-input">
            <label class="form-check-label" for="is_active">Active</label>
        </div>
    </div>
</div>

<div class="mt-4">
    <button type="submit" class="btn btn-primary fw-bold px-4">Save</button>
</div>
