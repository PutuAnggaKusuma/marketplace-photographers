@extends('layouts.admin')

@section('title', 'Kelola User & Verifikasi Fotografer — Admin')

@section('content')
<div class="flex justify-between items-center">
  <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Kelola User & Role (admins, clients, photographers)</h1>
</div>

<div class="p-5 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
  @include('partials.table.table-01')
</div>
@endsection
