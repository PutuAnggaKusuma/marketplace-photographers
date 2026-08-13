@extends('layouts.photographer')

@section('title', 'Profil & Bio Fotografer — Dashboard')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 dark:text-white">Kelola Profil & Bio Fotografer</h1>

<div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm space-y-4">
  <form class="space-y-4 max-w-2xl">
    <div>
      <label class="block text-xs font-semibold mb-1">Nama Studio / Fotografer</label>
      <input type="text" value="Alex Visual Studio" class="w-full px-4 py-2.5 text-xs rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900" />
    </div>

    <div>
      <label class="block text-xs font-semibold mb-1">Nomor Telepon</label>
      <input type="text" value="081234567890" class="w-full px-4 py-2.5 text-xs rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900" />
    </div>

    <div>
      <label class="block text-xs font-semibold mb-1">Link Sosmed (Instagram / Portofolio)</label>
      <input type="text" value="https://instagram.com/alexvisual" class="w-full px-4 py-2.5 text-xs rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900" />
    </div>

    <div>
      <label class="block text-xs font-semibold mb-1">Alamat Studio / Operasional</label>
      <input type="text" value="Jl. Sudirman No. 45, Jakarta Selatan" class="w-full px-4 py-2.5 text-xs rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900" />
    </div>

    <div>
      <label class="block text-xs font-semibold mb-1">Deskripsi Bio</label>
      <textarea rows="4" class="w-full px-4 py-2.5 text-xs rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900">Spesialis foto wedding & prewedding dengan pengalaman 8 tahun. Siap melayani area Jabodetabek & Bali.</textarea>
    </div>

    <button type="submit" class="px-6 py-2.5 bg-brand-500 text-white rounded-xl text-xs font-bold">Simpan Profil</button>
  </form>
</div>
@endsection
