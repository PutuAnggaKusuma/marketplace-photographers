<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;

class HelpController extends Controller
{
    /**
     * Tampilkan Halaman FAQ & Pusat Bantuan.
     */
    public function faq()
    {
        $faqs = config('lensmatch_faqs.help_categories', []);

        return view('public.help.faq', compact('faqs'));
    }

    /**
     * Tampilkan Halaman Kebijakan Privasi.
     */
    public function privacy()
    {
        return view('public.help.privacy');
    }

    /**
     * Tampilkan Halaman Syarat Ketentuan Platform.
     */
    public function terms()
    {
        return view('public.help.terms');
    }
}