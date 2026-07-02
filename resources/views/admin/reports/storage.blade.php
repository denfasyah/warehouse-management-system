@extends('layouts.app')
@section('title', 'Laporan Storage - Admin Panel')
@section('role', 'ADMIN PANEL')

@section('sidebar')
    @include('partials.sidebar_menu_admin')
@endsection

@section('content')
<div class="space-y-5 max-w-6xl mx-auto">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-headline-lg font-bold text-on-surface">Laporan Storage (Kapasitas Gudang)</h1>
            <p class="text-sm text-gray-500 mt-1">Pantau penggunaan kapasitas per lokasi rak penyimpanan.</p>
        </div>
        <a href="{{ route('admin.reports.storage.pdf') }}" target="_blank"
           class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-xl text-sm font-semibold flex items-center gap-2 transition-colors shadow-sm shrink-0">
            <span class="material-symbols-outlined text-[18px]">picture_as_pdf</span> Export PDF
        </a>
    </div>

    {{-- Summary Alert jika ada yang melebihi --}}
    @php
        $overCapCount = $locations->filter(fn($l) => $l->current_fill > $l->capacity)->count();
    @endphp
    @if($overCapCount > 0)
    <div class="flex items-start gap-3 p-4 bg-red-50 border border-red-200 rounded-xl">
        <span class="material-symbols-outlined text-red-600 text-[22px] shrink-0 mt-0.5" style="font-variation-settings:'FILL' 1;">warning</span>
        <div>
            <p class="text-sm font-bold text-red-700">{{ $overCapCount }} Lokasi Melebihi Kapasitas!</p>
            <p class="text-xs text-red-600 mt-0.5">Data <code>current_fill</code> lokasi tersebut melebihi <code>capacity</code> yang ditetapkan. Kemungkinan data seeder/awal melebihi batas. Tidak akan bisa terjadi lagi setelah transaksi baru karena ada validasi kapasitas.</p>
        </div>
    </div>
    @endif

    {{-- Filter --}}
    <div class="glass-card rounded-2xl p-4">
        <form method="GET" action="{{ route('admin.reports.storage') }}" class="flex flex-wrap gap-3 items-end">
            <div class="flex flex-col gap-1">
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Zona</label>
                <select name="zone" onchange="this.form.submit()"
                        class="bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary transition-colors">
                    <option value="">Semua Zona</option>
                    @foreach($zones as $z)
                        <option value="{{ $z }}" {{ request('zone') === $z ? 'selected' : '' }}>Zona {{ $z }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</label>
                <select name="status" onchange="this.form.submit()"
                        class="bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary transition-colors">
                    <option value="">Semua Status</option>
                    <option value="over" {{ request('status') === 'over' ? 'selected' : '' }}>Melebihi Kapasitas</option>
                    <option value="full" {{ request('status') === 'full' ? 'selected' : '' }}>Penuh (≥100%)</option>
                    <option value="empty" {{ request('status') === 'empty' ? 'selected' : '' }}>Kosong</option>
                </select>
            </div>
            @if(request()->anyFilled(['zone', 'status']))
                <a href="{{ route('admin.reports.storage') }}"
                   class="flex items-center gap-1 px-3 py-2 bg-gray-100 text-gray-600 rounded-xl text-sm hover:bg-gray-200 transition-colors">
                    <span class="material-symbols-outlined text-[15px]">close</span> Reset
                </a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="py-3 px-4 font-semibold text-gray-600 text-xs uppercase tracking-wider">Kode Lokasi</th>
                        <th class="py-3 px-4 font-semibold text-gray-600 text-xs uppercase tracking-wider">Zona</th>
                        <th class="py-3 px-4 font-semibold text-gray-600 text-xs uppercase tracking-wider">Kelas CBS</th>
                        <th class="py-3 px-4 font-semibold text-gray-600 text-xs uppercase tracking-wider">Kapasitas Max</th>
                        <th class="py-3 px-4 font-semibold text-gray-600 text-xs uppercase tracking-wider">Terisi (Aktual)</th>
                        <th class="py-3 px-4 font-semibold text-gray-600 text-xs uppercase tracking-wider">Status Penggunaan</th>
                        <th class="py-3 px-4 font-semibold text-gray-600 text-xs uppercase tracking-wider">Barang</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($locations as $loc)
                        @php
                            $filled   = $loc->current_fill; // Sudah di-sync dari pivot
                            $max      = $loc->capacity > 0 ? $loc->capacity : 1;
                            $realPct  = round(($filled / $max) * 100); // Bisa > 100
                            $barPct   = min(100, $realPct);             // Untuk bar UI capped 100
                            $isOver   = $filled > $loc->capacity;
                            $barColor = $isOver ? 'bg-red-600' : ($realPct >= 80 ? 'bg-red-500' : ($realPct >= 50 ? 'bg-yellow-400' : 'bg-green-500'));
                            $textColor = $isOver ? 'text-red-700' : ($realPct >= 80 ? 'text-red-600' : ($realPct >= 50 ? 'text-yellow-600' : 'text-green-600'));
                            $cbsColors = ['fast' => 'red', 'medium' => 'yellow', 'slow' => 'green', 'general' => 'blue'];
                            $cbsColor  = $cbsColors[$loc->storage_class] ?? 'gray';
                        @endphp
                        <tr class="hover:bg-gray-50/70 transition-colors {{ $isOver ? 'bg-red-50/40' : '' }}">
                            <td class="py-3 px-4">
                                <span class="font-bold text-blue-700 bg-blue-50 px-2 py-1 rounded-lg border border-blue-100 text-xs">{{ $loc->code }}</span>
                            </td>
                            <td class="py-3 px-4 font-medium text-gray-700">Zona {{ $loc->zone }}</td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-{{ $cbsColor }}-100 text-{{ $cbsColor }}-700">
                                    {{ ucfirst($loc->storage_class ?? 'general') }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-bold text-gray-700">{{ number_format($loc->capacity) }} Unit</td>
                            <td class="py-3 px-4">
                                <span class="font-bold {{ $isOver ? 'text-red-700' : 'text-gray-700' }}">
                                    {{ number_format($filled) }} Unit
                                </span>
                                @if($isOver)
                                    <span class="ml-1 inline-flex items-center gap-0.5 text-[10px] font-bold text-red-600 bg-red-100 px-1.5 py-0.5 rounded-full">
                                        <span class="material-symbols-outlined text-[12px]">warning</span>
                                        +{{ number_format($filled - $loc->capacity) }} OVER
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2 min-w-[160px]">
                                    <div class="flex-1 bg-gray-200 rounded-full h-2 overflow-hidden">
                                        <div class="{{ $barColor }} h-full rounded-full transition-all duration-500"
                                             style="width: {{ $barPct }}%"></div>
                                    </div>
                                    <span class="text-xs font-bold {{ $textColor }} w-12 text-right">
                                        {{ $isOver ? '>'.$loc->capacity : $realPct.'%' }}
                                    </span>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-xs text-gray-500">
                                {{ $loc->items->count() }} jenis
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-16 text-center">
                                <span class="material-symbols-outlined text-4xl text-gray-300 block mb-2">shelves</span>
                                <p class="text-gray-400">Tidak ada data lokasi penyimpanan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
