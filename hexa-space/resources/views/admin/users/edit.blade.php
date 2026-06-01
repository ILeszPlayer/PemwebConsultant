<x-app-layout>
    <div class="py-12 bg-[#F8F3FF] min-h-screen">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <a href="{{ route('admin.users.index') }}" class="text-[#C084FC] hover:text-[#7E22CE] text-sm font-medium transition">&larr; Kembali</a>
                <h1 class="text-3xl font-bold text-[#374151] mt-2">Edit User: {{ $user->name }}</h1>
            </div>

            <div class="bg-white rounded-2xl p-8 shadow-sm border border-[#E5E7EB]">
                <form action="{{ route('admin.users.update', $user) }}" method="POST">
                    @csrf @method('PUT')

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-[#6B7280] mb-1">Nama</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full border border-[#E5E7EB] rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#C084FC]" required>
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-[#6B7280] mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full border border-[#E5E7EB] rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#C084FC]" required>
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-[#6B7280] mb-1">Role</label>
                        <select name="role" class="w-full border border-[#E5E7EB] rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#C084FC]" required>
                            <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>Pasien</option>
                            <option value="doctor" {{ old('role', $user->role) === 'doctor' ? 'selected' : '' }}>Dokter</option>
                            <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                        @error('role') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="w-full bg-[#C084FC] hover:bg-[#7E22CE] text-white px-6 py-3 rounded-xl font-medium transition">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
