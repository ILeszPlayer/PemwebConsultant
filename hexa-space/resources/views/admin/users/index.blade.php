<x-app-layout>
    <div class="py-12 bg-[#F8F3FF] min-h-screen">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-[#374151]">Manajemen Pengguna</h1>
                    <p class="text-[#6B7280] mt-2">Kelola semua akun pasien dan dokter.</p>
                </div>
                <a href="{{ route('admin.users.create') }}" class="bg-[#C084FC] hover:bg-[#7E22CE] text-white px-6 py-3 rounded-full font-medium transition shadow-sm">+ Tambah User</a>
            </div>

            {{-- Search & Filter --}}
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-[#E5E7EB] mb-6">
                <form method="GET" class="flex flex-wrap gap-4 items-end">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-sm font-medium text-[#6B7280] mb-1">Cari</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama atau email..." class="w-full border border-[#E5E7EB] rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#C084FC]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#6B7280] mb-1">Role</label>
                        <select name="role" class="border border-[#E5E7EB] rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#C084FC]">
                            <option value="">Semua Role</option>
                            <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>Pasien</option>
                            <option value="doctor" {{ request('role') === 'doctor' ? 'selected' : '' }}>Dokter</option>
                            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>
                    <button type="submit" class="bg-[#C084FC] hover:bg-[#7E22CE] text-white px-5 py-2 rounded-xl text-sm font-medium transition">Filter</button>
                    @if(request('search') || request('role'))
                        <a href="{{ route('admin.users.index') }}" class="text-[#6B7280] hover:text-[#374151] text-sm font-medium px-4 py-2">Reset</a>
                    @endif
                </form>
            </div>

            {{-- Users Table --}}
            <div class="bg-white rounded-2xl shadow-sm border border-[#E5E7EB] overflow-hidden">
                <table class="w-full">
                    <thead class="bg-[#F8F3FF] border-b border-[#E5E7EB]">
                        <tr>
                            <th class="text-left px-6 py-4 text-sm font-semibold text-[#374151]">Nama</th>
                            <th class="text-left px-6 py-4 text-sm font-semibold text-[#374151]">Email</th>
                            <th class="text-left px-6 py-4 text-sm font-semibold text-[#374151]">Role</th>
                            <th class="text-left px-6 py-4 text-sm font-semibold text-[#374151]">Status</th>
                            <th class="text-left px-6 py-4 text-sm font-semibold text-[#374151]">Terakhir Login</th>
                            <th class="text-right px-6 py-4 text-sm font-semibold text-[#374151]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E5E7EB]">
                        @forelse($users as $user)
                            <tr class="hover:bg-[#F8F3FF]/50 transition">
                                <td class="px-6 py-4 text-sm font-medium text-[#374151]">
                                    <div class="flex items-center gap-2">
                                        @if($user->avatar)
                                            <img src="{{ Storage::url($user->avatar) }}" class="w-8 h-8 rounded-full object-cover">
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-[#F8F3FF] flex items-center justify-center text-sm">👤</div>
                                        @endif
                                        {{ $user->name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-[#6B7280]">{{ $user->email }}</td>
                                <td class="px-6 py-4">
                                    @php $roleColors = ['user' => 'bg-purple-100 text-purple-700', 'doctor' => 'bg-sky-100 text-sky-700', 'admin' => 'bg-amber-100 text-amber-700']; @endphp
                                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $roleColors[$user->role] ?? 'bg-gray-100 text-gray-600' }}">{{ ucfirst($user->role) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($user->is_banned)
                                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-600">Diblokir</span>
                                    @else
                                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Aktif</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-[#9CA3AF]">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Belum pernah' }}</td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.users.edit', $user) }}" class="text-sm bg-[#F8F3FF] hover:bg-[#E9D5FF] text-[#7E22CE] px-4 py-2 rounded-xl font-medium transition">Edit</a>
                                        <button onclick="openPasswordModal({{ $user->id }}, '{{ $user->name }}')" class="text-sm bg-gray-100 hover:bg-gray-200 text-[#374151] px-4 py-2 rounded-xl font-medium transition">Ganti Password</button>
                                        @if($user->id !== auth()->id())
                                            <form action="{{ route('admin.users.toggleBan', $user) }}" method="POST" class="inline">
                                                @csrf @method('PATCH')
                                                <button class="text-sm {{ $user->is_banned ? 'bg-green-50 hover:bg-green-100 text-green-600' : 'bg-orange-50 hover:bg-orange-100 text-orange-600' }} px-4 py-2 rounded-xl font-medium transition">
                                                    {{ $user->is_banned ? 'Aktifkan' : 'Blokir' }}
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Yakin hapus user {{ $user->name }}?')">
                                                @csrf @method('DELETE')
                                                <button class="text-sm bg-red-50 hover:bg-red-100 text-red-600 px-4 py-2 rounded-xl font-medium transition">Hapus</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-[#6B7280]">Tidak ada pengguna ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $users->links() }}
            </div>
        </div>
    </div>

    {{-- Password Modal --}}
    <div id="passwordModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-purple-100">
            <h3 class="text-lg font-bold text-gray-800 mb-2">Ganti Password</h3>
            <p class="text-xs text-gray-500 mb-6" id="passwordModalUser">User: </p>

            <form id="passwordForm" method="POST" action="">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-[#6B7280] mb-1">Password Baru</label>
                    <input type="password" name="password" class="w-full border border-[#E5E7EB] rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#C084FC]" required minlength="8">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-[#6B7280] mb-1">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="w-full border border-[#E5E7EB] rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#C084FC]" required minlength="8">
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="closePasswordModal()" class="flex-1 px-4 py-2 border border-[#E5E7EB] rounded-xl text-sm font-medium text-[#6B7280] hover:bg-gray-50 transition">Batal</button>
                    <button type="submit" class="flex-1 bg-[#C084FC] hover:bg-[#7E22CE] text-white px-4 py-2 rounded-xl text-sm font-medium transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openPasswordModal(userId, userName) {
            document.getElementById('passwordModalUser').textContent = 'User: ' + userName;
            document.getElementById('passwordForm').action = '/admin/users/' + userId + '/change-password';
            document.getElementById('passwordModal').classList.remove('hidden');
            document.getElementById('passwordModal').classList.add('flex');
        }

        function closePasswordModal() {
            document.getElementById('passwordModal').classList.add('hidden');
            document.getElementById('passwordModal').classList.remove('flex');
        }
    </script>
</x-app-layout>
