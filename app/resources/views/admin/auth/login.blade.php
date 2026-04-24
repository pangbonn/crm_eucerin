<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — {{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        @php $logoPath = isset($appSettings) ? ($appSettings->get('site_logo') ?? null) : null; @endphp
        @if($logoPath)
            <img src="{{ Storage::url($logoPath) }}" alt="Logo" style="max-height:60px;object-fit:contain;">
        @else
            <b>{{ isset($appSettings) ? ($appSettings->get('site_name') ?? 'CRM') : 'CRM' }}</b> Eucerin
        @endif
    </div>
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">เข้าสู่ระบบ Admin</p>

            @if($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <form action="{{ route('admin.login.post') }}" method="POST">
                @csrf
                <div class="input-group mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email"
                           value="{{ old('email') }}" required autofocus>
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-envelope"></span></div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
                    <div class="input-group-append">
                        <button type="button" class="btn btn-outline-secondary" id="toggle-password" tabindex="-1"
                                style="border-color:#ced4da;background:#fff;border-left:none;">
                            <span class="fas fa-eye" id="eye-icon"></span>
                        </button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        @php $btnClass = isset($appSettings) ? ($appSettings->get('auth_btn_class') ?? 'btn-flat btn-danger') : 'btn-flat btn-danger'; @endphp
                        <button type="submit" class="btn {{ $btnClass }} btn-block">เข้าสู่ระบบ</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
<script>
document.getElementById('toggle-password').addEventListener('click', function () {
    var input = document.getElementById('password');
    var icon = document.getElementById('eye-icon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
});
</script>
</body>
</html>
