<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public / Universal Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('public.home');
});

Route::get('/fotografer', function () {
    return view('public.photographers.index');
});

Route::get('/fotografer/{id}', function ($id) {
    return view('public.photographers.show');
});

Route::get('/forum', function () {
    return view('public.forum.index');
});

Route::get('/forum/{id}', function ($id) {
    return view('public.forum.show');
});

Route::get('/lomba', function () {
    return view('public.contests.index');
});

Route::get('/lomba/{id}', function ($id) {
    return view('public.contests.show');
});

Route::get('/e-learning', function () {
    return view('public.elearning.index');
});

Route::get('/e-learning/{id}', function ($id) {
    return view('public.elearning.show');
});

/*
|--------------------------------------------------------------------------
| Guest Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Protected Client Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:client'])->group(function () {
    Route::get('/booking/create', function () {
        return view('public.booking.create');
    });

    Route::get('/pembayaran', function () {
        return view('public.payment.index');
    });

    Route::get('/chat', function () {
        return view('public.chat.index');
    });
});

/*
|--------------------------------------------------------------------------
| Protected Admin Routes (Super Admin & Admin Only)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:super_admin,admin'])->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::get('/users', function () {
        return view('admin.users.index');
    })->name('users');

    Route::get('/categories', function () {
        return view('admin.categories.index');
    })->name('categories');

    Route::get('/transactions', function () {
        return view('admin.transactions.index');
    })->name('transactions');

    Route::get('/contests', function () {
        return view('admin.contests.index');
    })->name('contests');

    Route::get('/elearning', function () {
        return view('admin.elearning.index');
    })->name('elearning');

    Route::get('/forum', function () {
        return view('admin.forum.index');
    })->name('forum');
});

/*
|--------------------------------------------------------------------------
| Protected Photographer Routes (Photographer Only)
|--------------------------------------------------------------------------
*/
Route::prefix('photographer')->name('photographer.')->middleware(['auth', 'role:photographer'])->group(function () {
    Route::get('/dashboard', function () {
        return view('photographer.dashboard');
    })->name('dashboard');

    Route::get('/profile', function () {
        return view('photographer.profile.index');
    })->name('profile');

    Route::get('/services', function () {
        return view('photographer.services.index');
    })->name('services');

    Route::get('/portfolio', function () {
        return view('photographer.portfolio.index');
    })->name('portfolio');

    Route::get('/availability', function () {
        return view('photographer.availability.index');
    })->name('availability');

    Route::get('/bookings', function () {
        return view('photographer.bookings.index');
    })->name('bookings');

    Route::get('/chat', function () {
        return view('photographer.chat.index');
    })->name('chat');
});
