@extends('layouts.admin')

@section('title', 'Kelola Admin & Staff — Admin')

@section('content')
<div class="space-y-6 w-full" x-data="{
    searchQuery: '{{ $search }}',
    currentRole: '{{ $roleFilter ?? $role ?? 'all' }}',
    isLoading: false,
    searchTimeout: null,
    applyFilter(key, val) {
        if (key === 'role') this.currentRole = val;
        let url = new URL(window.location.href);
        url.searchParams.set(key, val);
        url.searchParams.set('page', '1');
        this.fetchTable(url.toString());
    },
    handleSearch(e) {
        this.searchQuery = e.target.value;
        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(() => {
            let url = new URL(window.location.href);
            url.searchParams.set('search', this.searchQuery);
            url.searchParams.set('page', '1');
            this.fetchTable(url.toString());
        }, 300);
    },
    fetchTable(url) {
        this.isLoading = true;
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.text())
            .then(html => {
                let parser = new DOMParser();
                let doc = parser.parseFromString(html, 'text/html');
                let newTable = doc.querySelector('#dashboard-table-container');
                let curTable = document.querySelector('#dashboard-table-container');
                if (newTable && curTable) {
                    curTable.innerHTML = newTable.innerHTML;
                }
                history.pushState(null, '', url);
                this.isLoading = false;
            })
            .catch(err => {
                console.error(err);
                this.isLoading = false;
            });
    }
}">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">Manajemen Admin</h1>
        </div>
        <a href="{{ route('admin.admins.create') }}" class="px-5 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs rounded-xl shadow-xs transition flex items-center gap-2 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path></svg>
            <span>Tambah Admin Baru</span>
        </a>
    </div>

        <!-- Metric Stat Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        <!-- Card 1 -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Total Pengelola</span>
            <div class="text-xs font-black text-gray-900 dark:text-white tracking-tight">{{ $totalAdmins ?? $admins->total() }}</div>
        </div>

        <!-- Card 2 -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Super Admin</span>
            <div class="text-xs font-black text-gray-900 dark:text-white tracking-tight">{{ $totalSuperAdmins ?? $admins->where('role', 'super_admin')->count() }}</div>
        </div>

        <!-- Card 3 -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Admin Operasional</span>
            <div class="text-xs font-black text-gray-900 dark:text-white tracking-tight">{{ $totalStaffAdmins ?? $admins->where('role', 'admin')->count() }}</div>
        </div>

        <!-- Card 4 -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Admin Terdaftar</span>
            <div class="text-xs font-black text-gray-900 dark:text-white tracking-tight">{{ $totalAdmins ?? $admins->total() }} Akun</div>
        </div>
    </div>

    <!-- Standard Data Table Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm overflow-hidden">
        
        <!-- Search Toolbar & Sort -->
        <div class="p-4 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3 w-full md:w-auto">
                @php
                    $roleLabels = [
                        'all' => 'Semua Role Pengelola',
                        'super_admin' => 'Super Admin',
                        'admin' => 'Admin Operasional'
                    ];
                    $curRoleName = $roleLabels[$role ?? 'all'] ?? 'Semua Role Pengelola';
                @endphp
                <div class="relative w-full sm:w-auto min-w-[200px]" x-data="{ open: false, selectedId: '{{ $role ?? 'all' }}', selectedName: '{{ $curRoleName }}' }">
                    <input type="hidden" name="role" :value="selectedId">
                    <button @click="open = !open" type="button" 
                            class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs font-bold text-gray-800 dark:text-white flex items-center justify-between gap-2 shadow-2xs hover:border-amber-400 dark:hover:border-amber-500 cursor-pointer transition">
                        <span x-text="selectedName" class="truncate"></span>
                        <svg class="w-3.5 h-3.5 text-gray-400 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" @click.away="open = false" x-transition 
                          class="absolute z-50 left-0 mt-2 min-w-[210px] w-full bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700/80 py-1 max-h-60 overflow-y-auto" style="display: none;">
                        @foreach($roleLabels as $rKey => $rVal)
                            <button type="button" @click="selectedId = '{{ $rKey }}'; selectedName = '{{ $rVal }}'; open = false; applyFilter('role', '{{ $rKey }}')" 
                                    class="w-full px-3.5 py-2 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition flex items-center justify-between text-left cursor-pointer border-b border-gray-50 dark:border-gray-700/40 last:border-0"
                                    :class="selectedId === '{{ $rKey }}' ? 'bg-amber-50/70 dark:bg-amber-950/30 text-amber-600 font-extrabold' : ''">
                                <span>{{ $rVal }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Search di Kanan -->
            <div class="relative w-full md:w-80">
                <input type="text" 
                       name="search" 
                       value="{{ $search }}" @input="handleSearch($event)" 
                       placeholder="Cari nama atau email admin..." 
                       class="w-full pl-4 pr-10 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">
                <svg class="w-4 h-4 text-gray-400 absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </div>

        <div id="dashboard-table-container" class="transition-opacity duration-200" :class="isLoading ? 'opacity-40 pointer-events-none' : 'opacity-100'">

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-[11px] font-extrabold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            <th class="py-3.5 px-4 sm:px-6 w-4/12">Nama</th>
                            <th class="py-3.5 px-4 sm:px-6 w-3/12">Role</th>
                            <th class="py-3.5 px-4 sm:px-6 w-3/12">Tanggal Terdaftar</th>
                            <th class="py-3.5 px-4 sm:px-6 w-2/12 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 text-xs">
                        @forelse($admins as $a)
                            <tr class="hover:bg-amber-50/30 dark:hover:bg-amber-950/20 transition">
                                <td class="py-4 px-4 sm:px-6 align-middle">
                                    <div class="min-w-0">
                                        <h4 class="font-extrabold text-gray-900 dark:text-white text-xs tracking-tight">{{ $a->nama ?? $a->name ?? 'Admin' }}</h4>
                                        <span class="text-xs text-gray-400 block mt-0.5">{{ $a->email }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-4 sm:px-6 align-middle whitespace-nowrap">
                                    <span class="text-xs font-bold text-gray-900 dark:text-white">
                                        {{ $a->role === 'super_admin' ? 'Super Admin' : 'Admin Operasional' }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 sm:px-6 align-middle text-gray-600 dark:text-gray-400 whitespace-nowrap">
                                    {{ $a->created_at ? $a->created_at->format('d M Y, H:i') : '-' }}
                                </td>
                                <td class="py-4 px-4 sm:px-6 align-middle text-center whitespace-nowrap">
                                    @if(auth()->id() !== $a->id)
                                        <button type="button" 
                                                @click="$dispatch('open-confirm-modal', {
                                                    title: 'Hapus Akun Admin?',
                                                    message: 'Apakah Anda yakin ingin mencabut hak akses dan menghapus admin {{ addslashes($a->nama ?? $a->name) }}?',
                                                    buttonText: 'Ya, Hapus Admin',
                                                    iconType: 'delete',
                                                    buttonClass: 'bg-red-600 hover:bg-red-700 text-white',
                                                    actionUrl: '{{ route('admin.admins.destroy', $a->id) }}',
                                                    method: 'POST'
                                                })"
                                                class="px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200/80 dark:border-rose-900/60 font-bold text-xs transition min-w-[60px] text-center">
                                            Hapus
                                        </button>
                                    @else
                                        <span class="text-xs text-gray-400 italic font-medium">Akun Anda</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-12 text-center text-gray-400 font-bold text-xs">
                                    Belum ada akun admin ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($admins->total() > 0)
                <div class="p-4 bg-gray-50/50 dark:bg-gray-900/50">
                    {{ $admins->links('partials.dashboard.pagination') }}
                </div>
            @endif
        </div>

    </div>

</div>
@endsection