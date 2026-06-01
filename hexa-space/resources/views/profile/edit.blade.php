<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Avatar --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">Foto Profil</h2>
                            <p class="mt-1 text-sm text-gray-600">Upload foto profil untuk personalisasi akunmu.</p>
                        </header>

                        <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-4">
                            @csrf @method('patch')

                            <div class="flex items-center gap-6">
                                <div class="w-20 h-20 rounded-full overflow-hidden bg-[#F8F3FF] border-2 border-[#C084FC]">
                                    @if(Auth::user()->avatar)
                                        <img src="{{ Storage::url(Auth::user()->avatar) }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-3xl">👤</div>
                                    @endif
                                </div>
                                <div>
                                    <input type="file" name="avatar" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#F8F3FF] file:text-[#7E22CE] hover:file:bg-[#E9D5FF]">
                                    @error('avatar') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <button type="submit" class="bg-[#C084FC] hover:bg-[#7E22CE] text-white px-6 py-2 rounded-full text-sm font-medium transition">Simpan Foto</button>
                                @if(Auth::user()->avatar)
                                    <a href="{{ route('profile.avatar.remove') }}" class="text-red-500 hover:text-red-700 text-sm font-medium transition" onclick="return confirm('Hapus foto profil?')">Hapus Foto</a>
                                @endif
                            </div>
                        </form>
                    </section>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
