<x-guest-layout>
    <div class="text-center mb-6">
        <span class="text-3xl">🫧</span>
        <h1 class="text-xl font-bold text-[#7E22CE] mt-2">Hexa Space</h1>
        <p class="text-sm text-[#6B7280] mt-1">Buat akun baru</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Nama Lengkap')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Kata Sandi')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Kata Sandi')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-input-label :value="__('Daftar Sebagai')" />
            <div class="grid grid-cols-2 gap-3 mt-2">
                <label class="flex items-center gap-3 p-4 border border-[#E5E7EB] rounded-2xl cursor-pointer transition has-[:checked]:border-[#C084FC] has-[:checked]:bg-[#F8F3FF]">
                    <input type="radio" name="role" value="user" class="text-[#C084FC] focus:ring-[#C084FC]" {{ old('role', 'user') === 'user' ? 'checked' : '' }}>
                    <div>
                        <span class="block text-sm font-medium text-[#374151]">Pasien</span>
                        <span class="block text-xs text-[#6B7280]">Mendapat layanan konseling</span>
                    </div>
                </label>
                <label class="flex items-center gap-3 p-4 border border-[#E5E7EB] rounded-2xl cursor-pointer transition has-[:checked]:border-[#C084FC] has-[:checked]:bg-[#F8F3FF]">
                    <input type="radio" name="role" value="doctor" class="text-[#C084FC] focus:ring-[#C084FC]" {{ old('role') === 'doctor' ? 'checked' : '' }}>
                    <div>
                        <span class="block text-sm font-medium text-[#374151]">Doktor</span>
                        <span class="block text-xs text-[#6B7280]">Memantau sesi pasien</span>
                    </div>
                </label>
            </div>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-6">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#C084FC]" href="{{ route('login') }}">
                Sudah punya akun?
            </a>

            <x-primary-button class="ms-4">
                Daftar
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
