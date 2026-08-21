<footer class="bg-[#222222] text-zinc-400 py-14 mt-20">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
      <!-- Brand Column -->
      <div class="space-y-4">
        <a href="{{ url('/') }}" class="inline-block">
          <img onerror="this.onerror=null;this.src=window.SVG_IMAGE_FALLBACK||'data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='800' height='600' viewBox='0 0 800 600' fill='none'%3E%3Crect width='800' height='600' fill='%23F3F4F6'/%3E%3Cpath d='M360 260C360 248.954 368.954 240 380 240H420C431.046 240 440 248.954 440 260V265H450C466.569 265 480 278.431 480 295V345C480 361.569 466.569 375 450 375H350C333.431 375 320 361.569 320 345V295C320 278.431 333.431 265 350 265H360V260Z' stroke='%239CA3AF' stroke-width='12' stroke-linecap='round' stroke-linejoin='round'/%3E%3Ccircle cx='400' cy='320' r='30' stroke='%239CA3AF' stroke-width='12'/%3E%3Cline x1='310' y1='240' x2='490' y2='390' stroke='%23EF4444' stroke-width='10' stroke-linecap='round'/%3E%3Ctext x='400' y='450' font-family='sans-serif' font-size='22' font-weight='700' fill='%236B7280' text-anchor='middle'%3EGambar Tidak Dapat Dimuat%3C/text%3E%3C/svg%3E';" src="{{ asset('images/logo/lensmatch_logo_transparent_yellow.png') }}" class="h-9 w-auto" alt="LensMatch Logo" />
        </a>
        <p class="text-xs text-zinc-400 leading-relaxed">
          Platform marketplace booking fotografer terpercaya. Cari fotografer profesional, buat kontrak aman, serta gabung komunitas fotografi dalam satu tempat.
        </p>
      </div>

      <!-- Marketplace Links -->
      <div>
        <h4 class="text-white text-xs font-bold uppercase tracking-wider mb-4 border-b border-zinc-700/60 pb-2">Marketplace</h4>
        <ul class="space-y-2.5 text-xs">
          <li><a href="{{ url('/fotografer') }}" class="hover:text-amber-400 transition">Cari Fotografer</a></li>
          <li><a href="{{ url('/fotografer?cat=wedding') }}" class="hover:text-amber-400 transition">Fotografi Wedding</a></li>
          <li><a href="{{ url('/fotografer?cat=event') }}" class="hover:text-amber-400 transition">Fotografi Event</a></li>
          <li><a href="{{ url('/fotografer?cat=portrait') }}" class="hover:text-amber-400 transition">Fotografi Portrait</a></li>
        </ul>
      </div>

      <!-- Komunitas Links -->
      <div>
        <h4 class="text-white text-xs font-bold uppercase tracking-wider mb-4 border-b border-zinc-700/60 pb-2">Komunitas</h4>
        <ul class="space-y-2.5 text-xs">
          <li><a href="{{ url('/forum') }}" class="hover:text-amber-400 transition">Forum Diskusi Komunitas</a></li>
          <li><a href="{{ url('/lomba') }}" class="hover:text-amber-400 transition">Informasi Lomba</a></li>
          <li><a href="{{ url('/e-learning') }}" class="hover:text-amber-400 transition">E-Learning</a></li>
        </ul>
      </div>

      <!-- Bantuan & Syarat Links -->
      <div>
        <h4 class="text-white text-xs font-bold uppercase tracking-wider mb-4 border-b border-zinc-700/60 pb-2">Bantuan & Syarat</h4>
        <ul class="space-y-2.5 text-xs">
          <li><a href="{{ route('public.faq') }}" class="hover:text-amber-400 transition">Pusat Bantuan & FAQ</a></li>
          <li><a href="{{ route('public.privacy') }}" class="hover:text-amber-400 transition">Kebijakan Privasi & Escrow</a></li>
          <li><a href="{{ route('public.terms') }}" class="hover:text-amber-400 transition">Syarat & Ketentuan Platform</a></li>
        </ul>
      </div>
    </div>

    <div class="border-t border-zinc-800 mt-12 pt-6 flex flex-col sm:flex-row justify-between items-center text-xs text-zinc-500 gap-4">
      <p>&copy; 2026 LensMatch Platform Booking Fotografer — All rights reserved.</p>
      <div class="flex gap-6">
        <a href="{{ route('public.privacy') }}" class="hover:text-amber-400 transition">Privacy Policy</a>
        <a href="{{ route('public.terms') }}" class="hover:text-amber-400 transition">Terms of Service</a>
      </div>
    </div>
  </div>
</footer>
