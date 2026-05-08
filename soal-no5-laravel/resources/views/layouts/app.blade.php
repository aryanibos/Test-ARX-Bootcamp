<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'CMS User' }} - {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdn.datatables.net">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @stack('vendor_styles')
    <style>
        :root {
            --page-bg: #f6f7f4;
            --panel-bg: #ffffff;
            --ink: #20231f;
            --muted: #6f746a;
            --line: #d9ded2;
            --accent: #177d73;
            --accent-dark: #106158;
            --danger: #bf3d32;
            --warning: #c78d21;
        }

        body {
            min-height: 100vh;
            background: var(--page-bg);
            color: var(--ink);
            font-family: Arial, Helvetica, sans-serif;
            letter-spacing: 0;
        }

        .auth-page {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 32px 16px;
        }

        .auth-panel,
        .tool-panel {
            background: var(--panel-bg);
            border: 1px solid var(--line);
            border-radius: 8px;
            box-shadow: 0 16px 40px rgba(32, 35, 31, 0.08);
        }

        .auth-panel {
            width: min(100%, 420px);
            padding: 28px;
        }

        .app-header {
            background: #ffffff;
            border-bottom: 1px solid var(--line);
        }

        .cms-main {
            padding-top: 24px;
            padding-bottom: 40px;
        }

        .tool-panel {
            padding: 16px;
        }

        .btn-primary {
            --bs-btn-bg: var(--accent);
            --bs-btn-border-color: var(--accent);
            --bs-btn-hover-bg: var(--accent-dark);
            --bs-btn-hover-border-color: var(--accent-dark);
            --bs-btn-active-bg: var(--accent-dark);
            --bs-btn-active-border-color: var(--accent-dark);
        }

        .icon-button,
        .action-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .action-button {
            width: 38px;
            height: 38px;
            padding: 0;
        }

        .lucide {
            width: 18px;
            height: 18px;
            stroke-width: 2;
        }

        .profile-thumb,
        .profile-placeholder,
        .image-preview {
            width: 200px;
            height: 200px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid var(--line);
            background: #eef1ea;
        }

        .profile-placeholder,
        .image-preview-empty {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--muted);
        }

        .profile-placeholder .lucide {
            width: 46px;
            height: 46px;
        }

        .image-preview-wrap {
            min-height: 200px;
        }

        .image-preview-empty {
            width: 200px;
            height: 200px;
            border: 1px dashed var(--line);
            border-radius: 8px;
            background: #fafbf8;
        }

        table.dataTable > tbody > tr > td {
            vertical-align: middle;
        }

        .table-responsive {
            min-height: 320px;
        }

        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 0.2rem rgba(23, 125, 115, 0.18);
        }

        @media (max-width: 576px) {
            .auth-panel {
                padding: 22px;
            }

            .cms-main {
                padding-inline: 12px;
            }

            .profile-thumb,
            .profile-placeholder {
                width: 160px;
                height: 160px;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    @yield('content')

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    @stack('vendor_scripts')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            }
        });

        function refreshIcons() {
            if (window.lucide) {
                window.lucide.createIcons();
            }
        }

        $(refreshIcons);
    </script>
    @stack('scripts')
</body>
</html>
