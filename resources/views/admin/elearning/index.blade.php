@extends('layouts.admin')

@section('title', 'Kelola E-Learning — Admin')

@section('content')
<div class="flex justify-between items-center">
  <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Upload & Kelola Materi E-Learning</h1>
  <button class="px-4 py-2 bg-brand-500 text-white rounded-lg text-sm font-semibold">+ Upload Materi</button>
</div>

<div class="p-5 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
  @include('partials.table.table-01')
</div>
@endsection
