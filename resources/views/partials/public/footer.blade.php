<footer class="bg-[#222222] text-zinc-400 py-14 mt-20">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
      <!-- Brand Column -->
      <div class="space-y-4">
        <a href="{{ url('/') }}" class="inline-block">
          <img src="{{ asset('images/logo/lensmatch_logo_transparent_yellow.png') }}" class="h-9 w-auto" alt="LensMatch Logo" />
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
          <li><a href="{{ url('/lomba') }}" class="hover:text-amber-400 transition">Kompetisi & Lomba Foto</a></li>
          <li><a href="{{ url('/e-learning') }}" class="hover:text-amber-400 transition">Akademi E-Learning</a></li>
        </ul>
      </div>

      <!-- Bantuan & Syarat Links -->
      <div>
        <h4 class="text-white text-xs font-bold uppercase tracking-wider mb-4 border-b border-zinc-700/60 pb-2">Bantuan & Syarat</h4>
        <ul class="space-y-2.5 text-xs">
          <li><a href="#" class="hover:text-amber-400 transition">Syarat & Ketentuan Kontrak</a></li>
          <li><a href="#" class="hover:text-amber-400 transition">Kebijakan Pembatalan & Refund</a></li>
          <li><a href="#" class="hover:text-amber-400 transition">Pusat Bantuan Client & Fotografer</a></li>
        </ul>
      </div>
    </div>

    <div class="border-t border-zinc-800 mt-12 pt-6 flex flex-col sm:flex-row justify-between items-center text-xs text-zinc-500 gap-4">
      <p>&copy; 2026 LensMatch Platform Booking Fotografer — All rights reserved.</p>
      <div class="flex gap-6">
        <a href="#" class="hover:text-amber-400 transition">Privacy Policy</a>
        <a href="#" class="hover:text-amber-400 transition">Terms of Service</a>
      </div>
    </div>
  </div>
</footer>
