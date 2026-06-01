<x-app-layout>
    <div class="py-12 bg-[#F8F3FF] min-h-screen">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-[#374151]">Manajemen Layanan</h1>
                    <p class="text-[#6B7280] mt-2">Kelola layanan konseling yang tersedia.</p>
                </div>
                <a href="{{ route('admin.services.create') }}" class="bg-[#C084FC] hover:bg-[#7E22CE] text-white px-6 py-3 rounded-full font-medium transition shadow-sm">+ Tambah Layanan</a>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-[#E5E7EB] overflow-hidden">
                <table class="w-full">
                    <thead class="bg-[#F8F3FF] border-b border-[#E5E7EB]">
                        <tr>
                            <th class="text-left px-6 py-4 text-sm font-semibold text-[#374151]">Ikon</th>
                            <th class="text-left px-6 py-4 text-sm font-semibold text-[#374151]">Nama</th>
                            <th class="text-left px-6 py-4 text-sm font-semibold text-[#374151]">Slug</th>
                            <th class="text-left px-6 py-4 text-sm font-semibold text-[#374151]">Status</th>
                            <th class="text-right px-6 py-4 text-sm font-semibold text-[#374151]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E5E7EB]">
                        @forelse($services as $service)
                            <tr class="hover:bg-[#F8F3FF]/50 transition">
                                <td class="px-6 py-4 text-2xl">{{ $service->icon ?? '💬' }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-[#374151]">{{ $service->name }}</td>
                                <td class="px-6 py-4 text-sm text-[#6B7280]">{{ $service->slug }}</td>
                                <td class="px-6 py-4">
                                    @if($service->is_active)
                                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Aktif</span>
                                    @else
                                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.services.edit', $service) }}" class="text-sm bg-[#F8F3FF] hover:bg-[#E9D5FF] text-[#7E22CE] px-4 py-2 rounded-xl font-medium transition">Edit</a>
                                        <form action="{{ route('admin.services.destroy', $service) }}" method="POST" onsubmit="return confirm('Yakin hapus layanan ini? Semua sesi terkait akan ikut terhapus.')">
                                            @csrf @method('DELETE')
                                            <button class="text-sm bg-red-50 hover:bg-red-100 text-red-600 px-4 py-2 rounded-xl font-medium transition">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-[#6B7280]">Belum ada layanan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
