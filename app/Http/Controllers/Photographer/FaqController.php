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

        return view('photographer.faqs.index', compact('photographer', 'faqs'));
    }

    /**
     * Store a new custom FAQ.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pertanyaan' => 'required|string|max:255',
            'jawaban' => 'required|string|max:2500',
            'urutan' => 'nullable|integer|min:0|max:999',
        ], [
            'pertanyaan.required' => 'Pertanyaan FAQ wajib diisi.',
            'pertanyaan.max' => 'Pertanyaan maksimal 255 karakter.',
            'jawaban.required' => 'Jawaban FAQ wajib diisi.',
            'jawaban.max' => 'Jawaban maksimal 2500 karakter.',
        ]);

        $photographer = RolePhotographer::firstOrCreate(
            ['id_user' => Auth::id()],
            ['nama' => Auth::user()->nama]
        );

        PhotographerFaq::create([
            'id_photographer' => $photographer->id,
            'pertanyaan' => trim($request->pertanyaan),
            'jawaban' => trim($request->jawaban),
            'urutan' => $request->urutan ?? ($photographer->faqs()->count() + 1),
            'is_active' => true,
        ]);

        return redirect()->route('photographer.faqs')->with('success', 'Tanya Jawab (FAQ) berhasil ditambahkan ke etalase profil Anda!');
    }

    /**
     * Update an existing custom FAQ.
     */
    public function update(Request $request, $id)
    {
        $photographer = RolePhotographer::where('id_user', Auth::id())->firstOrFail();
        $faq = PhotographerFaq::where('id_photographer', $photographer->id)->findOrFail($id);

        $request->validate([
            'pertanyaan' => 'required|string|max:255',
            'jawaban' => 'required|string|max:2500',
            'urutan' => 'nullable|integer|min:0|max:999',
        ], [
            'pertanyaan.required' => 'Pertanyaan FAQ wajib diisi.',
            'pertanyaan.max' => 'Pertanyaan maksimal 255 karakter.',
            'jawaban.required' => 'Jawaban FAQ wajib diisi.',
            'jawaban.max' => 'Jawaban maksimal 2500 karakter.',
        ]);

        $faq->update([
            'pertanyaan' => trim($request->pertanyaan),
            'jawaban' => trim($request->jawaban),
            'urutan' => $request->urutan ?? $faq->urutan,
            'is_active' => $request->has('is_active') ? (bool) $request->is_active : true,
        ]);

        return redirect()->route('photographer.faqs')->with('success', 'Pertanyaan FAQ berhasil diperbaharui!');
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

