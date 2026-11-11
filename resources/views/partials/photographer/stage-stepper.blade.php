@php
    $isProfileComplete = $photographer->isProfileComplete();
    $isVerified = $photographer->isVerified();
    $isRejected = $photographer->isRejected();
    $isStage1Done = $photographer->isFeatureUnlocked();
    $profilePct = $photographer->getProfileCompletionPercentage();

    $checklist = $photographer->getStorefrontChecklist();
    $items = $checklist['items'];
    $completedCount = $checklist['completed_count'];
    $totalCount = $checklist['total_count'];
    $isReady = $checklist['is_ready'];

    $isStorefrontPending = $photographer->isStorefrontPending();
    $isStorefrontRejected = $photographer->isStorefrontRejected();

    if ($isStage1Done) {
        $stage1StatusText = 'Terverifikasi';
        $stage1StatusClass = 'text-emerald-600 dark:text-emerald-400';
    } elseif ($isRejected) {
        $stage1StatusText = 'Ditolak';
        $stage1StatusClass = 'text-rose-600 dark:text-rose-400';
    } elseif ($isProfileComplete) {
        $stage1StatusText = 'Menunggu Admin';
        $stage1StatusClass = 'text-amber-600 dark:text-amber-400';
    } else {
        $stage1StatusText = $profilePct . '%';
        $stage1StatusClass = 'text-gray-500 dark:text-gray-400';
    }

    if (!$isStage1Done) {
        $stage2StatusText = 'Terkunci';
        $stage2StatusClass = 'text-gray-400 dark:text-gray-500';
    } else {
        $stage2StatusText = $completedCount . '/' . $totalCount . ' Lengkap';
        $stage2StatusClass = 'text-gray-500 dark:text-gray-400';
    }

    if (!$isStage1Done) {
        $stage3StatusText = 'Terkunci';
        $stage3StatusClass = 'text-gray-400 dark:text-gray-500';
    } elseif ($isStorefrontPending) {
        $stage3StatusText = 'Menunggu Admin';
        $stage3StatusClass = 'text-amber-600 dark:text-amber-400';
    } elseif ($isStorefrontRejected) {
        $stage3StatusText = 'Ditolak';
        $stage3StatusClass = 'text-rose-600 dark:text-rose-400';
    } elseif ($isReady) {
        $stage3StatusText = 'Siap Diajukan';
        $stage3StatusClass = 'text-emerald-600 dark:text-emerald-400';
    } else {
        $stage3StatusText = 'Belum Diajukan';
        $stage3StatusClass = 'text-gray-400 dark:text-gray-500';
    }

    $isWeeklyTab = request()->routeIs('photographer.availability') && request('tab', 'weekly') === 'weekly';

    if (!$items['services']['done']) {
        $nextStep = [
            'cta' => 'Buat Paket Layanan',
            'url' => route('photographer.services'),
            'isCurrent' => request()->routeIs('photographer.services*'),
            'hint' => request()->routeIs('photographer.services.create')
                ? 'Simpan paket layanan pada form ini'
                : 'Tambahkan minimal 1 paket layanan',
        ];
    } elseif (!$items['portfolios']['done']) {
        $nextStep = [
            'cta' => 'Unggah Portofolio',
            'url' => route('photographer.portfolio'),
            'isCurrent' => request()->routeIs('photographer.portfolio*'),
            'hint' => request()->routeIs('photographer.portfolio.create')
                ? 'Simpan album portofolio pada form ini'
                : 'Unggah minimal 1 album portofolio',
        ];
    } else {
        $nextStep = [
            'cta' => 'Atur Jadwal',
            'url' => route('photographer.availability', ['tab' => 'weekly']),
            'isCurrent' => $isWeeklyTab || request()->routeIs('photographer.availability.create'),
            'hint' => 'Konfirmasi jam kerja mingguan pada halaman ini',
        ];
    }

    $stage2Url = $nextStep['url'];
    $itemMeta = [
        'services' => [
            'label' => 'Paket Layanan',
            'url' => route('photographer.services'),
        ],
        'portfolios' => [
            'label' => 'Portofolio',
            'url' => route('photographer.portfolio'),
        ],
        'schedule' => [
            'label' => 'Jadwal',
            'url' => route('photographer.availability', ['tab' => 'weekly']),
        ],
    ];
@endphp

