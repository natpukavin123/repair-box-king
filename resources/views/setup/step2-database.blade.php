@extends('setup.layout')
@php $step = 2; @endphp

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="card">
    <div class="card-title">🗄️ Database Configuration</div>
    <div class="card-sub">Enter your MySQL database credentials. We'll test the connection before saving.</div>

    @if($errors->has('db_connection'))
        <div class="alert alert-danger">{{ $errors->first('db_connection') }}</div>
    @endif

    <div id="test-result" style="display:none;" class="alert"></div>

    <form method="POST" action="{{ route('setup.database.save') }}">
        @csrf

        <div class="row-2">
            <div class="form-group">
                <label>Database Host</label>
                <input type="text" name="db_host" id="db_host" value="{{ old('db_host', $envData['db_host'] ?? '127.0.0.1') }}" required>
            </div>
            <div class="form-group">
                <label>Port</label>
                <input type="number" name="db_port" id="db_port" value="{{ old('db_port', $envData['db_port'] ?? '3306') }}" required>
            </div>
        </div>

        <div class="form-group">
            <label>Database Name</label>
            <input type="text" name="db_database" id="db_database" value="{{ old('db_database', $envData['db_database'] ?? '') }}" placeholder="e.g. repair_box" required>
        </div>

        <div class="row-2">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="db_username" id="db_username" value="{{ old('db_username', $envData['db_username'] ?? 'root') }}" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="db_password" id="db_password" value="{{ old('db_password', $envData['db_password'] ?? '') }}" placeholder="Leave blank if none">
            </div>
        </div>

        <div class="btn-row">
            <a href="{{ route('setup.index') }}" class="btn btn-outline">← Back</a>
            <div style="display:flex;gap:0.75rem;">
                <button type="button" class="btn btn-outline" id="testBtn" onclick="testConnection()">🔌 Test Connection</button>
                <button type="submit" class="btn btn-primary" id="saveBtn">Save & Continue →</button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
async function testConnection() {
    const btn = document.getElementById('testBtn');
    const result = document.getElementById('test-result');
    btn.disabled = true;
    btn.textContent = '⏳ Testing...';
    result.style.display = 'none';

    const payload = {
        db_host:     document.getElementById('db_host').value,
        db_port:     document.getElementById('db_port').value,
        db_database: document.getElementById('db_database').value,
        db_username: document.getElementById('db_username').value,
        db_password: document.getElementById('db_password').value,
        _token:      document.querySelector('meta[name="csrf-token"]').content,
    };

    try {
        const res  = await fetch('/setup/test-connection', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': payload._token },
            body: JSON.stringify(payload),
        });
        const data = await res.json();
        result.style.display = 'block';
        if (data.success) {
            result.className = 'alert';
            result.style.background = 'rgba(16,185,129,0.15)';
            result.style.border = '1px solid rgba(16,185,129,0.3)';
            result.style.color = '#6ee7b7';
            result.textContent = '✅ ' + data.message;
        } else {
            result.className = 'alert alert-danger';
            result.textContent = '❌ ' + data.message;
        }
    } catch(e) {
        result.style.display = 'block';
        result.className = 'alert alert-danger';
        result.textContent = '❌ Network error. Please try again.';
    }
    btn.disabled = false;
    btn.textContent = '🔌 Test Connection';
}
</script>
@endsection
