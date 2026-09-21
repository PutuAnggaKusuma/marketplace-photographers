<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sertifikat Kelulusan - {{ $course->judul }} - {{ $user->nama }}</title>
  
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700;900&family=Great+Vibes&family=Playfair+Display:ital,wght@0,600;0,800;1,400;1,700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Alex+Brush&display=swap" rel="stylesheet">
  
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  
  <style>
    @media print {
      @page {
        size: A4 landscape;
        margin: 0;
      }
      body {
        background: #ffffff !important;
        margin: 0 !important;
        padding: 0 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
      }
      .no-print {
        display: none !important;
      }
      .cert-container {
        box-shadow: none !important;
        border: none !important;
        margin: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        max-width: 100vw !important;
        max-height: 100vh !important;
        page-break-inside: avoid !important;
        page-break-after: avoid !important;
      }
    }

    .font-cinzel { font-family: 'Cinzel', serif; }
    .font-playfair { font-family: 'Playfair Display', serif; }
    .font-signature { font-family: 'Alex Brush', cursive; }
    .font-vibes { font-family: 'Great Vibes', cursive; }
    .font-jakarta { font-family: 'Plus Jakarta Sans', sans-serif; }

    /* Subtle luxury parchment textured background */
    .cert-bg {
      background: radial-gradient(circle at 50% 50%, #ffffff 0%, #fafaf9 70%, #f5f5f4 100%);
    }

    /* Gold gradient effects */
    .gold-gradient {
      background: linear-gradient(135deg, #fef08a 0%, #f59e0b 50%, #b45309 100%);
    }
    
    .gold-text {
      background: linear-gradient(135deg, #b45309 0%, #f59e0b 50%, #92400e 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
  </style>
</head>
<body class="bg-gray-100 font-jakarta min-h-screen flex flex-col items-center py-6 sm:py-10 px-4 select-none">

  <!-- Floating Action Bar (Hidden in Print) -->
  <div class="no-print w-full max-w-[1050px] mb-6 flex flex-wrap items-center justify-between gap-4 bg-white/90 backdrop-blur-md px-6 py-4 rounded-2xl border border-gray-200 shadow-sm">
    <a href="{{ route('public.elearning.show', $course->slug) }}" class="inline-flex items-center gap-2 text-xs font-bold text-gray-700 hover:text-amber-600 transition">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"></path></svg>
      <span>Kembali ke Ruang Belajar</span>
    </a>

    <div class="flex items-center gap-3">
      <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-200">
        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
        <span>Status: 100% Selesai & Lulus</span>
      </span>

      <button onclick="window.print()" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-gray-900 font-extrabold text-xs rounded-xl shadow-sm transition inline-flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
        <span>Cetak / Unduh PDF</span>
      </button>
    </div>
  </div>

  <!-- Certificate Canvas (A4 Landscape 297mm x 210mm Ratio) -->
  <div class="cert-container cert-bg relative w-full max-w-[1050px] aspect-[1.414/1] bg-white rounded-2xl sm:rounded-3xl border border-gray-300 shadow-2xl overflow-hidden p-8 sm:p-12 flex flex-col justify-between">
    
    <!-- Geometric Luxury Corner Accents (Cyan/Teal & Rich Burgundy/Magenta Ribbons) -->
    <!-- Top Left Accent Wave -->
    <div class="absolute top-0 left-0 w-44 h-44 pointer-events-none z-0">
      <svg viewBox="0 0 200 200" class="w-full h-full" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M0 0 C60 0 120 40 140 100 C150 130 140 170 200 200 L0 200 Z" fill="#0f766e" opacity="0.9" />
        <path d="M0 0 C40 0 80 30 100 80 C110 110 110 150 160 180 L0 180 Z" fill="#831843" opacity="0.85" />
        <path d="M0 0 C20 0 50 20 70 60 C80 90 80 120 120 150 L0 150 Z" fill="#0d9488" opacity="0.95" />
      </svg>
    </div>

    <!-- Bottom Right Accent Wave -->
    <div class="absolute bottom-0 right-0 w-52 h-52 pointer-events-none z-0 rotate-180">
      <svg viewBox="0 0 200 200" class="w-full h-full" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M0 0 C70 0 130 40 150 100 C160 130 150 170 200 200 L0 200 Z" fill="#0f766e" opacity="0.9" />
        <path d="M0 0 C50 0 90 30 110 80 C120 110 120 150 170 180 L0 180 Z" fill="#831843" opacity="0.85" />
        <path d="M0 0 C30 0 60 20 80 60 C90 90 90 120 130 150 L0 150 Z" fill="#0d9488" opacity="0.95" />
      </svg>
    </div>

    <!-- Inner Frame Borders -->
    <div class="absolute inset-4 sm:inset-6 border-2 border-teal-700/60 pointer-events-none rounded-xl"></div>
    <div class="absolute inset-5 sm:inset-7.5 border border-teal-600/30 pointer-events-none rounded-lg"></div>

    <!-- Main Content Inside Frame -->
    <div class="relative z-10 flex flex-col justify-between h-full text-center px-4 sm:px-12 py-2">
      
      <!-- Top Header Section: Logo & Academy Badge -->
      <div class="space-y-1">
        <div class="flex items-center justify-center gap-2">
          <!-- Official LensMatch Academy Logo Mark -->
          <div class="w-8 h-8 rounded-lg bg-teal-800 text-amber-400 flex items-center justify-center shadow-xs font-black text-sm">
            LM
          </div>
          <span class="font-cinzel text-base sm:text-lg font-bold tracking-widest text-teal-950 uppercase">
            LensMatch <span class="text-amber-600 font-extrabold">Academy</span>
          </span>
        </div>
        <p class="text-[10px] tracking-[0.25em] uppercase text-gray-500 font-semibold">Official Photography & Visual Arts Certification</p>
      </div>

      <!-- Center Title Section -->
      <div class="space-y-1 my-auto py-2">
        <h1 class="font-cinzel text-3xl sm:text-5xl font-black text-teal-950 tracking-wider">
          CERTIFICATE
        </h1>
        <p class="font-cinzel text-xs sm:text-sm tracking-[0.35em] text-teal-800 font-bold uppercase">
          OF COMPLETION
        </p>

        <p class="text-[11px] sm:text-xs text-gray-500 font-medium tracking-wide mt-3">
          Diberikan dengan bangga sebagai bukti kelulusan kepada:
        </p>

        <!-- Recipient Name -->
        <div class="py-2">
          <h2 class="font-playfair text-3xl sm:text-5xl font-bold text-gray-900 tracking-tight italic">
            {{ $user->nama }}
          </h2>
          <div class="w-48 sm:w-64 h-[1.5px] bg-gradient-to-r from-transparent via-teal-700 to-transparent mx-auto mt-2"></div>
        </div>

        <!-- Description & Course Name -->
        <p class="text-[11px] sm:text-xs text-gray-600 leading-relaxed max-w-2xl mx-auto px-4">
          Telah berhasil menyelesaikan seluruh rangkaian materi pelatihan, kurikulum pembelajaran, dan evaluasi praktik pada kelas:
        </p>

        <div class="py-1">
          <h3 class="font-jakarta text-base sm:text-xl font-black text-teal-900 uppercase tracking-wide">
            {{ $course->judul }}
          </h3>
          <p class="text-[10px] text-gray-500 font-semibold tracking-wider uppercase mt-0.5">
            Kategori: {{ $course->kategori }} • {{ $totalLessons }} Materi Pembelajaran Tuntas
          </p>
        </div>
      </div>

      <!-- Bottom Signature & Gold Seal Section -->
      <div class="grid grid-cols-3 items-end pt-4 sm:pt-6 border-t border-gray-200/80">
        
        <!-- Left Signature: Lead Photography Instructor -->
        <div class="flex flex-col items-center text-center space-y-1">
          <!-- Handwritten Cursive SVG Vector Signature -->
          <div class="h-10 sm:h-12 flex items-center justify-center">
            <svg class="h-8 sm:h-10 w-28 sm:w-36 text-gray-800" viewBox="0 0 150 50" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
              <path d="M10 35 C 20 10, 30 15, 35 30 C 40 45, 50 10, 60 25 C 70 40, 80 20, 95 25 C 110 30, 120 15, 140 20 M20 28 L 70 28" />
            </svg>
          </div>
          <div class="w-28 sm:w-40 h-[1px] bg-gray-400 mx-auto"></div>
          <p class="text-[11px] font-black text-gray-900 leading-tight mt-1">Reza Pratama, M.Sn</p>
          <p class="text-[9px] font-semibold text-gray-500 uppercase tracking-wider">Lead Photography Instructor</p>
        </div>

        <!-- Center: Official Gold Seal Badge -->
        <div class="flex flex-col items-center justify-center -mb-2">
          <div class="relative w-16 h-16 sm:w-20 sm:h-20 flex items-center justify-center">
            <!-- Golden Starburst Seal Wings -->
            <div class="absolute inset-0 rounded-full gold-gradient shadow-md flex items-center justify-center p-1">
              <div class="w-full h-full rounded-full border border-amber-200/80 flex items-center justify-center p-1 bg-gradient-to-tr from-amber-500 via-amber-400 to-yellow-200">
                <div class="w-full h-full rounded-full border border-dashed border-amber-800/40 flex flex-col items-center justify-center text-center p-1">
                  <!-- Medal Icon -->
                  <svg class="w-5 h-5 sm:w-6 sm:h-6 text-amber-950" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                  </svg>
                  <span class="text-[6px] sm:text-[7px] font-black text-amber-950 uppercase tracking-tighter leading-none mt-0.5">Verified</span>
                </div>
              </div>
            </div>

            <!-- Hanging Gold Ribbons -->
            <div class="absolute -bottom-3 flex justify-center gap-1">
              <div class="w-3 h-5 bg-amber-500 rotate-[-15deg] shadow-xs clip-ribbon"></div>
              <div class="w-3 h-5 bg-amber-600 rotate-[15deg] shadow-xs clip-ribbon"></div>
            </div>
          </div>
        </div>

        <!-- Right Signature: Head of Academy -->
        <div class="flex flex-col items-center text-center space-y-1">
          <!-- Handwritten Cursive SVG Vector Signature -->
          <div class="h-10 sm:h-12 flex items-center justify-center">
            <svg class="h-8 sm:h-10 w-28 sm:w-36 text-gray-800" viewBox="0 0 150 50" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
              <path d="M15 30 C 25 15, 35 40, 50 20 C 65 10, 75 35, 90 25 C 105 15, 120 40, 135 15 M40 32 C 70 32, 100 28, 125 30" />
            </svg>
          </div>
          <div class="w-28 sm:w-40 h-[1px] bg-gray-400 mx-auto"></div>
          <p class="text-[11px] font-black text-gray-900 leading-tight mt-1">Putu Angga Kusuma</p>
          <p class="text-[9px] font-semibold text-gray-500 uppercase tracking-wider">Head of LensMatch Academy</p>
        </div>

      </div>

      <!-- Credential Footer Bar -->
      <div class="flex flex-wrap items-center justify-between text-[9px] sm:text-[10px] text-gray-400 font-medium pt-2 border-t border-gray-100">
        <div>ID Kredensial: <span class="font-mono font-bold text-gray-600">{{ $credentialId }}</span></div>
        <div>Diterbitkan Pada: <span class="font-semibold text-gray-600">{{ $completedAt->translatedFormat('d F Y') }}</span></div>
        <div>Verifikasi Keaslian: <span class="text-teal-700 font-semibold">lensmatch.id/verify</span></div>
      </div>

    </div>
  </div>

</body>
</html>
