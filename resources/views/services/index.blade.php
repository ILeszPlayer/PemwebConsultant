<x-app-layout>
    <div class="py-12 bg-[#F8F3FF] min-h-screen">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-[#374151]">Pilih Layanan Konseling</h1>
                <p class="text-[#6B7280] mt-2">Pilih layanan yang paling sesuai dengan kebutuhanmu. Setelah memilih, kamu akan masuk ke ruang chat untuk memulai sesi.</p>
            </div>

            @if(session('success'))
                <div class="bg-[#FCE7F3] border border-[#FBCFE8] text-[#BE185D] rounded-2xl px-6 py-4 mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @if($services->count() > 0)
                <div class="grid md:grid-cols-3 gap-8">
                    @foreach($services as $service)
                        <div class="bg-white rounded-2xl p-8 shadow-sm border border-[#E5E7EB] hover:shadow-md transition flex flex-col">
                            <div class="text-5xl mb-5">{{ $service->icon ?? '💬' }}</div>
                            <h3 class="font-semibold text-xl text-[#374151] mb-3">{{ $service->name }}</h3>
                            <p class="text-[#6B7280] leading-relaxed mb-6 flex-grow">{{ $service->description }}</p>
                            <form action="{{ route('sessions.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="service_id" value="{{ $service->id }}">
                                <button type="submit" class="w-full bg-[#C084FC] hover:bg-[#7E22CE] text-white px-6 py-3 rounded-full font-medium transition text-sm">Pilih Layanan</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-2xl p-12 shadow-sm border border-[#E5E7EB] text-center">
                    <div class="text-5xl mb-4">📋</div>
                    <p class="text-[#6B7280] text-lg">Belum ada layanan tersedia saat ini.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
