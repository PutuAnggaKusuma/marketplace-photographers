<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Client\ClientBookingController;
use App\Http\Controllers\Photographer\PhotographerBookingController;
use App\Http\Controllers\Public\BookingController;
use App\Http\Controllers\Public\ChatController;
use App\Http\Controllers\Public\PhotographerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public / Universal Routes (Guest & Auth Accessible)
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    $testimonials = \App\Models\Testimonial::with(['client.user', 'photographer'])->latest()->take(10)->get();
    $categories = \App\Models\Category::all();
    $featuredPhotographers = \App\Models\RolePhotographer::with(['user', 'province', 'city', 'categories', 'services', 'portfolios.medias', 'testimonials'])
        ->take(4)
        ->get();
    $totalPhotographers = \App\Models\RolePhotographer::count();
    $totalCategories = \App\Models\Category::count();

    return view('public.home', compact(
        'testimonials', 
        'categories', 
        'featuredPhotographers',
        'totalPhotographers',
        'totalCategories'
    ));
});

Route::get('/fotografer', [PhotographerController::class, 'index'])->name('public.photographers.index');
Route::get('/fotografer/katalog', [PhotographerController::class, 'katalog'])->name('public.photographers.katalog');
Route::get('/fotografer/{id}', [PhotographerController::class, 'show']);
Route::get('/api/cities/{provinceCode}', [PhotographerController::class, 'getCities']);

// Public E-Learning & Forum Routes
Route::get('/elearning', [\App\Http\Controllers\Public\ElearningController::class, 'index'])->name('public.elearning.index');
Route::get('/elearning/katalog', [\App\Http\Controllers\Public\ElearningController::class, 'katalog'])->name('public.elearning.katalog');
Route::get('/e-learning', [\App\Http\Controllers\Public\ElearningController::class, 'index']);
Route::get('/elearning/{slug}', [\App\Http\Controllers\Public\ElearningController::class, 'show'])->name('public.elearning.show');

Route::get('/forum', [\App\Http\Controllers\Public\ForumController::class, 'index'])->name('public.forum.index');
Route::get('/forum/{id}', [\App\Http\Controllers\Public\ForumController::class, 'show'])->name('public.forum.show');
Route::post('/forum/store', [\App\Http\Controllers\Public\ForumController::class, 'storePost'])->name('public.forum.store')->middleware('auth');
Route::post('/forum/{id}/comment', [\App\Http\Controllers\Public\ForumController::class, 'storeComment'])->name('public.forum.comment')->middleware('auth');
Route::post('/forum/comment/{id}/like', [\App\Http\Controllers\Public\ForumController::class, 'likeComment'])->name('public.forum.comment.like');
Route::post('/forum/comment/{id}/dislike', [\App\Http\Controllers\Public\ForumController::class, 'dislikeComment'])->name('public.forum.comment.dislike');

// Public Photo Contest Routes
Route::get('/lomba', [\App\Http\Controllers\Public\ContestController::class, 'index'])->name('public.contests.index');
Route::get('/lomba/{id}', [\App\Http\Controllers\Public\ContestController::class, 'show'])->name('public.contests.show');
Route::post('/lomba/{id}/submit', [\App\Http\Controllers\Public\ContestController::class, 'submit'])->name('public.contests.submit')->middleware('auth');

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

