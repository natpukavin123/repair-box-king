@extends('setup.layout')
@php $step = 3; @endphp

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="card">
    <div class="card-title">🏪 Owner & Shop Settings</div>
    <div class="card-sub">Configure your shop name and the admin account that will be created.</div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul style="margin:0; padding-left:1.2rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('setup.owner.save') }}">
        @csrf

        <div class="form-group">
            <label>Shop / Business Name</label>
            <input type="text" name="shop_name" value="{{ old('shop_name', 'My Repair Shop') }}" placeholder="e.g. TechFix Mobile Repair" required>
        </div>

        <hr style="border:none; border-top:1px solid var(--border); margin:1.5rem 0;">
        <p style="font-size:0.8rem; color:var(--muted); margin-bottom:1rem; font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Admin Account</p>

        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="admin_name" value="{{ old('admin_name', 'Administrator') }}" required>
        </div>

        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="admin_email" value="{{ old('admin_email') }}" placeholder="admin@yourdomain.com" required>
        </div>

        <div class="row-2">
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="admin_password" placeholder="Min. 6 characters" required>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="admin_password_confirmation" placeholder="Confirm password" required>
            </div>
        </div>

        <div class="btn-row">
            <a href="{{ route('setup.database') }}" class="btn btn-outline">← Back</a>
            <button type="submit" class="btn btn-primary">Save & Continue →</button>
        </div>
    </form>
</div>
@endsection
