@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Tire Types</h1>
        <a href="{{ route('tires.types.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i>Add Tire Type
        </a>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Category</th>
                            <th>Spec</th>
                            <th>Sort Order</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tireTypes as $type)
                        <tr>
                            <td>{{ $type->name }}</td>
                            <td>{{ $type->code ?? '-' }}</td>
                            <td>{{ $type->category ?? '-' }}</td>
                            <td>{{ $type->formatted_spec ?? ($type->width ? $type->width . '/' . $type->aspect_ratio . 'R' . $type->rim_diameter : '-') }}</td>
                            <td>{{ $type->sort_order }}</td>
                            <td>
                                @if($type->is_active)
                                <span class="badge bg-success">Active</span>
                                @else
                                <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('tires.types.edit', $type) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="{{ route('tires.types.destroy', $type) }}" class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to delete this tire type?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No tire types found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($tireTypes->hasPages())
        <div class="card-footer">
            {{ $tireTypes->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
