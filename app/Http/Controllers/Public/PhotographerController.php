<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\RolePhotographer;
use Illuminate\Http\Request;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\Province;

class PhotographerController extends Controller
{
    /**
     * Display Overview Landing Page of Photographers (Hero, Categories Grid, Featured Preview).
     */
    public function index(Request $request)
    {
        // Pure dynamic database categories with photos and descriptions
        $categories = Category::all();
        
        // Take top 4 featured photographers (with services, portfolios, testimonials)
        $featuredPhotographers = RolePhotographer::with(['user', 'province', 'city', 'categories', 'services', 'portfolios.medias', 'testimonials'])
            ->take(4)
            ->get();

        $totalPhotographers = RolePhotographer::count();
        $totalCategories = Category::count();

        return view('public.photographers.index', compact(
            'categories',
            'featuredPhotographers',
            'totalPhotographers',
            'totalCategories'
        ));
    }

    /**
     * Display Full Catalog of Photographers with Filters (Location, Category, Price, Search).
     */
    public function katalog(Request $request)
    {
        $categories = Category::all();
        $provinces = Province::orderBy('name', 'asc')->get();

        $selectedProvince = $request->input('province');
        $selectedCity = $request->input('city');
        $selectedCategory = $request->input('category');
        $searchKeyword = $request->input('q');
        $locationKeyword = $request->input('location');
        $priceMin = $request->input('price_min');
        $priceMax = $request->input('price_max');

        // Cities list based on selected province or default top cities
        $cities = collect();
        if ($selectedProvince) {
            $cities = City::where('province_code', $selectedProvince)->orderBy('name', 'asc')->get();
        }

        $query = RolePhotographer::with(['user', 'province', 'city', 'categories', 'services', 'portfolios.medias', 'testimonials']);

        // Strict Location Search from Beranda (Matches ONLY Location, Address, Province, or City)
        if ($locationKeyword) {
            $matchingProvinceCodes = Province::where('name', 'LIKE', '%' . $locationKeyword . '%')->pluck('code');
            $matchingCityCodes = City::where('name', 'LIKE', '%' . $locationKeyword . '%')->pluck('code');

            $query->where(function ($q) use ($locationKeyword, $matchingProvinceCodes, $matchingCityCodes) {
                $q->where('alamat', 'LIKE', '%' . $locationKeyword . '%');

                if ($matchingProvinceCodes->isNotEmpty()) {
                    $q->orWhereIn('province_code', $matchingProvinceCodes);
                }

                if ($matchingCityCodes->isNotEmpty()) {
                    $q->orWhereIn('city_code', $matchingCityCodes);
                }
            });
        }

        // Generic Catalog Keyword Search (Matches Name, Bio, or Address)
        if ($searchKeyword) {
            $query->where(function ($q) use ($searchKeyword) {
                $q->where('nama', 'LIKE', '%' . $searchKeyword . '%')
                  ->orWhere('deskripsi_bio', 'LIKE', '%' . $searchKeyword . '%')
                  ->orWhere('alamat', 'LIKE', '%' . $searchKeyword . '%');
            });
        }

        // Filter by Province
        if ($selectedProvince) {
            $query->where('province_code', $selectedProvince);
        }

        // Filter by City
        if ($selectedCity) {
            $query->where('city_code', $selectedCity);
        }

        // Filter by Category
        if ($selectedCategory) {
            $query->whereHas('categories', function ($q) use ($selectedCategory) {
                if (is_numeric($selectedCategory)) {
                    $q->where('categories.id', $selectedCategory);
                } else {
                    $q->where('categories.slug', $selectedCategory)
                      ->orWhere('categories.nama_kategori', 'LIKE', '%' . $selectedCategory . '%');
                }
            });
        }

        // Filter by Price Range
        if ($priceMin || $priceMax) {
            $query->whereHas('services', function ($q) use ($priceMin, $priceMax) {
                if ($priceMin) {
                    $q->where('tarif_harga', '>=', $priceMin);
                }
                if ($priceMax) {
                    $q->where('tarif_harga', '<=', $priceMax);
                }
            });
        }

        $perPage = (int) $request->query('per_page', 9);
        if (!in_array($perPage, [9, 12, 24, 48])) {
            $perPage = 9;
        }

        $photographers = $query->paginate($perPage)->withQueryString();

        return view('public.photographers.katalog', compact(
            'photographers',
            'categories',
            'provinces',
            'cities',
            'selectedProvince',
            'selectedCity',
            'selectedCategory',
            'searchKeyword',
            'locationKeyword',
            'priceMin',
            'priceMax'
        ));
    }

    /**
     * Display Detail Page of a Photographer.
     */
    public function show($id)
    {
        $photographer = RolePhotographer::with([
            'user',
            'province',
            'city',
            'categories',
            'services.details',
            'portfolios.medias',
            'availabilities',
            'testimonials.client.user'
        ])->findOrFail($id);

        $testimonials = $photographer->testimonials()->with('client.user')->orderBy('created_at', 'desc')->get();
        $totalReviews = $testimonials->count();
        $avgRating = $totalReviews > 0 ? number_format($testimonials->avg('rating'), 1) : '5.0';

        $starCounts = [
            5 => $testimonials->where('rating', 5)->count(),
            4 => $testimonials->where('rating', 4)->count(),
            3 => $testimonials->where('rating', 3)->count(),
            2 => $testimonials->where('rating', 2)->count(),
            1 => $testimonials->where('rating', 1)->count(),
        ];

        return view('public.photographers.show', compact('photographer', 'testimonials', 'totalReviews', 'avgRating', 'starCounts'));
    }

    /**
     * AJAX Endpoint: Get Cities by Province Code.
     */
    public function getCities($provinceCode)
    {
        $cities = City::where('province_code', $provinceCode)
            ->orderBy('name', 'asc')
            ->get(['code', 'name']);

        return response()->json($cities);
    }
}