Route::middleware('auth')->group(function () {
    Route::get('/notifications/unread', [\App\Http\Controllers\Public\NotificationController::class, 'getUnread'])->name('notifications.unread');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\Public\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [\App\Http\Controllers\Public\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
});

/*
|--------------------------------------------------------------------------
| Protected Client Routes (Client Only)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:client'])->group(function () {
    Route::get('/booking/create', [BookingController::class, 'create'])->name('booking.create');
    Route::post('/booking/store', [BookingController::class, 'store'])->name('booking.store');
    Route::get('/pembayaran/{id}', [BookingController::class, 'showInvoice'])->name('booking.invoice');

    // Client Dashboard & Order History
    Route::get('/client/bookings', [ClientBookingController::class, 'index'])->name('client.bookings');
    Route::get('/client/invoices', [ClientBookingController::class, 'invoices'])->name('client.invoices');
    Route::get('/client/galleries', [ClientBookingController::class, 'galleries'])->name('client.galleries');
    Route::post('/client/reviews', [\App\Http\Controllers\Public\ReviewController::class, 'store'])->name('client.reviews.store');

    // Real-Time Chat Client Routes
    Route::get('/chat', [ChatController::class, 'index'])->name('client.chat');
    Route::get('/chat/start/{photographerId}', [ChatController::class, 'startChat'])->name('chat.start');
    Route::get('/chat/messages/{chatBookingId}', [ChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');

    // Photographer Financial Reports & Export Routes
    Route::get('/reports', [\App\Http\Controllers\Photographer\ReportController::class, 'index'])->name('reports');
    Route::get('/reports/export', [\App\Http\Controllers\Photographer\ReportController::class, 'export'])->name('reports.export');
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

    // Admin Category Management Routes
    Route::get('/categories', [\App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('categories');
    Route::post('/categories', [\App\Http\Controllers\Admin\CategoryController::class, 'store'])->name('categories.store');
    Route::post('/categories/{id}/update', [\App\Http\Controllers\Admin\CategoryController::class, 'update'])->name('categories.update');
    Route::post('/categories/{id}/delete', [\App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->name('categories.destroy');

    // Admin User & Verification Management Routes
    Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users');
    Route::post('/users/{id}/verify', [\App\Http\Controllers\Admin\UserController::class, 'toggleVerification'])->name('users.verify');
    Route::post('/users/{id}/delete', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');

    // Admin Customer Reviews & Rating Management Routes
    Route::get('/reviews', [\App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('reviews');
    Route::post('/reviews/{id}/toggle-hide', [\App\Http\Controllers\Admin\ReviewController::class, 'toggleHide'])->name('reviews.toggle_hide');
    Route::post('/reviews/{id}/delete', [\App\Http\Controllers\Admin\ReviewController::class, 'destroy'])->name('reviews.destroy');

    // Monitoring Transaksi & Escrow Admin
    Route::get('/transactions', [\App\Http\Controllers\Admin\TransactionController::class, 'index'])->name('transactions');
    Route::get('/transactions/{id}', [\App\Http\Controllers\Admin\TransactionController::class, 'show'])->name('transactions.show');
    Route::post('/transactions/{id}/release-payout', [\App\Http\Controllers\Admin\TransactionController::class, 'releasePayout'])->name('transactions.release-payout');
    Route::post('/transactions/{id}/refund', [\App\Http\Controllers\Admin\TransactionController::class, 'processRefund'])->name('transactions.refund');

    // Admin Photo Contest Management Routes
    Route::get('/contests', [\App\Http\Controllers\Admin\ContestController::class, 'index'])->name('contests');
    Route::post('/contests', [\App\Http\Controllers\Admin\ContestController::class, 'store'])->name('contests.store');
    Route::post('/contests/{id}/update', [\App\Http\Controllers\Admin\ContestController::class, 'update'])->name('contests.update');
    Route::post('/contests/{id}/delete', [\App\Http\Controllers\Admin\ContestController::class, 'destroy'])->name('contests.destroy');
    Route::get('/contests/{id}/submissions', [\App\Http\Controllers\Admin\ContestController::class, 'submissions'])->name('contests.submissions');
    Route::post('/contests/submission/{id}/winner', [\App\Http\Controllers\Admin\ContestController::class, 'setWinner'])->name('contests.submission.winner');

    // Admin E-Learning Management Routes
    Route::get('/elearning', [\App\Http\Controllers\Admin\ElearningController::class, 'index'])->name('elearning');
    Route::post('/elearning', [\App\Http\Controllers\Admin\ElearningController::class, 'store'])->name('elearning.store');
    Route::post('/elearning/{id}/update', [\App\Http\Controllers\Admin\ElearningController::class, 'update'])->name('elearning.update');
    Route::post('/elearning/{id}/delete', [\App\Http\Controllers\Admin\ElearningController::class, 'destroy'])->name('elearning.destroy');

    // Admin Forum Moderation Routes
    Route::get('/forum', [\App\Http\Controllers\Admin\ForumController::class, 'index'])->name('forum');
    Route::post('/forum/{id}/delete', [\App\Http\Controllers\Admin\ForumController::class, 'destroy'])->name('forum.destroy');
    Route::post('/forum/comment/{id}/delete', [\App\Http\Controllers\Admin\ForumController::class, 'destroyComment'])->name('forum.comment.destroy');
});

/*
|--------------------------------------------------------------------------
| Protected Photographer Routes (Photographer Studio Only)
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

    // Photographer Booking Order Management & Gallery Upload Routes
    Route::get('/bookings', [PhotographerBookingController::class, 'index'])->name('bookings');
    Route::post('/bookings/{id}/status', [PhotographerBookingController::class, 'updateStatus'])->name('bookings.status');
    Route::post('/bookings/{id}/gallery', [PhotographerBookingController::class, 'uploadGallery'])->name('bookings.gallery');

    // Real-Time Chat Photographer Studio Routes
    Route::get('/chat', [ChatController::class, 'photographerIndex'])->name('chat');
    Route::get('/chat/messages/{chatBookingId}', [ChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');

    // Photographer Financial Reports & Export Routes
    Route::get('/reports', [\App\Http\Controllers\Photographer\ReportController::class, 'index'])->name('reports');
    Route::get('/reports/export', [\App\Http\Controllers\Photographer\ReportController::class, 'export'])->name('reports.export');
});
/*
|--------------------------------------------------------------------------
| User Profile & Account Security Routes (Universal Auth)
|--------------------------------------------------------------------------
*/
// User Profile & Security Routes (Supports /profile, /client/profile, /photographer/profile, /admin/profile)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [\App\Http\Controllers\Public\ProfileController::class, 'index'])->name('profile.index');
    Route::get('/client/profile', [\App\Http\Controllers\Public\ProfileController::class, 'index']);
    Route::get('/photographer/profile', [\App\Http\Controllers\Public\ProfileController::class, 'index']);
    Route::get('/admin/profile', [\App\Http\Controllers\Public\ProfileController::class, 'index']);
    Route::post('/profile/update', [\App\Http\Controllers\Public\ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/password', [\App\Http\Controllers\Public\ProfileController::class, 'updatePassword'])->name('profile.password');
});
// Public Help, FAQ & Privacy Routes
Route::get('/faq', [\App\Http\Controllers\Public\HelpController::class, 'faq'])->name('public.faq');
Route::get('/kebijakan-privasi', [\App\Http\Controllers\Public\HelpController::class, 'privacy'])->name('public.privacy');
Route::get('/syarat-ketentuan', [\App\Http\Controllers\Public\HelpController::class, 'privacy'])->name('public.terms');