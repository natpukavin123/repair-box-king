<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup — RepairBox</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --bg: #0f172a;
            --card: #1e293b;
            --border: #334155;
            --text: #f1f5f9;
            --muted: #94a3b8;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 2rem 1rem;
        }

        .setup-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        .setup-header .logo {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
            letter-spacing: -0.5px;
        }
        .setup-header .logo span { color: var(--text); }
        .setup-header p {
            color: var(--muted);
            margin-top: 0.4rem;
            font-size: 0.9rem;
        }

        /* Step progress bar */
        .steps {
            display: flex;
            align-items: center;
            gap: 0;
            margin-bottom: 2rem;
            width: 100%;
            max-width: 680px;
        }
        .step-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            position: relative;
        }
        .step-item:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 18px;
            left: 50%;
            width: 100%;
            height: 2px;
            background: var(--border);
            z-index: 0;
        }
        .step-item.done:not(:last-child)::after { background: var(--primary); }
        .step-num {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 2px solid var(--border);
            background: var(--card);
            display: flex; align-items: center; justify-content: center;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--muted);
            position: relative;
            z-index: 1;
            transition: all 0.3s;
        }
        .step-item.active .step-num {
            border-color: var(--primary);
            background: var(--primary);
            color: #fff;
        }
        .step-item.done .step-num {
            border-color: var(--success);
            background: var(--success);
            color: #fff;
        }
        .step-label {
            font-size: 0.7rem;
            color: var(--muted);
            margin-top: 0.4rem;
            text-align: center;
        }
        .step-item.active .step-label { color: var(--text); font-weight: 500; }

        /* Card */
        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 2rem 2.5rem;
            width: 100%;
            max-width: 680px;
        }
        .card-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 0.3rem;
        }
        .card-sub {
            color: var(--muted);
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
        }

        /* Form */
        .form-group { margin-bottom: 1.25rem; }
        label { display: block; font-size: 0.85rem; font-weight: 500; margin-bottom: 0.4rem; color: var(--muted); }
        input[type="text"], input[type="email"], input[type="password"], input[type="number"] {
            width: 100%;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text);
            padding: 0.65rem 0.875rem;
            font-size: 0.925rem;
            font-family: inherit;
            transition: border-color 0.2s;
        }
        input:focus { outline: none; border-color: var(--primary); }
        .row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }

        /* Buttons */
        .btn {
            display: inline-flex; align-items: center; gap: 0.4rem;
            padding: 0.7rem 1.5rem;
            border-radius: 8px;
            border: none;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-primary:hover { background: var(--primary-dark); }
        .btn-outline { background: transparent; border: 1px solid var(--border); color: var(--text); }
        .btn-outline:hover { border-color: var(--primary); color: var(--primary); }
        .btn-success { background: var(--success); color: #fff; }

        .btn-row { display: flex; justify-content: space-between; margin-top: 1.75rem; }
        .btn-row.right { justify-content: flex-end; }

        /* Alerts */
        .alert { padding: 0.75rem 1rem; border-radius: 8px; font-size: 0.875rem; margin-bottom: 1rem; }
        .alert-danger { background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: #fca5a5; }

        /* Check/X icons */
        .chk { font-style: normal; }
        .chk.ok { color: var(--success); }
        .chk.fail { color: var(--danger); }
    </style>
</head>
<body>

<div class="setup-header">
    <div class="logo">🔧 Repair<span>Box</span></div>
    <p>Installation Wizard</p>
</div>

<!-- Step indicators -->
<div class="steps">
    <div class="step-item {{ $step >= 1 ? ($step > 1 ? 'done' : 'active') : '' }}">
        <div class="step-num">{{ $step > 1 ? '✓' : '1' }}</div>
        <div class="step-label">Requirements</div>
    </div>
    <div class="step-item {{ $step >= 2 ? ($step > 2 ? 'done' : 'active') : '' }}">
        <div class="step-num">{{ $step > 2 ? '✓' : '2' }}</div>
        <div class="step-label">Database</div>
    </div>
    <div class="step-item {{ $step >= 3 ? ($step > 3 ? 'done' : 'active') : '' }}">
        <div class="step-num">{{ $step > 3 ? '✓' : '3' }}</div>
        <div class="step-label">Owner Info</div>
    </div>
    <div class="step-item {{ $step >= 4 ? ($step > 4 ? 'done' : 'active') : '' }}">
        <div class="step-num">{{ $step > 4 ? '✓' : '4' }}</div>
        <div class="step-label">Install</div>
    </div>
    <div class="step-item {{ $step >= 5 ? 'active' : '' }}">
        <div class="step-num">5</div>
        <div class="step-label">Complete</div>
    </div>
</div>

@yield('content')

<script>
function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content || '';
}
</script>
@yield('scripts')
</body>
</html>
