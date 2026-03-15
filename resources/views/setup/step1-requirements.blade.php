@extends('setup.layout')
@php $step = 1; @endphp

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="card">
    <div class="card-title">⚙️ System Requirements</div>
    <div class="card-sub">Checking your server meets all requirements before we proceed.</div>

    @if(!$requirements['allPassed'])
        <div class="alert alert-danger">
            ⚠️ Some requirements are not met. Please fix the issues below before continuing.
        </div>
    @endif

    <!-- PHP Version -->
    <table style="width:100%; border-collapse:collapse; margin-bottom:1.5rem;">
        <thead>
            <tr style="border-bottom:1px solid var(--border);">
                <th style="text-align:left; padding:0.6rem 0; color:var(--muted); font-size:0.8rem; font-weight:500;">REQUIREMENT</th>
                <th style="text-align:left; padding:0.6rem 0; color:var(--muted); font-size:0.8rem; font-weight:500;">CURRENT</th>
                <th style="text-align:center; padding:0.6rem 0; color:var(--muted); font-size:0.8rem; font-weight:500;">STATUS</th>
            </tr>
        </thead>
        <tbody>
            <tr style="border-bottom:1px solid rgba(255,255,255,0.05);">
                <td style="padding:0.7rem 0; font-size:0.875rem;">PHP Version ≥ 8.1</td>
                <td style="padding:0.7rem 0; font-size:0.875rem; color:var(--muted);">{{ $requirements['phpVersion'] }}</td>
                <td style="text-align:center; padding:0.7rem 0;">
                    <em class="chk {{ $requirements['phpVersionOk'] ? 'ok' : 'fail' }}">{{ $requirements['phpVersionOk'] ? '✅' : '❌' }}</em>
                </td>
            </tr>
            <tr style="border-bottom:1px solid rgba(255,255,255,0.05);">
                <td style="padding:0.7rem 0; font-size:0.875rem;">storage/ writable</td>
                <td style="padding:0.7rem 0; font-size:0.875rem; color:var(--muted);">{{ $requirements['storageWritable'] ? 'Writable' : 'Not writable' }}</td>
                <td style="text-align:center; padding:0.7rem 0;">
                    <em class="chk {{ $requirements['storageWritable'] ? 'ok' : 'fail' }}">{{ $requirements['storageWritable'] ? '✅' : '❌' }}</em>
                </td>
            </tr>
            <tr style="border-bottom:1px solid rgba(255,255,255,0.05);">
                <td style="padding:0.7rem 0; font-size:0.875rem;">.env writable</td>
                <td style="padding:0.7rem 0; font-size:0.875rem; color:var(--muted);">{{ $requirements['envWritable'] ? 'Writable' : 'Not writable' }}</td>
                <td style="text-align:center; padding:0.7rem 0;">
                    <em class="chk {{ $requirements['envWritable'] ? 'ok' : 'fail' }}">{{ $requirements['envWritable'] ? '✅' : '❌' }}</em>
                </td>
            </tr>
            @foreach ($requirements['phpExtensions'] as $ext => $loaded)
            <tr style="border-bottom:1px solid rgba(255,255,255,0.05);">
                <td style="padding:0.7rem 0; font-size:0.875rem;">PHP Extension: <code style="background:rgba(255,255,255,0.07);padding:1px 6px;border-radius:4px;font-size:0.8rem;">{{ $ext }}</code></td>
                <td style="padding:0.7rem 0; font-size:0.875rem; color:var(--muted);">{{ $loaded ? 'Loaded' : 'Missing' }}</td>
                <td style="text-align:center; padding:0.7rem 0;">
                    <em class="chk {{ $loaded ? 'ok' : 'fail' }}">{{ $loaded ? '✅' : '❌' }}</em>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="btn-row right">
        @if($requirements['allPassed'])
            <a href="{{ route('setup.database') }}" class="btn btn-primary">Continue →</a>
        @else
            <button class="btn btn-outline" onclick="location.reload()">🔄 Re-check</button>
        @endif
    </div>
</div>
@endsection
