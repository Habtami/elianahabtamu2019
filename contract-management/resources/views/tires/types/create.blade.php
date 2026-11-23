@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Add Tire Type</h1>
        <a href="{{ route('tires.types.index') }}" class="btn btn-secondary btn-sm">Cancel</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('tires.types.store') }}">
                @csrf
                @include('tires.types._form', ['tireType' => null])
            </form>
        </div>
    </div>
</div>
@endsection
