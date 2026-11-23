@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fab fa-telegram fa-lg me-2"></i>Telegram Settings</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <form method="POST" action="{{ route('profile.telegram.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Telegram Chat ID</label>
                            <input type="text" name="telegram_chat_id" class="form-control" placeholder="Enter your Telegram Chat ID" value="{{ old('telegram_chat_id', auth()->user()->telegram_chat_id) }}">
                            <small class="text-muted">To get your Chat ID, send a message to @userinfobot on Telegram.</small>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="telegram_notifications" value="1" id="notifications" {{ (auth()->user()->telegram_notifications ?? false) ? 'checked' : '' }} class="form-check-input">
                                <label class="form-check-label" for="notifications">
                                    Enable Telegram notifications
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">Save Settings</button>
                            <a href="{{ route('profile.show') }}" class="btn btn-secondary">Back to Profile</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
