<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Cementerio General de Sacaba') }}</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #0a240d 0%, #113615 50%, #1e5924 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            overflow: hidden;
            width: 100%;
            max-width: 440px;
        }

        .login-header {
            background-color: #113615;
            color: #ffffff;
            padding: 2rem 1.5rem 1.5rem 1.5rem;
            text-align: center;
        }

        .btn-sacaba {
            background-color: #113615;
            color: #ffffff;
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
        }

        .btn-sacaba:hover {
            background-color: #18481d;
            color: #ffffff;
        }
    </style>
</head>
<body>
    <div class="container p-3">
        <div class="row justify-content-center">
            <div class="col-12 d-flex justify-content-center">
                <div class="card login-card">
                    <div class="login-header">
                        <div class="brand-icon bg-white text-emerald rounded-circle d-inline-flex align-items-center justify-content-center fw-bold mb-3 shadow" style="width: 56px; height: 56px; color: #113615;">
                            <i class="bi bi-building-fill fs-3"></i>
                        </div>
                        <h4 class="fw-bold mb-1">Cementerio General</h4>
                        <p class="small text-success-light mb-0 text-uppercase tracking-wider opacity-75" style="letter-spacing: 1px;">Sacaba - Cochabamba</p>
                    </div>
                    <div class="card-body p-4 bg-white">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5.3.3 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
