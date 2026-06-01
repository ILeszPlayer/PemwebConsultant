<x-app-layout>
    <div class="py-12 bg-[#F8F3FF] min-h-screen">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <a href="{{ route('admin.articles.index') }}" class="text-[#C084FC] hover:text-[#7E22CE] text-sm font-medium transition">&larr; Kembali</a>
                <h1 class="text-3xl font-bold text-[#374151] mt-2">Tambah Artikel Baru</h1>
            </div>

            <div class="bg-white rounded-2xl p-8 shadow-sm border border-[#E5E7EB]">
                <form action="{{ route('admin.articles.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-[#6B7280] mb-1">Judul</label>
                        <input type="text" name="title" value="{{ old('title') }}" class="w-full border border-[#E5E7EB] rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#C084FC]" required>
                        @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-[#6B7280] mb-1">Konten</label>
                        <textarea name="content" rows="12" class="w-full border border-[#E5E7EB] rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#C084FC]" required>{{ old('content') }}</textarea>
                        @error('content') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-[#6B7280] mb-1">Gambar (opsional)</label>
                        <input type="file" name="image" class="w-full border border-[#E5E7EB] rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#C084FC]">
                        @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-6">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="is_published" value="1" checked class="rounded border-[#E5E7EB] text-[#C084FC] focus:ring-[#C084FC]">
                            <span class="text-sm text-[#6B7280]">Publikasikan langsung</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full bg-[#C084FC] hover:bg-[#7E22CE] text-white px-6 py-3 rounded-xl font-medium transition">Simpan Artikel</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
