<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Elearning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ElearningController extends Controller
{
    /**
     * Tampilkan Overview Landing Akademi E-Learning.
     */
    public function index(Request $request)
    {
        // Overview preview: Take top 6 MOST READ courses (sorted by view_count DESC)
        $elearnings = Elearning::orderBy('view_count', 'desc')->take(6)->get();
        $categories = Elearning::distinct()->pluck('kategori');

        // Dynamic Aggregate Stats
        $totalCategories = Elearning::distinct('kategori')->count();
        $totalViews = Elearning::sum('view_count');
        $totalCourses = Elearning::count();

        return view('public.elearning.index', compact(
            'elearnings', 
            'categories', 
            'totalCategories',
            'totalViews',
            'totalCourses'
        ));
    }

    /**
     * Tampilkan Halaman Full Data Katalog E-Learning (Search, Filter & Pagination).
     */
    public function katalog(Request $request)
    {
        $selectedCategory = $request->query('category');
        $search = $request->query('q');

        $query = Elearning::orderBy('created_at', 'desc');

        if ($selectedCategory && $selectedCategory !== 'all') {
            $query->where('kategori', $selectedCategory);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('ringkasan', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->query('per_page', 10);
        if (!in_array($perPage, [10, 20, 50, 100])) {
            $perPage = 10;
        }

        $elearnings = $query->paginate($perPage)->withQueryString();
        $categories = Elearning::distinct()->pluck('kategori');

        return view('public.elearning.katalog', compact(
            'elearnings', 
            'categories', 
            'selectedCategory', 
            'search'
        ));
    }

    /**
     * Tampilkan Detail Baca Materi E-Learning (Strict Protected for Logged-In Users).
     */
    public function show($slug)
    {
        if (!Auth::check()) {
            return redirect()->route('login', ['redirect' => route('public.elearning.show', $slug)])
                ->with('error', 'Silakan login terlebih dahulu untuk mengakses materi e-learning.');
        }

        $course = Elearning::where('slug', $slug)->firstOrFail();
        $course->increment('view_count');

        $relatedCourses = Elearning::where('id', '!=', $course->id)
            ->where('kategori', $course->kategori)
            ->take(3)
            ->get();

        return view('public.elearning.show', compact('course', 'relatedCourses'));
    }
}