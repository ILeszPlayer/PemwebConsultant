<x-app-layout>
    <div class="py-12 bg-[#F8F3FF] min-h-screen">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-[#374151]">Artikel Kesehatan Mental</h1>
                <p class="text-[#6B7280] mt-2">Baca artikel-artikel bermanfaat untuk kesehatan mentalmu.</p>
            </div>

            @forelse($articles as $article)
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB] mb-4 hover:shadow-md transition">
                    <div class="flex items-start gap-4">
                        @if($article->image)
                            <img src="{{ Storage::url($article->image) }}" class="w-20 h-20 rounded-xl object-cover shrink-0">
                        @else
                            <div class="w-20 h-20 rounded-xl bg-[#F8F3FF] flex items-center justify-center text-3xl shrink-0">📝</div>
                        @endif
                        <div class="flex-1">
                            <h3 class="font-semibold text-lg text-[#374151]">{{ $article->title }}</h3>
                            <p class="text-sm text-[#6B7280] mt-1 line-clamp-2">{{ strip_tags($article->content) }}</p>
                            <p class="text-xs text-[#9CA3AF] mt-2">{{ $article->created_at->format('d M Y') }}</p>
                        </div>
                        <a href="{{ route('articles.show', $article) }}" class="shrink-0 bg-[#C084FC] hover:bg-[#7E22CE] text-white px-5 py-2 rounded-full text-sm font-medium transition self-center">Baca</a>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl p-16 shadow-sm border border-[#E5E7EB] text-center">
                    <div class="text-6xl mb-4">📚</div>
                    <h3 class="text-xl font-semibold text-[#374151] mb-2">Belum Ada Artikel</h3>
                    <p class="text-[#6B7280]">Belum ada artikel yang tersedia saat ini.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
