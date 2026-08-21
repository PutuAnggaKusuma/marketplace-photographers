{{--
  x-user-avatar component (LensMatch UI Standard — Rule #44)
  
  MEKANISME AVATAR USER:
  1. Jika user memiliki foto profil (role_photographers.foto) → tampilkan <img>
  2. Jika TIDAK ada foto → tampilkan lingkaran dengan HURUF INISIAL NAMA USER

  Usage:
    <x-user-avatar :user="$post->user" />
    <x-user-avatar :user="$comment->user" size="w-10 h-10" />
    <x-user-avatar :user="auth()->user()" size="w-9 h-9 sm:w-10 sm:h-10" />
    <x-user-avatar :user="$user" size="w-11 h-11" bg="bg-amber-400" textColor="text-gray-900" />
--}}
@props([
  'user'      => null,
  'size'      => 'w-9 h-9',
  'textSize'  => 'text-xs',
  'bg'        => 'bg-gray-200 dark:bg-gray-700',
  'textColor' => 'text-gray-700 dark:text-gray-200',
  'class'     => '',
])

@php
  // Resolve foto URL: only via role_photographers.foto (users table has no foto column)
  $fotoUrl = $user?->avatar_url ?? null;

  // Initials fallback: up to 2 letters from nama words
  $initials = $user?->initials ?? '?';
@endphp

@if($fotoUrl)
  <img
    src="{{ $fotoUrl }}"
    alt="{{ $user?->nama ?? 'User' }}"
    class="{{ $size }} rounded-full object-cover shrink-0 shadow-xs {{ $class }}"
    onerror="this.onerror=null;this.style.display='none';this.nextElementSibling.style.display='flex';"
  />
  {{-- Hidden fallback div, shown via onerror JS if foto URL fails --}}
  <div class="{{ $size }} {{ $bg }} {{ $textColor }} font-extrabold {{ $textSize }} rounded-full flex items-center justify-center shrink-0 shadow-xs {{ $class }}" style="display:none;">
    {{ $initials }}
  </div>
@else
  <div class="{{ $size }} {{ $bg }} {{ $textColor }} font-extrabold {{ $textSize }} rounded-full flex items-center justify-center shrink-0 shadow-xs {{ $class }}">
    {{ $initials }}
  </div>
@endif