<div class="bg-white dark:bg-gray-800 px-5 py-4 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-xs space-y-2.5 print:hidden">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
        <!-- Alur 3 Tahap Fotografer -->
        <div class="flex flex-wrap items-center gap-x-2.5 gap-y-2 text-xs">
            @if(!request()->routeIs('photographer.profile'))
                <a href="{{ route('photographer.profile') }}" class="inline-flex items-center gap-1 hover:opacity-80 transition">
                    <span class="font-extrabold text-gray-900 dark:text-white">1. Profil:</span>
                    <span class="font-bold {{ $stage1StatusClass }}">{{ $stage1StatusText }}</span>
                </a>
            @else
                <div class="inline-flex items-center gap-1">
                    <span class="font-extrabold text-gray-900 dark:text-white">1. Profil:</span>
                    <span class="font-bold {{ $stage1StatusClass }}">{{ $stage1StatusText }}</span>
                </div>
            @endif

            <svg class="w-3.5 h-3.5 text-gray-300 dark:text-gray-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
            </svg>

            @if($isStage1Done && !$nextStep['isCurrent'])
                <a href="{{ $stage2Url }}" class="inline-flex items-center gap-1 hover:opacity-80 transition">
                    <span class="font-extrabold text-gray-900 dark:text-white">2. Etalase:</span>
                    <span class="font-bold {{ $stage2StatusClass }}">{{ $stage2StatusText }}</span>
                </a>
            @else
                <div class="inline-flex items-center gap-1">
                    <span class="font-extrabold {{ $isStage1Done ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-500' }}">2. Etalase:</span>
                    <span class="font-bold {{ $stage2StatusClass }}">{{ $stage2StatusText }}</span>
                </div>
            @endif

            <svg class="w-3.5 h-3.5 text-gray-300 dark:text-gray-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
            </svg>

            <div class="inline-flex items-center gap-1">
                <span class="font-extrabold {{ ($isStage1Done && ($isReady || $isStorefrontPending || $isStorefrontRejected)) ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-500' }}">3. Review Tayang:</span>
                <span class="font-bold {{ $stage3StatusClass }}">{{ $stage3StatusText }}</span>
            </div>
        </div>

        <!-- Aksi Langkah Berikutnya -->
        <div class="flex flex-wrap items-center gap-2.5 text-xs shrink-0">
            @if($isRejected)
                @if(!request()->routeIs('photographer.profile') && !request()->routeIs('photographer.dashboard'))
                    <a href="{{ route('photographer.profile') }}" class="font-extrabold text-rose-600 dark:text-rose-400 hover:text-rose-500 underline underline-offset-4 decoration-rose-400 transition">
                        Perbaiki Profil
                    </a>
                @elseif(request()->routeIs('photographer.profile'))
                    <span class="font-bold text-rose-600 dark:text-rose-400">Perbaiki data lalu simpan ulang</span>
                @endif
            @elseif(!$isProfileComplete)
                @if(!request()->routeIs('photographer.profile'))
                    <span class="text-gray-400 dark:text-gray-500 font-medium">Berikutnya:</span>
                    <a href="{{ route('photographer.profile') }}" class="font-extrabold text-amber-600 dark:text-amber-400 hover:text-amber-500 underline underline-offset-4 decoration-amber-400 transition">
                        Lengkapi Profil
                    </a>
                @else
                    <span class="font-medium text-gray-500 dark:text-gray-400">Lengkapi data profil di bawah</span>
                @endif
            @elseif(!$isVerified)
                <span class="font-bold text-amber-600 dark:text-amber-400">Menunggu verifikasi Admin</span>
            @elseif(!$isReady)
                @if($nextStep['isCurrent'])
                    <span class="font-medium text-gray-500 dark:text-gray-400">{{ $nextStep['hint'] }}</span>
                @else
                    <span class="text-gray-400 dark:text-gray-500 font-medium">Berikutnya:</span>
                    <a href="{{ $nextStep['url'] }}" class="font-extrabold text-amber-600 dark:text-amber-400 hover:text-amber-500 underline underline-offset-4 decoration-amber-400 transition">
                        {{ $nextStep['cta'] }}
                    </a>
                @endif
            @elseif($isStorefrontPending)
                <span class="font-bold text-amber-600 dark:text-amber-400">Sedang ditinjau Admin</span>
            @else
                <form action="{{ route('photographer.storefront.submit') }}"
                      method="POST"
                      data-confirm="true"
                      data-confirm-title="Ajukan Tayang Etalase?"
                      data-confirm-message="Admin akan meninjau paket layanan, portofolio, dan jadwal Anda sebelum tampil di katalog publik."
                      data-confirm-btn="Ya, Ajukan Tayang">
                    @csrf
                    <button type="submit"
                            class="px-4 py-2 bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs rounded-xl shadow-xs transition cursor-pointer">
                        {{ $isStorefrontRejected ? 'Ajukan Ulang Tayang' : 'Ajukan Tayang' }}
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if($isStage1Done && !$isStorefrontPending && !$isReady)
        <div class="flex flex-wrap items-center gap-x-5 gap-y-1.5 text-[11px]">
            @foreach($items as $stepKey => $stepItem)
                @php $meta = $itemMeta[$stepKey]; @endphp
                <a href="{{ $meta['url'] }}" class="inline-flex items-center gap-1 hover:opacity-80 transition">
                    <span class="font-bold text-gray-600 dark:text-gray-300">{{ $meta['label'] }}:</span>
                    @if($stepItem['done'])
                        <span class="font-bold text-emerald-600 dark:text-emerald-400">Lengkap</span>
                    @else
                        <span class="font-bold text-gray-400 dark:text-gray-500">Belum</span>
                    @endif
                </a>
            @endforeach
        </div>
    @endif

    @if($isStage1Done && $isStorefrontRejected)
        <p class="text-xs text-rose-600 dark:text-rose-400 leading-relaxed">
            <span class="font-bold">Catatan Admin:</span> {{ $photographer->catatan_penolakan_etalase ?: 'Etalase belum memenuhi syarat penayangan.' }}
        </p>
    @endif
</div>

