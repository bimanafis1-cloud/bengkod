<x-layouts.guest title="Login">
    <div class="card w-full max-w-[420px] rounded-2xl bg-base-100 shadow-2xl">
        <div class="card-body p-10">
            <div class="mb-8 text-center">
                <img src="{{ asset('images/logo-bengkot.png') }}" alt="Logo Poliklinik"
                    class="mx-auto mb-4 h-[60px] w-[60px] rounded-2xl object-cover">
                <h1 class="mb-1 text-2xl font-extrabold text-[#1e2d6b]">Poliklinik</h1>
                <p class="text-sm text-slate-400">Masuk ke akun Anda</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-error mb-5 rounded-xl text-sm" role="alert">
                    <i class="fas fa-circle-xmark" aria-hidden="true"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success mb-5 rounded-xl text-sm" role="status">
                    <i class="fas fa-check-circle" aria-hidden="true"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="form-control mb-4">
                    <label class="label pb-1" for="email">
                        <span class="text-sm font-semibold text-gray-700">Email</span>
                    </label>
                    <label class="input input-bordered flex items-center gap-3 rounded-[10px] border-slate-200 bg-slate-50">
                        <i class="fas fa-envelope text-sm text-slate-400" aria-hidden="true"></i>
                        <input id="email" type="email" name="email" value="{{ old('email') }}"
                            placeholder="Masukkan email..." class="grow bg-transparent text-sm text-slate-800" required
                            autocomplete="email" autofocus>
                    </label>
                </div>

                <div class="form-control mb-6">
                    <label class="label pb-1" for="password_login">
                        <span class="text-sm font-semibold text-gray-700">Password</span>
                    </label>
                    <label class="input input-bordered flex items-center gap-3 rounded-[10px] border-slate-200 bg-slate-50">
                        <i class="fas fa-lock text-sm text-slate-400" aria-hidden="true"></i>
                        <input type="password" name="password" id="password_login" placeholder="Masukkan password..."
                            class="grow bg-transparent text-sm text-slate-800" required autocomplete="current-password">
                        <button type="button" class="text-slate-400" aria-label="Tampilkan password"
                            onclick="togglePassword('password_login', this)">
                            <i class="fas fa-eye text-sm" aria-hidden="true"></i>
                        </button>
                    </label>
                </div>

                <button type="submit" class="btn-primary-gradient mt-6">
                    <i class="fas fa-right-to-bracket" aria-hidden="true"></i>
                    Login
                </button>
            </form>

            <p class="mt-5 text-center text-sm text-slate-400">
                Belum punya akun?
                <a href="{{ route('register') }}" class="font-bold text-[#2d4499] no-underline">Register</a>
            </p>
        </div>
    </div>

    @push('scripts')
        <script>
            function togglePassword(inputId, button) {
                const input = document.getElementById(inputId);
                const icon = button.querySelector('i');
                const isPassword = input.type === 'password';

                input.type = isPassword ? 'text' : 'password';
                icon.classList.toggle('fa-eye', !isPassword);
                icon.classList.toggle('fa-eye-slash', isPassword);
                button.setAttribute('aria-label', isPassword ? 'Sembunyikan password' : 'Tampilkan password');
            }
        </script>
    @endpush
</x-layouts.guest>
