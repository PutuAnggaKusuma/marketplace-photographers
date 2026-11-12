<?php

namespace App\Http\Controllers\Photographer;

use App\Http\Controllers\Controller;
use App\Models\PhotographerFaq;
use App\Models\RolePhotographer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FaqController extends Controller
{
    /**
     * Display Photographer Custom FAQs Management Page.
     */
    public function index()
    {
        $user = Auth::user();
        $photographer = RolePhotographer::firstOrCreate(
            ['id_user' => $user->id],
            ['nama' => $user->nama]
        );

        $faqs = $photographer->faqs()->orderBy('urutan', 'asc')->get();
        $systemFaqs = config('lensmatch_faqs.profile_system_faqs', []);

        return view('photographer.faqs.index', compact('photographer', 'faqs', 'systemFaqs'));
    }

    /**
     * Store a new custom FAQ.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pertanyaan' => 'required|string|min:5|max:255',
            'jawaban' => 'required|string|min:10|max:2500',
            'urutan' => 'required|integer|min:1|max:999',
        ], [
            'pertanyaan.required' => 'Pertanyaan FAQ wajib diisi.',
            'pertanyaan.min' => 'Pertanyaan FAQ minimal 5 karakter.',
            'pertanyaan.max' => 'Pertanyaan maksimal 255 karakter.',
            'jawaban.required' => 'Jawaban FAQ wajib diisi.',
            'jawaban.min' => 'Jawaban FAQ minimal 10 karakter.',
            'jawaban.max' => 'Jawaban maksimal 2500 karakter.',
            'urutan.required' => 'Urutan tampil wajib diisi.',
            'urutan.integer' => 'Urutan tampil harus berupa angka.',
            'urutan.min' => 'Urutan tampil minimal 1.',
            'urutan.max' => 'Urutan tampil maksimal 999.',
        ]);

        $photographer = RolePhotographer::firstOrCreate(
            ['id_user' => Auth::id()],
            ['nama' => Auth::user()->nama]
        );

        PhotographerFaq::create([
            'id_photographer' => $photographer->id,
            'pertanyaan' => trim($request->pertanyaan),
            'jawaban' => trim($request->jawaban),
            'urutan' => (int) $request->urutan,
            'is_active' => true,
        ]);

        return redirect()->route('photographer.faqs')->with('success', 'Pertanyaan FAQ berhasil ditambahkan ke profil Anda.');
    }

    /**
     * Update an existing custom FAQ.
     */
    public function update(Request $request, $id)
    {
        $photographer = RolePhotographer::where('id_user', Auth::id())->firstOrFail();
        $faq = PhotographerFaq::where('id_photographer', $photographer->id)->findOrFail($id);

        $request->validate([
            'pertanyaan' => 'required|string|min:5|max:255',
            'jawaban' => 'required|string|min:10|max:2500',
            'urutan' => 'required|integer|min:1|max:999',
        ], [
            'pertanyaan.required' => 'Pertanyaan FAQ wajib diisi.',
            'pertanyaan.min' => 'Pertanyaan FAQ minimal 5 karakter.',
            'pertanyaan.max' => 'Pertanyaan maksimal 255 karakter.',
            'jawaban.required' => 'Jawaban FAQ wajib diisi.',
            'jawaban.min' => 'Jawaban FAQ minimal 10 karakter.',
            'jawaban.max' => 'Jawaban maksimal 2500 karakter.',
            'urutan.required' => 'Urutan tampil wajib diisi.',
            'urutan.integer' => 'Urutan tampil harus berupa angka.',
            'urutan.min' => 'Urutan tampil minimal 1.',
            'urutan.max' => 'Urutan tampil maksimal 999.',
        ]);

        $faq->update([
            'pertanyaan' => trim($request->pertanyaan),
            'jawaban' => trim($request->jawaban),
            'urutan' => (int) $request->urutan,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('photographer.faqs')->with('success', 'Pertanyaan FAQ berhasil diperbaharui.');
    }

    /**
     * Delete a custom FAQ.
     */
    public function destroy($id)
    {
        $photographer = RolePhotographer::where('id_user', Auth::id())->firstOrFail();
        $faq = PhotographerFaq::where('id_photographer', $photographer->id)->findOrFail($id);
        $faq->delete();

        return redirect()->route('photographer.faqs')->with('success', 'Pertanyaan FAQ berhasil dihapus.');
    }
}

