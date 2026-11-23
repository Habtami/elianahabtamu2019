@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Tire Label / Barcode</h4>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        <img src="https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl={{ urlencode($tire->serial_number) }}&choe=UTF-8" alt="QR Code" class="img-fluid border p-2">
                    </div>
                    <h5>{{ $tire->tire_type }}</h5>
                    <p class="lead text-muted">Serial: {{ $tire->serial_number }}</p>
                    <table class="table table-borderless text-start">
                        <tr>
                            <th width="40%">Status:</th>
                            <td>{{ ucfirst($tire->status) }}</td>
                        </tr>
                        <tr>
                            <th>Date Added:</th>
                            <td>{{ $tire->created_at->format('Y-m-d') }}</td>
                        </tr>
                        @if($currentGive)
                        <tr>
                            <th>Current Driver:</th>
                            <td>{{ $currentGive->driver->name }}</td>
                        </tr>
                        <tr>
                            <th>KM When Given:</th>
                            <td>{{ $currentGive->given_kilometer }}</td>
                        </tr>
                        @endif
                    </table>
                    <div class="mt-4">
                        <button class="btn btn-success" onclick="printLabel()">
                            <i class="fas fa-print me-2"></i>Print Label
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function printLabel() {
    window.print();
}
</script>
@endsection
