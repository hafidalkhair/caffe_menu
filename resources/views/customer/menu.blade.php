@extends('layouts.customer')

@section('title', 'Menu Selection')

@section('content')

    {{-- 1. HERO SECTION --}}
    <div class="relative w-full h-52 md:h-80 rounded-[2rem] overflow-hidden mb-8 shadow-float group">
        <img src="https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?q=80&w=1000&auto=format&fit=crop"
             class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-1000 filter sepia-[0.2]"
             alt="Coffee Banner">
        <div class="absolute inset-0 bg-gradient-to-r from-brand-dark/95 via-brand-dark/60 to-transparent flex items-center px-6 md:px-12">
            <div class="max-w-xl relative z-10">
                <span class="inline-block px-4 py-1.5 bg-gradient-primary text-white text-[10px] font-bold uppercase tracking-[0.2em] rounded-full mb-4 shadow-lg border border-white/20">
                    Signature Collection
                </span>
                <h1 class="text-3xl md:text-6xl font-bold text-white leading-[0.9] mb-4 drop-shadow-sm">
                    Taste the <br> <span class="text-transparent bg-clip-text bg-gradient-gold">Excellence.</span>
                </h1>
            </div>
        </div>
    </div>

    {{-- 2. BEST SELLER --}}
    @if (isset($bestSellers) && $bestSellers->count() > 0)
    <div class="mb-10">
        <div class="flex items-center justify-between mb-4 px-1">
            <h2 class="text-xl font-bold text-brand-dark flex items-center gap-3 uppercase tracking-wider">
                <span class="w-8 h-[2px] bg-brand-berry block"></span> Most Loved
            </h2>
        </div>
        <div class="flex gap-5 overflow-x-auto pb-8 snap-x snap-mandatory no-scrollbar -mx-5 px-5 md:mx-0 md:px-0">
            @foreach ($bestSellers as $menu)
                <div class="snap-center flex-shrink-0 w-[180px] md:w-[220px] bg-white rounded-[1.5rem] shadow-soft border border-brand-taupe/10 overflow-hidden hover:shadow-float hover:-translate-y-2 transition-all duration-500 group relative">
                    <div class="relative h-40 md:h-48 overflow-hidden bg-brand-surface">
                        <img src="{{ strpos($menu->image, '/') !== false && !\Illuminate\Support\Str::contains($menu->image, 'menu_images/') ? asset($menu->image) : ($menu->image ? asset('storage/' . $menu->image) : 'https://via.placeholder.com/400x250.png?text=No+Image') }}"
                             class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700"
                             alt="{{ $menu->name }}">
                         <div class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm px-3 py-1.5 rounded-full shadow-sm">
                            <p class="text-[9px] font-bold text-brand-dark uppercase tracking-widest">Top {{ $loop->iteration }}</p>
                        </div>
                    </div>
                    <div class="p-5">
                        <h3 class="text-sm font-bold text-brand-dark truncate mb-1 uppercase tracking-wide">{{ $menu->name }}</h3>
                        <p class="text-brand-berry font-medium text-sm mb-4">Rp{{ number_format($menu->price, 0, ',', '.') }}</p>
                        <form action="{{ route('customer.cart.add') }}" method="POST" class="add-to-cart-form">
                            @csrf
                            <input type="hidden" name="menu_id" value="{{ $menu->id }}">
                            <button type="submit" class="w-full py-2.5 bg-brand-dark text-white text-[10px] font-bold uppercase tracking-[0.15em] rounded-xl hover:bg-brand-berry transition-all shadow-lg group-active:scale-95">Add to Order</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ============================================================ --}}
    {{-- 3. CATEGORY TABS (NEW MODERN FLOATING GLASS) --}}
    {{-- ============================================================ --}}
    {{--
         Container Utama: Sticky dengan z-index tinggi.
         Padding vertikal (py-4) memberikan ruang napas.
         Background dihapus pada container utama agar transparan.
    --}}
    <div id="sticky-tabs-container" class="sticky top-[60px] md:top-[80px] z-30 py-2 mb-6 -mx-4 px-4 md:mx-0 transition-all duration-300">

        {{-- Inner Glass Capsule: Ini yang memberikan efek modern --}}
        <div class="bg-white/70 backdrop-blur-xl border border-white/50 shadow-sm rounded-full p-1.5 flex gap-2 overflow-x-auto no-scrollbar items-center justify-start md:justify-center">

            {{-- Tombol Semua --}}
            <button onclick="filterCategory('all')" id="tab-all"
                class="flex-shrink-0 px-5 py-2.5 rounded-full text-[10px] font-bold uppercase tracking-[0.15em] transition-all duration-300
                bg-brand-dark text-white shadow-md shadow-brand-dark/20 ring-1 ring-brand-dark scale-100 cursor-default">
                All
            </button>

            {{-- Loop Kategori --}}
            @foreach ($groupedMenus as $categoryName => $menus)
                <button onclick="filterCategory('{{ \Illuminate\Support\Str::slug($categoryName) }}')"
                    id="tab-{{ \Illuminate\Support\Str::slug($categoryName) }}"
                    class="flex-shrink-0 px-5 py-2.5 rounded-full text-[10px] font-bold uppercase tracking-[0.15em] text-brand-taupe bg-transparent border border-transparent
                    hover:bg-white hover:text-brand-dark hover:shadow-sm transition-all duration-300">
                    {{ $categoryName }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- 4. MENU GRID LIST --}}
    <div class="min-h-[100vh] space-y-12 pb-20"> {{-- Tambahan padding bottom dan min-height --}}
        @foreach ($groupedMenus as $categoryName => $menus)
            {{--
               KITA HILANGKAN SCROLL-MT DISINI.
               Kita akan menangani offset sepenuhnya lewat JavaScript agar presisi.
            --}}
            <div id="section-{{ \Illuminate\Support\Str::slug($categoryName) }}" class="category-section transition-all duration-500">

                {{-- Section Title --}}
                <div class="flex items-center gap-4 mb-6 pt-4"> {{-- pt-4 memberi jarak dari sticky header --}}
                    <h3 class="text-2xl font-bold text-brand-dark tracking-tight uppercase">{{ $categoryName }}</h3>
                    <div class="h-[1px] flex-grow bg-gradient-to-r from-brand-taupe/30 to-transparent"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-8">
                    @foreach ($menus as $menu)
                        <div class="group flex gap-4 items-start p-3 rounded-[1.5rem] hover:bg-white hover:shadow-soft transition-all duration-300 border border-transparent hover:border-brand-taupe/10">
                            {{-- Image --}}
                            <div class="w-24 h-24 md:w-28 md:h-28 flex-shrink-0 rounded-2xl overflow-hidden bg-brand-surface relative shadow-inner">
                                <img src="{{ strpos($menu->image, '/') !== false && !\Illuminate\Support\Str::contains($menu->image, 'menu_images/') ? asset($menu->image) : ($menu->image ? asset('storage/' . $menu->image) : 'https://via.placeholder.com/400x250.png?text=No+Image') }}"
                                     class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700"
                                     alt="{{ $menu->name }}">
                            </div>

                            {{-- Content --}}
                            <div class="flex flex-col justify-between flex-grow py-1 min-h-[6rem]">
                                <div>
                                    <div class="flex justify-between items-start gap-2">
                                        <h4 class="text-base font-bold text-brand-dark leading-tight mb-1 group-hover:text-brand-berry transition-colors uppercase tracking-wide">
                                            {{ $menu->name }}
                                        </h4>
                                        <span class="text-brand-dark font-bold text-xs bg-brand-surface px-2 py-1 rounded-lg whitespace-nowrap">
                                            {{ number_format($menu->price / 1000, 0) }}K
                                        </span>
                                    </div>
                                    <p class="text-[10px] text-brand-taupe line-clamp-2 leading-relaxed tracking-wide mt-1 font-medium">
                                        {{ $menu->description ?? 'Nikmati cita rasa premium dari menu pilihan kami.' }}
                                    </p>
                                </div>

                                <div class="flex items-end justify-between mt-2">
                                    <span class="text-[9px] text-brand-taupe/60 uppercase tracking-widest font-bold">Aigle Signature</span>
                                    <form action="{{ route('customer.cart.add') }}" method="POST" class="add-to-cart-form">
                                        @csrf
                                        <input type="hidden" name="menu_id" value="{{ $menu->id }}">
                                        <button type="submit" class="w-9 h-9 rounded-xl bg-white border border-brand-taupe/20 text-brand-dark flex items-center justify-center shadow-sm hover:bg-brand-dark hover:text-white hover:border-transparent transition-all duration-300 group-active:scale-90">
                                            <i class="fas fa-plus text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="w-full h-[1px] bg-brand-taupe/10 md:hidden last:hidden"></div>
                    @endforeach
                </div>
            </div>
        @endforeach

        @if (count($groupedMenus) === 0)
            <div class="flex flex-col items-center justify-center py-32 text-center">
                <p class="text-brand-taupe text-xs tracking-wide">Menu Coming Soon</p>
            </div>
        @endif
    </div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        window.filterCategory = (slug) => {
            // 1. Reset Style Tombol (Gaya Modern Minimalis)
            document.querySelectorAll('#category-tabs button').forEach(btn => {
                btn.className = 'flex-shrink-0 px-5 py-2.5 rounded-full text-[10px] font-bold uppercase tracking-[0.15em] text-brand-taupe bg-transparent border border-transparent hover:bg-white hover:text-brand-dark hover:shadow-sm transition-all duration-300 cursor-pointer';
            });

            // 2. Highlight Tombol Aktif (Solid Maroon)
            const activeBtn = document.getElementById(`tab-${slug}`);
            if(activeBtn) {
                activeBtn.className = 'flex-shrink-0 px-5 py-2.5 rounded-full text-[10px] font-bold uppercase tracking-[0.15em] transition-all duration-300 bg-brand-dark text-white shadow-md shadow-brand-dark/20 ring-1 ring-brand-dark scale-100 cursor-default';
                activeBtn.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
            }

            // 3. Logika Tampilan & Scroll Presisi
            const tabsContainer = document.getElementById('sticky-tabs-container');

            // MENGHITUNG OFFSET AGAR TIDAK KETUTUP
            // Navbar (~70px) + Tinggi Tabs (~60px) + Buffer (10px) = ~140px
            const totalHeaderHeight = window.innerWidth < 768 ? 140 : 180;

            if (slug === 'all') {
                document.querySelectorAll('.category-section').forEach(el => {
                    el.style.display = 'block';
                    el.classList.add('animate-fade-in');
                });
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else {
                document.querySelectorAll('.category-section').forEach(el => el.style.display = 'none');

                const target = document.getElementById(`section-${slug}`);
                if (target) {
                    target.style.display = 'block';
                    target.classList.add('animate-fade-in');

                    // SCROLL MANUAL JS
                    if (tabsContainer) {
                        // Ambil posisi absolut Tabs dari paling atas dokumen
                        const tabsRect = tabsContainer.getBoundingClientRect();
                        const absoluteTabsTop = tabsRect.top + window.pageYOffset;

                        // Posisi navbar (sticky)
                        // Karena tabsContainer sticky, posisinya berubah relatif viewport.
                        // Kita gunakan offset manual agar aman.

                        // Cara paling aman: Scroll ke (Posisi Absolut Tabs - Tinggi Navbar)
                        // Tapi karena Tabs sticky, posisi absolutnya bergerak.

                        // SOLUSI TERBAIK: Scroll ke atas sedikit dari tabs
                        const bodyRect = document.body.getBoundingClientRect().top;
                        const targetRect = target.getBoundingClientRect().top;
                        const targetPosition = targetRect - bodyRect;

                        // Scroll ke posisi elemen target dikurangi tinggi header total
                        window.scrollTo({
                            top: targetPosition - totalHeaderHeight,
                            behavior: 'smooth'
                        });
                    }
                }
            }
        };

        // --- ADD TO CART (Tidak berubah) ---
        document.querySelectorAll('.add-to-cart-form').forEach(form => {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const btn = form.querySelector('button');
                const originalContent = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                btn.disabled = true;

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                        body: JSON.stringify({ menu_id: form.querySelector('input[name="menu_id"]').value })
                    });
                    const data = await response.json();
                    if (data.success || data.status === 'success') {
                        const newQty = data.total_qty || data.cart_count;
                        document.querySelectorAll('.cart-badge').forEach(b => {
                            b.textContent = newQty;
                            b.classList.remove('hidden');
                            b.classList.add('scale-125', 'bg-brand-berry');
                            setTimeout(() => b.classList.remove('scale-125', 'bg-brand-berry'), 300);
                        });
                        CoffeeToast.fire({ icon: 'success', title: 'Added' });
                        if (navigator.vibrate) navigator.vibrate(50);
                    } else { throw new Error(data.message); }
                } catch (error) { CoffeeToast.fire({ icon: 'error', title: 'Error' }); }
                finally { btn.innerHTML = originalContent; btn.disabled = false; }
            });
        });
    });
</script>

<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fadeIn 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>
@endpush
