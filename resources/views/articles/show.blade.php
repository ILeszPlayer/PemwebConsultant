<x-app-layout>
    <div class="py-12 bg-[#F8F3FF] min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <a href="{{ route('articles.index') }}" class="text-[#C084FC] hover:text-[#7E22CE] text-sm font-medium transition">&larr; Kembali ke Artikel</a>
            </div>

            <article class="bg-white rounded-3xl p-8 shadow-sm border border-[#E5E7EB]">
                @if($article->image)
                    <img src="{{ Storage::url($article->image) }}" class="w-full h-64 object-cover rounded-2xl mb-6">
                @endif
                <h1 class="text-3xl font-bold text-[#374151] mb-4">{{ $article->title }}</h1>
                <p class="text-sm text-[#9CA3AF] mb-6">{{ $article->created_at->format('d M Y') }}</p>
                <div class="prose prose-purple max-w-none text-[#374151] leading-relaxed">
                    {!! nl2br(e($article->content)) !!}
                </div>
            </article>
        </div>
    </div>
</x-app-layout>
