@extends('layouts.app')

@section('title', 'Form Pembuatan Booking & Kontrak — LensMatch')

@section('content')
<main class="max-w-4xl mx-auto px-4 py-10 space-y-8 min-h-screen">
  <div class="space-y-2">
    <h1 class="text-3xl font-extrabold">Form Booking & Pengajuan Kontrak</h1>
    <p class="text-xs text-gray-500">1 Kontrak berlaku untuk 1 Fotografer. Pengajuan akan dikonfirmasi oleh Fotografer.</p>
  </div>

  <form action="{{ url('/pembayaran') }}" class="bg-white dark:bg-gray-800 p-8 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm space-y-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="block text-xs font-semibold mb-1">Fotografer Dipilih</label>
        <input type="text" value="Alex Visual Studio" readonly class="w-full px-4 py-2.5 text-xs rounded-xl border border-gray-200 bg-gray-100 dark:bg-gray-900 font-semibold" />
      </div>
      <div>
        <label class="block text-xs font-semibold mb-1">Paket Layanan</label>
        <select class="w-full px-4 py-2.5 text-xs rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900">
          <option value="1">Paket Full-Day Wedding (Rp 7.500.000)</option>
          <option value="2">Paket Half-Day Event (Rp 3.500.000)</option>
        </select>
      </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div>
        <label class="block text-xs font-semibold mb-1">Tanggal Acara</label>
        <input type="date" class="w-full px-4 py-2.5 text-xs rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900" />
      </div>
      <div>
        <label class="block text-xs font-semibold mb-1">Jam Mulai</label>
        <input type="time" class="w-full px-4 py-2.5 text-xs rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900" />
      </div>
      <div>
        <label class="block text-xs font-semibold mb-1">Jam Selesai</label>
        <input type="time" class="w-full px-4 py-2.5 text-xs rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900" />
      </div>
    </div>

    <div>
      <label class="block text-xs font-semibold mb-1">Lokasi Detail Acara</label>
      <textarea rows="3" placeholder="Alamat lengkap gedung / venue..." class="w-full px-4 py-2.5 text-xs rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900"></textarea>
    </div>

    <button type="submit" class="w-full py-3 bg-brand-500 hover:bg-brand-600 text-white font-bold text-sm rounded-xl transition">
      Lanjut ke Pembayaran Midtrans / Transfer
    </button>
  </form>
</main>
@endsection
