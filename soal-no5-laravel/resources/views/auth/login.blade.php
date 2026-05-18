@extends('layouts.app', ['title' => 'Login'])

@push('styles')
    <style>
        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            padding: 0;
            text-decoration: none;
            border: none;
        }
    </style>
@endpush

@section('content')
<main class="auth-page">
    <section class="auth-panel">
        <div class="mb-4">
            <p class="text-uppercase text-muted fw-semibold small mb-2">CMS User</p>
            <h1 class="h3 mb-1">Login</h1>
            <p class="text-muted mb-0">Masuk untuk mengelola data user.</p>
        </div>

        <div id="loginAlert" class="alert alert-danger d-none" role="alert"></div>

        <div class="mb-3">
            <label for="loginEmail" class="form-label">Email</label>
            <input type="text" id="loginEmail" class="form-control" autocomplete="email" value="{{ old('email') }}">
           @error('email')
                 <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="loginPassword" class="form-label">Password</label>

            <div class="position-relative">
                <input
                    type="password"
                    name="password"
                    id="loginPassword"
                    class="form-control pe-5 @error('password') is-invalid @enderror"
                    autocomplete="current-password"
                >

                <button
                    type="button"
                    class="btn btn-link password-toggle"
                    data-target="#loginPassword"
                    aria-label="Tampilkan password"
                >
                    👁️
                </button>
            </div>

            @error('password')
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <button type="button" id="loginButton" class="btn btn-primary w-100 icon-button">
            <i data-lucide="log-in"></i>
            <span>Login</span>
        </button>
    </section>
</main>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.password-toggle').forEach(function(button) {
    button.addEventListener('click', function() {
        const input = document.querySelector(this.dataset.target);

        if (input.type === 'password') {
            input.type = 'text';
            this.textContent = '🙈';
            this.setAttribute('aria-label', 'Sembunyikan password');
        } else {
            input.type = 'password';
            this.textContent = '👁️';
            this.setAttribute('aria-label', 'Tampilkan password');
        }
    });
});
</script>
@endpush

@push('scripts')
<script>
    $(function () {
        const $loginButton = $('#loginButton');

        function setBusy(isBusy) {
            $loginButton.prop('disabled', isBusy);
            $loginButton.find('span').text(isBusy ? 'Memproses...' : 'Login');
        }

        function clearErrors() {
            $('#loginAlert').addClass('d-none').text('');
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');
        }

        function showErrors(errors, fallbackMessage) {
            const firstMessage = errors ? Object.values(errors)[0]?.[0] : fallbackMessage;
            $('#loginAlert').removeClass('d-none').text(firstMessage || 'Login gagal.');

            $.each(errors || {}, function (field, messages) {
                const inputId = field === 'email' ? '#loginEmail' : '#loginPassword';
                $(inputId).addClass('is-invalid');
                $(`[data-error-for="${field}"]`).text(messages[0]);
            });
        }

        function login() {
            clearErrors();
            setBusy(true);

            $.ajax({
                url: '{{ route('login.attempt') }}',
                method: 'POST',
                data: {
                    email: $('#loginEmail').val(),
                    password: $('#loginPassword').val()
                },
                success: function (response) {
                    window.location.href = response.redirect;
                },
                error: function (xhr) {
                    showErrors(xhr.responseJSON?.errors, xhr.responseJSON?.message);
                },
                complete: function () {
                    setBusy(false);
                    refreshIcons();
                }
            });
        }

        $loginButton.on('click', login);

        $('#loginEmail, #loginPassword').on('keydown', function (event) {
            if (event.key === 'Enter') {
                login();
            }
        });
    });
</script>
@endpush
