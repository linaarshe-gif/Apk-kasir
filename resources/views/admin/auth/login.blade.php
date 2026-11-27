<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#02090b">
    <title>TIGA | Secure Login</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800&family=Rajdhani:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/vendor/admin/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="/vendor/admin/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="/css/hologram.css">
</head>
<body class="hold-transition login-page hologram-login">
    <div class="login-hud" aria-hidden="true">
        <span class="hud-status"><i class="hud-status-dot"></i> SYSTEM ONLINE</span>
        <span class="hud-code">TIGA // SECURE ACCESS PROTOCOL</span>
        <span class="hud-scan"></span>
    </div>

    <div class="login-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="/" class="hologram-brand">
                    <span class="brand-mark"><i class="fas fa-gem"></i></span>
                    <span class="brand-text">
                        <b>TIGA</b>
                        <small>RETAIL OS</small>
                    </span>
                </a>
            </div>
            <div class="card-body">
                <p class="login-box-msg">Masuk ke terminal pengguna</p>

                @if (session()->has('loginError'))
                    <div class="alert alert-danger">{{ session('loginError') }}</div>
                @endif

                <form action="/login/do" method="post">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" placeholder="Email" autocomplete="username" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="input-group mb-3">
                        <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Password" autocomplete="current-password" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Masuk ke Sistem</button>
                </form>

                <div class="login-foot">Authorized personnel only // terminal v2.6</div>
            </div>
        </div>
    </div>

    <script src="/vendor/admin/plugins/jquery/jquery.min.js"></script>
    <script src="/vendor/admin/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/vendor/admin/dist/js/adminlte.min.js"></script>
</body>
</html>
