<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Audit Logging Demo')</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }

        nav {
            background: #1a1a2e;
            padding: 0 2rem;
            display: flex;
            align-items: center;
            gap: 2rem;
            height: 56px;
        }

        nav a {
            color: #e0e0e0;
            text-decoration: none;
            font-size: 0.9rem;
        }

        nav a:hover { color: #fff; }

        nav .brand {
            font-weight: 600;
            font-size: 1rem;
            color: #fff;
            margin-right: auto;
        }

        .container {
            max-width: 860px;
            margin: 2rem auto;
            padding: 0 1.5rem;
        }

        h1 { font-size: 1.6rem; margin-bottom: 1.5rem; }
        h2 { font-size: 1.2rem; margin-bottom: 1rem; }

        .alert {
            padding: 0.75rem 1rem;
            border-radius: 6px;
            margin-bottom: 1.25rem;
            font-size: 0.9rem;
        }

        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-error   { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }

        .card {
            background: #fff;
            border-radius: 8px;
            padding: 1.75rem;
            box-shadow: 0 1px 4px rgba(0,0,0,.08);
        }

        table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
        th { text-align: left; padding: 0.6rem 0.75rem; border-bottom: 2px solid #e5e7eb; color: #555; font-weight: 600; }
        td { padding: 0.6rem 0.75rem; border-bottom: 1px solid #f0f0f0; vertical-align: top; }
        tr:last-child td { border-bottom: none; }

        .btn {
            display: inline-block;
            padding: 0.45rem 1rem;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: opacity .15s;
        }
        .btn:hover { opacity: .85; }
        .btn-primary  { background: #4f46e5; color: #fff; }
        .btn-secondary{ background: #e5e7eb; color: #374151; }
        .btn-danger   { background: #ef4444; color: #fff; }
        .btn-sm       { padding: 0.3rem 0.7rem; font-size: 0.8rem; }

        .form-group { margin-bottom: 1.25rem; }
        label { display: block; font-size: 0.85rem; font-weight: 500; margin-bottom: 0.35rem; color: #555; }

        input[type="text"],
        input[type="datetime-local"],
        textarea {
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 0.9rem;
            font-family: inherit;
        }

        input[type="text"]:focus,
        input[type="datetime-local"]:focus,
        textarea:focus {
            outline: none;
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79,70,229,.15);
        }

        textarea { min-height: 120px; resize: vertical; }

        .checkbox-row {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .error-msg { color: #ef4444; font-size: 0.8rem; margin-top: 0.25rem; }

        .badge {
            display: inline-block;
            padding: 0.15rem 0.5rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-yes { background: #dcfce7; color: #166534; }
        .badge-no  { background: #f3f4f6; color: #6b7280; }

        .actions { display: flex; gap: 0.4rem; }

        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }

        /* Timeline */
        .timeline {
            position: relative;
            padding-left: 2rem;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 0.55rem;
            top: 0.5rem;
            bottom: 0.5rem;
            width: 2px;
            background: #e5e7eb;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .timeline-item:last-child { margin-bottom: 0; }

        .timeline-dot {
            position: absolute;
            left: -1.7rem;
            top: 0.25rem;
            width: 1rem;
            height: 1rem;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 0 0 2px #d1d5db;
        }

        .dot-create { background: #22c55e; box-shadow: 0 0 0 2px #86efac; }
        .dot-update { background: #3b82f6; box-shadow: 0 0 0 2px #93c5fd; }
        .dot-delete { background: #ef4444; box-shadow: 0 0 0 2px #fca5a5; }

        .timeline-meta {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            margin-bottom: 0.4rem;
        }

        .timeline-action {
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            padding: 0.15rem 0.55rem;
            border-radius: 999px;
        }

        .action-create { background: #dcfce7; color: #166534; }
        .action-update { background: #dbeafe; color: #1e40af; }
        .action-delete { background: #fee2e2; color: #991b1b; }

        .timeline-time {
            font-size: 0.78rem;
            color: #9ca3af;
        }

        .timeline-body {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 0.75rem 1rem;
            font-size: 0.85rem;
        }

        .timeline-body .meta-row {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            color: #6b7280;
            font-size: 0.8rem;
            margin-top: 0.5rem;
            padding-top: 0.5rem;
            border-top: 1px solid #e5e7eb;
        }

        .meta-row span { display: flex; align-items: center; gap: 0.25rem; }

        .changes-table { width: 100%; border-collapse: collapse; margin-top: 0.4rem; }
        .changes-table th { font-size: 0.75rem; color: #9ca3af; text-transform: uppercase; letter-spacing: .04em; padding: 0.2rem 0.4rem; border-bottom: 1px solid #e5e7eb; }
        .changes-table td { padding: 0.3rem 0.4rem; vertical-align: top; border-bottom: 1px solid #f3f4f6; font-size: 0.82rem; }
        .changes-table tr:last-child td { border-bottom: none; }
        .val-from { color: #dc2626; text-decoration: line-through; }
        .val-to   { color: #16a34a; }
        .field-name { font-weight: 600; color: #374151; }
    </style>
</head>
<body>
    <nav>
        <span class="brand">Audit Logging Demo</span>
        <a href="{{ route('posts.index') }}">Posts</a>
        <a href="{{ route('posts.create') }}">New Post</a>
    </nav>

    <div class="container">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @yield('content')
    </div>
</body>
</html>
