@extends('layouts.admin')

@section('title', 'Moderasi Forum — Admin')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 dark:text-white">Moderasi Postingan & Komentar Forum</h1>

<div class="p-5 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
  @include('partials.table.table-01')
</div>
@endsection
