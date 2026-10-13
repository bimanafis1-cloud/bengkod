<x-layouts.guest title="Register">
    <div class="card w-full max-w-[480px] rounded-2xl bg-base-100 shadow-2xl">
        <div class="card-body p-10">
            <div class="mb-7 text-center">
                <img src="{{ asset('images/logo-bengkot.png') }}" alt="Logo Poliklinik"
                    class="mx-auto mb-4 h-[60px] w-[60px] rounded-2xl object-cover">
                <h1 class="mb-1 text-2xl font-extrabold text-[#1e2d6b]">Poliklinik</h1>
                <p class="text-sm text-slate-400">Buat akun baru</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-error mb-4 rounded-xl text-sm" role="alert">
                    <i class="fas fa-circle-xmark" aria-hidden="true"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST">
                @csrf
                <div class="form-control mb-4">
                    <label class="label pb-1" for="nama">
                        <span class="text-sm font-semibold text-gray-700">Nama Lengkap</span>
                    </label>
                    <input id="nama" type="text" name="nama" value="{{ old('nama') }}"
                        placeholder="Masukkan nama lengkap..." class="input input-bordered w-full rounded-[10px] bg-slate-50"
                        required autocomplete="name">
                </div>

                <div class="form-control mb-4">
                    <label class="label pb-1" for="email">
                        <span class="text-sm font-semibold text-gray-700">Email</span>
                    </label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                        placeholder="Masukkan email..." class="input input-bordered w-full rounded-[10px] bg-slate-50"
                        required autocomplete="email">
                </div>

                <div class="form-control mb-4">
                    <label class="label pb-1" for="alamat">
                        <span class="text-sm font-semibold text-gray-700">Alamat</span>
                    </label>
                    <input id="alamat" type="text" name="alamat" value="{{ old('alamat') }}"
                        placeholder="Masukkan alamat..." class="input input-bordered w-full rounded-[10px] bg-slate-50"
                        required autocomplete="street-address">
                </div>

                <div class="mb-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="form-control">
                        <label class="label pb-1" for="no_hp">
                            <span class="text-sm font-semibold text-gray-700">No. HP</span>
                        </label>
                        <input id="no_hp" type="tel" name="no_hp" value="{{ old('no_hp') }}"
                            placeholder="No. HP..." class="input input-bordered w-full rounded-[10px] bg-slate-50"
                            required autocomplete="tel">
                    </div>
                    <div class="form-control">
                        <label class="label pb-1" for="no_ktp">
                            <span class="text-sm font-semibold text-gray-700">No. KTP</span>
                        </label>
                        <input id="no_ktp" type="text" name="no_ktp" value="{{ old('no_ktp') }}"
                            placeholder="No. KTP..." inputmode="numeric"
                            class="input input-bordered w-full rounded-[10px] bg-slate-50" required>
                    </div>
                </div>

                <div class="form-control mb-4">
                    <label class="label pb-1" for="password_reg">
                        <span class="text-sm font-semibold text-gray-700">Password</span>
                    </label>
                    <input id="password_reg" type="password" name="password" placeholder="Password..."
                        class="input input-bordered w-full rounded-[10px] bg-slate-50" required
                        autocomplete="new-password">
                </div>

                <div class="form-control mb-6">
                    <label class="label pb-1" for="password_confirm">
                        <span class="text-sm font-semibold text-gray-700">Konfirmasi Password</span>
                    </label>
                    <input id="password_confirm" type="password" name="password_confirmation"
                        placeholder="Ulangi password..." class="input input-bordered w-full rounded-[10px] bg-slate-50"
                        required autocomplete="new-password">
                </div>

                <button type="submit" class="btn-primary-gradient">
                    <i class="fas fa-user-plus" aria-hidden="true"></i>
                    Register
                </button>
            </form>

            <p class="mt-5 text-center text-sm text-slate-400">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="font-bold text-[#2d4499] no-underline">Login</a>
            </p>
        </div>
    </div>
</x-layouts.guest>
