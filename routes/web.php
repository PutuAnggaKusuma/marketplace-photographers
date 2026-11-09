<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
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
    $featuredPhotographers = \App\Models\RolePhotographer::where('is_onboarded', true)->with(['user', 'province', 'city', 'categories', 'services', 'portfolios.medias', 'testimonials'])
        ->take(4)
        ->get();
    $totalPhotographers = \App\Models\RolePhotographer::where('is_onboarded', true)->count();
    $totalCategories = \App\Models\Category::count();
    $totalCities = \App\Models\RolePhotographer::where('is_onboarded', true)->whereNotNull('city_code')->distinct('city_code')->count('city_code');
    if ($totalCities === 0) {
        $totalCities = \Laravolt\Indonesia\Models\City::count();
    }
    $totalSessions = \App\Models\Contract::whereHas('bookingDetail', fn($q) => $q->where('status_booking', 'completed'))->count();
    if ($totalSessions === 0) {
        $totalSessions = \App\Models\Contract::count();
    }
    $avgRating = \App\Models\Testimonial::count() > 0 ? number_format(\App\Models\Testimonial::avg('rating'), 1) : '5.0';
    $heroPhotos = \App\Models\PortfolioMedia::take(5)->pluck('media')->toArray();

    return view('public.home', compact(
        'testimonials', 
        'categories', 
        'featuredPhotographers',
        'totalPhotographers',
        'totalCategories',
        'totalCities',
        'totalSessions',
        'avgRating',
        'heroPhotos'
    ));
})->name('home');

Route::get('/fotografer', [PhotographerController::class, 'index'])->name('public.photographers.index');
Route::get('/fotografer/katalog', [PhotographerController::class, 'katalog'])->name('public.photographers.katalog');
Route::get('/fotografer/{id}', [PhotographerController::class, 'show'])->name('public.photographers.show');
Route::get('/user/{id}', [\App\Http\Controllers\Public\ProfileController::class, 'publicShow'])->name('public.user.show');
Route::get('/api/cities/{provinceCode}', [PhotographerController::class, 'getCities']);

// Public E-Learning & Forum Routes
Route::get('/elearning', [\App\Http\Controllers\Public\ElearningController::class, 'index'])->name('public.elearning.index');
Route::get('/elearning/katalog', [\App\Http\Controllers\Public\ElearningController::class, 'katalog'])->name('public.elearning.katalog');
Route::get('/e-learning', [\App\Http\Controllers\Public\ElearningController::class, 'index']);
Route::get('/elearning/{slug}', [\App\Http\Controllers\Public\ElearningController::class, 'show'])->name('public.elearning.show');
Route::get('/elearning/{slug}/certificate', [\App\Http\Controllers\Public\ElearningController::class, 'certificate'])->name('public.elearning.certificate')->middleware('auth');
Route::get('/elearning/{slug}/checkout', [\App\Http\Controllers\Public\ElearningController::class, 'checkout'])->name('public.elearning.checkout');
Route::post('/elearning/{slug}/checkout', [\App\Http\Controllers\Public\ElearningController::class, 'processCheckout'])->name('public.elearning.checkout.process');
Route::post('/elearning/{id}/toggle-progress', [\App\Http\Controllers\Public\ElearningController::class, 'toggleProgress'])->name('public.elearning.toggle-progress')->middleware('auth');
Route::post('/elearning/lesson/{id}/toggle', [\App\Http\Controllers\Public\ElearningController::class, 'toggleLessonProgress'])->name('public.elearning.lesson.toggle')->middleware('auth');

Route::get('/forum', [\App\Http\Controllers\Public\ForumController::class, 'index'])->name('public.forum.index');
Route::get('/forum/{id}', [\App\Http\Controllers\Public\ForumController::class, 'show'])->name('public.forum.show');
Route::post('/forum/store', [\App\Http\Controllers\Public\ForumController::class, 'storePost'])->name('public.forum.store')->middleware('auth');
Route::post('/forum/{id}/comment', [\App\Http\Controllers\Public\ForumController::class, 'storeComment'])->name('public.forum.comment')->middleware('auth');
Route::post('/forum/comment/{id}/like', [\App\Http\Controllers\Public\ForumController::class, 'likeComment'])->name('public.forum.comment.like');
Route::post('/forum/comment/{id}/dislike', [\App\Http\Controllers\Public\ForumController::class, 'dislikeComment'])->name('public.forum.comment.dislike');
Route::post('/forum/comment/{id}/report', [\App\Http\Controllers\Public\ForumController::class, 'reportComment'])->name('public.forum.comment.report')->middleware('auth');
Route::post('/forum/comment/{id}/user-delete', [\App\Http\Controllers\Public\ForumController::class, 'deleteComment'])->name('public.forum.comment.delete')->middleware('auth');

// Public Photo Contest Routes
Route::get('/lomba', [\App\Http\Controllers\Public\ContestController::class, 'index'])->name('public.contests.index');
Route::get('/lomba/{id}', [\App\Http\Controllers\Public\ContestController::class, 'show'])->name('public.contests.show');
Route::post('/lomba/{id}/register', [\App\Http\Controllers\Public\ContestController::class, 'registerParticipant'])->name('public.contests.register')->middleware('auth');
Route::post('/lomba/{id}/payment', [\App\Http\Controllers\Public\ContestController::class, 'uploadPaymentProof'])->name('public.contests.payment')->middleware('auth');
Route::post('/lomba/{id}/submit', [\App\Http\Controllers\Public\ContestController::class, 'submit'])->name('public.contests.submit')->middleware('auth');
Route::get('/lomba-pengajuan/buat', [\App\Http\Controllers\Public\ContestProposalController::class, 'create'])->name('public.contests.propose.create')->middleware('auth');
Route::post('/lomba-pengajuan/simpan', [\App\Http\Controllers\Public\ContestProposalController::class, 'store'])->name('public.contests.propose.store')->middleware('auth');
Route::get('/lomba-pengajuan/riwayat', [\App\Http\Controllers\Public\ContestProposalController::class, 'myProposals'])->name('public.contests.propose.history')->middleware('auth');

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

    // Password Reset Routes
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/chat/unread-count', [\App\Http\Controllers\Public\ChatController::class, 'getUnreadCount'])->name('chat.unread-count');
    Route::post('/chat/typing', [\App\Http\Controllers\Public\ChatController::class, 'setTyping'])->name('chat.typing');
    Route::patch('/chat/messages/{id}', [\App\Http\Controllers\Public\ChatController::class, 'updateMessage'])->name('chat.message.update');
    Route::delete('/chat/messages/{id}', [\App\Http\Controllers\Public\ChatController::class, 'deleteMessage'])->name('chat.message.delete');
    Route::post('/chat/custom-offer', [\App\Http\Controllers\Public\ChatController::class, 'storeCustomOffer'])->name('chat.custom_offer.store');
    Route::post('/chat/custom-offer/{id}/accept', [\App\Http\Controllers\Public\ChatController::class, 'acceptCustomOffer'])->name('chat.custom_offer.accept');
    Route::post('/chat/custom-offer/{id}/reject', [\App\Http\Controllers\Public\ChatController::class, 'rejectCustomOffer'])->name('chat.custom_offer.reject');
    Route::get('/notifications/unread', [\App\Http\Controllers\Public\NotificationController::class, 'getUnread'])->name('notifications.unread');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\Public\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [\App\Http\Controllers\Public\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
});

// Universal Dashboard & Base Route Role Dispatcher
Route::get('/dashboard', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }
    $role = auth()->user()->role;
    if (in_array($role, ['super_admin', 'admin'])) {
        return redirect()->route('admin.dashboard');
    } elseif ($role === 'photographer') {
        return redirect()->route('photographer.dashboard');
    } else {
        return redirect('/');
    }
})->name('dashboard');

Route::get('/admin', fn() => redirect()->route('admin.dashboard'));
Route::get('/photographer', fn() => redirect()->route('photographer.dashboard'));
Route::get('/client', fn() => redirect('/'));

/*
|--------------------------------------------------------------------------
| Protected Client Routes (Client Only)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:client'])->group(function () {
    Route::get('/booking/create', [BookingController::class, 'create'])->name('booking.create');
    Route::post('/booking/store', [BookingController::class, 'store'])->name('booking.store');
    Route::get('/pembayaran/{id}', [BookingController::class, 'showInvoice'])->name('booking.invoice');
    Route::get('/pembayaran/{id}/cetak', [BookingController::class, 'printInvoice'])->name('booking.invoice.print');
    Route::post('/pembayaran/{id}/konfirmasi', [BookingController::class, 'confirmPayment'])->name('booking.pay.confirm');
    Route::get('/elearning/transaksi/{id}/cetak', [\App\Http\Controllers\Public\ElearningController::class, 'printInvoice'])->name('elearning.invoice.print');

    // Client Dashboard & Order History
    Route::get('/client/bookings', [ClientBookingController::class, 'index'])->name('client.bookings');
    Route::post('/client/bookings/{id}/cancel', [ClientBookingController::class, 'cancelBooking'])->name('client.bookings.cancel');
    Route::post('/client/bookings/{id}/reschedule', [ClientBookingController::class, 'requestReschedule'])->name('client.bookings.reschedule');
    Route::post('/client/bookings/{id}/reschedule-respond', [ClientBookingController::class, 'respondReschedule'])->name('client.bookings.reschedule-respond');
    Route::get('/client/invoices', [ClientBookingController::class, 'invoices'])->name('client.invoices');
    Route::get('/client/favorites', [\App\Http\Controllers\Client\ClientFavoriteController::class, 'index'])->name('client.favorites');
    Route::post('/client/favorites/{photographerId}/toggle', [\App\Http\Controllers\Client\ClientFavoriteController::class, 'toggle'])->name('client.favorites.toggle');
    Route::get('/client/galleries', [ClientBookingController::class, 'galleries'])->name('client.galleries');
    Route::post('/client/bookings/{id}/confirm-gallery', [ClientBookingController::class, 'confirmGallery'])->name('client.bookings.confirm-gallery');
    Route::post('/client/bookings/{id}/request-revision', [ClientBookingController::class, 'requestRevision'])->name('client.bookings.request-revision');
    Route::post('/client/reviews', [\App\Http\Controllers\Public\ReviewController::class, 'store'])->name('client.reviews.store');
    Route::put('/client/reviews/{id}', [\App\Http\Controllers\Public\ReviewController::class, 'update'])->name('client.reviews.update');
    Route::delete('/client/reviews/{id}', [\App\Http\Controllers\Public\ReviewController::class, 'destroy'])->name('client.reviews.destroy');

    // Real-Time Chat Client Routes
    Route::get('/chat', [ChatController::class, 'index'])->name('client.chat');
    Route::get('/chat/start/{photographerId}', [ChatController::class, 'startChat'])->name('chat.start');
    Route::get('/chat/messages/{chatBookingId}', [ChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
});

/*
|--------------------------------------------------------------------------
| Protected Admin Routes (Super Admin & Admin Only)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:super_admin,admin'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Admin Category Management Routes
    Route::get('/categories', [\App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('categories');
    Route::get('/categories/create', [\App\Http\Controllers\Admin\CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [\App\Http\Controllers\Admin\CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{id}/edit', [\App\Http\Controllers\Admin\CategoryController::class, 'edit'])->name('categories.edit');
    Route::post('/categories/{id}/update', [\App\Http\Controllers\Admin\CategoryController::class, 'update'])->name('categories.update');
    Route::post('/categories/{id}/delete', [\App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->name('categories.destroy');

    // Admin User & Client Management Routes
    Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users');
    Route::post('/users/{id}/delete', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');

    // Admin Dedicated Photographer & Verification Routes
    Route::get('/photographers', [\App\Http\Controllers\Admin\PhotographerVerificationController::class, 'index'])->name('photographers');
    Route::get('/photographers/{id}', [\App\Http\Controllers\Admin\PhotographerVerificationController::class, 'show'])->name('photographers.show');
    Route::post('/photographers/{id}/verify', [\App\Http\Controllers\Admin\PhotographerVerificationController::class, 'toggleVerification'])->name('photographers.verify');
    Route::post('/photographers/{id}/reject', [\App\Http\Controllers\Admin\PhotographerVerificationController::class, 'reject'])->name('photographers.reject');
    Route::post('/photographers/{id}/delete', [\App\Http\Controllers\Admin\PhotographerVerificationController::class, 'destroy'])->name('photographers.destroy');

    // Admin Dedicated Staff & Super Admin Management Routes (Super Admin Only)
    Route::get('/admins', [\App\Http\Controllers\Admin\AdminStaffController::class, 'index'])->name('admins');
    Route::get('/admins/create', [\App\Http\Controllers\Admin\AdminStaffController::class, 'create'])->name('admins.create');
    Route::post('/admins', [\App\Http\Controllers\Admin\AdminStaffController::class, 'store'])->name('admins.store');
    Route::post('/admins/{id}/delete', [\App\Http\Controllers\Admin\AdminStaffController::class, 'destroy'])->name('admins.destroy');

    // Admin Customer Reviews & Rating Management Routes
    Route::get('/reviews', [\App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('reviews');
    Route::post('/reviews/{id}/toggle-hide', [\App\Http\Controllers\Admin\ReviewController::class, 'toggleHide'])->name('reviews.toggle_hide');
    Route::post('/reviews/{id}/delete', [\App\Http\Controllers\Admin\ReviewController::class, 'destroy'])->name('reviews.destroy');

    // Monitoring Transaksi & Escrow Admin
    Route::get('/transactions', [\App\Http\Controllers\Admin\TransactionController::class, 'index'])->name('transactions');
    Route::get('/transactions/{id}', [\App\Http\Controllers\Admin\TransactionController::class, 'show'])->name('transactions.show');
    Route::post('/transactions/{id}/verify-payment', [\App\Http\Controllers\Admin\TransactionController::class, 'verifyPayment'])->name('transactions.verify-payment');
    Route::post('/transactions/{id}/reject-payment', [\App\Http\Controllers\Admin\TransactionController::class, 'rejectPayment'])->name('transactions.reject-payment');
    Route::post('/transactions/{id}/release-payout', [\App\Http\Controllers\Admin\TransactionController::class, 'releasePayout'])->name('transactions.release-payout');
    Route::post('/transactions/{id}/refund', [\App\Http\Controllers\Admin\TransactionController::class, 'processRefund'])->name('transactions.refund');

    // Admin Activity Logs & Audit Trail Routes
    Route::get('/activity-logs', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity_logs');
    Route::post('/activity-logs/clear', [\App\Http\Controllers\Admin\ActivityLogController::class, 'clearOld'])->name('activity_logs.clear');

    // Admin Photo Contest Management Routes
    Route::get('/contests', [\App\Http\Controllers\Admin\ContestController::class, 'index'])->name('contests');
    Route::get('/contests/create', [\App\Http\Controllers\Admin\ContestController::class, 'create'])->name('contests.create');
    Route::post('/contests', [\App\Http\Controllers\Admin\ContestController::class, 'store'])->name('contests.store');
    Route::get('/contests/{id}/edit', [\App\Http\Controllers\Admin\ContestController::class, 'edit'])->name('contests.edit');
    Route::post('/contests/{id}/update', [\App\Http\Controllers\Admin\ContestController::class, 'update'])->name('contests.update');
    Route::post('/contests/{id}/delete', [\App\Http\Controllers\Admin\ContestController::class, 'destroy'])->name('contests.destroy');
    Route::post('/contests/{id}/approve', [\App\Http\Controllers\Admin\ContestController::class, 'approve'])->name('contests.approve');
    Route::post('/contests/{id}/reject', [\App\Http\Controllers\Admin\ContestController::class, 'reject'])->name('contests.reject');
    Route::get('/contests/{id}/manage', [\App\Http\Controllers\Admin\ContestController::class, 'manage'])->name('contests.manage');
    Route::get('/contests/{id}/participants', [\App\Http\Controllers\Admin\ContestController::class, 'participants'])->name('contests.participants');
    Route::post('/contests/participant/{id}/update-status', [\App\Http\Controllers\Admin\ContestController::class, 'updateParticipantStatus'])->name('contests.participant.update_status');
    Route::post('/contests/participant/{id}/approve', [\App\Http\Controllers\Admin\ContestController::class, 'approveParticipant'])->name('contests.participant.approve');
    Route::post('/contests/participant/{id}/reject', [\App\Http\Controllers\Admin\ContestController::class, 'rejectParticipant'])->name('contests.participant.reject');
    Route::post('/contests/participant/{id}/verify-payment', [\App\Http\Controllers\Admin\ContestController::class, 'verifyPayment'])->name('contests.participant.verify_payment');
    Route::get('/contests/{id}/submissions', [\App\Http\Controllers\Admin\ContestController::class, 'submissions'])->name('contests.submissions');
    Route::post('/contests/submission/{id}/winner', [\App\Http\Controllers\Admin\ContestController::class, 'setWinner'])->name('contests.submission.winner');

    // Admin E-Learning Management Routes
    Route::get('/elearning', [\App\Http\Controllers\Admin\ElearningController::class, 'index'])->name('elearning');
    Route::get('/elearning/create', [\App\Http\Controllers\Admin\ElearningController::class, 'create'])->name('elearning.create');
    Route::post('/elearning', [\App\Http\Controllers\Admin\ElearningController::class, 'store'])->name('elearning.store');
    Route::get('/elearning/{id}/edit', [\App\Http\Controllers\Admin\ElearningController::class, 'edit'])->name('elearning.edit');
    Route::post('/elearning/{id}/update', [\App\Http\Controllers\Admin\ElearningController::class, 'update'])->name('elearning.update');
    Route::post('/elearning/{id}/delete', [\App\Http\Controllers\Admin\ElearningController::class, 'destroy'])->name('elearning.destroy');

    // Admin E-Learning Curriculum (Topik & Tahapan Belajar)
    Route::get('/elearning/{id}/curriculum', [\App\Http\Controllers\Admin\ElearningController::class, 'curriculum'])->name('elearning.curriculum');
    Route::post('/elearning/{id}/modules', [\App\Http\Controllers\Admin\ElearningController::class, 'storeModule'])->name('elearning.modules.store');
    Route::post('/elearning/modules/{id}/update', [\App\Http\Controllers\Admin\ElearningController::class, 'updateModule'])->name('elearning.modules.update');
    Route::post('/elearning/modules/{id}/delete', [\App\Http\Controllers\Admin\ElearningController::class, 'destroyModule'])->name('elearning.modules.destroy');
    Route::post('/elearning/{id}/lessons', [\App\Http\Controllers\Admin\ElearningController::class, 'storeLesson'])->name('elearning.lessons.store');
    Route::post('/elearning/lessons/{id}/update', [\App\Http\Controllers\Admin\ElearningController::class, 'updateLesson'])->name('elearning.lessons.update');
    Route::post('/elearning/lessons/{id}/delete', [\App\Http\Controllers\Admin\ElearningController::class, 'destroyLesson'])->name('elearning.lessons.destroy');
    Route::post('/elearning/lessons/{id}/toggle-free', [\App\Http\Controllers\Admin\ElearningController::class, 'toggleLessonFree'])->name('elearning.lessons.toggle-free');

    // Admin Forum Moderation Routes
    Route::get('/forum', [\App\Http\Controllers\Admin\ForumController::class, 'index'])->name('forum');
    Route::post('/forum/{id}/delete', [\App\Http\Controllers\Admin\ForumController::class, 'destroy'])->name('forum.destroy');
    Route::post('/forum/comment/{id}/delete', [\App\Http\Controllers\Admin\ForumController::class, 'destroyComment'])->name('forum.comment.destroy');
    Route::post('/forum/comment/{id}/dismiss-reports', [\App\Http\Controllers\Admin\ForumController::class, 'dismissReports'])->name('forum.comment.dismiss_reports');
});

/*
|--------------------------------------------------------------------------
| Protected Photographer Routes (Photographer Studio Only)
|--------------------------------------------------------------------------
*/
Route::prefix('photographer')->name('photographer.')->middleware(['auth', 'role:photographer'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Photographer\DashboardController::class, 'index'])->name('dashboard');
    Route::post('/onboarding/complete', [\App\Http\Controllers\Photographer\OnboardingController::class, 'complete'])->name('onboarding.complete');

    Route::get('/profile', [\App\Http\Controllers\Public\ProfileController::class, 'index'])->name('profile');

    // Operational Features - Locked until 100% profile completed & Admin approved
    Route::middleware('photographer.verified')->group(function () {
        Route::get('/services', [\App\Http\Controllers\Photographer\ServiceController::class, 'index'])->name('services');
        Route::get('/services/create', [\App\Http\Controllers\Photographer\ServiceController::class, 'create'])->name('services.create');
        Route::post('/services', [\App\Http\Controllers\Photographer\ServiceController::class, 'store'])->name('services.store');
        Route::get('/services/{id}/edit', [\App\Http\Controllers\Photographer\ServiceController::class, 'edit'])->name('services.edit');
        Route::post('/services/{id}/update', [\App\Http\Controllers\Photographer\ServiceController::class, 'update'])->name('services.update');
        Route::post('/services/{id}/delete', [\App\Http\Controllers\Photographer\ServiceController::class, 'destroy'])->name('services.delete');

        Route::get('/portfolio', [\App\Http\Controllers\Photographer\PortfolioController::class, 'index'])->name('portfolio');
        Route::get('/portfolio/create', [\App\Http\Controllers\Photographer\PortfolioController::class, 'create'])->name('portfolio.create');
        Route::post('/portfolio', [\App\Http\Controllers\Photographer\PortfolioController::class, 'store'])->name('portfolio.store');
        Route::get('/portfolio/{id}/edit', [\App\Http\Controllers\Photographer\PortfolioController::class, 'edit'])->name('portfolio.edit');
        Route::post('/portfolio/{id}/update', [\App\Http\Controllers\Photographer\PortfolioController::class, 'update'])->name('portfolio.update');
        Route::post('/portfolio/{id}/delete', [\App\Http\Controllers\Photographer\PortfolioController::class, 'destroy'])->name('portfolio.delete');

        Route::get('/availability', [\App\Http\Controllers\Photographer\AvailabilityController::class, 'index'])->name('availability');
        Route::get('/availability/create', [\App\Http\Controllers\Photographer\AvailabilityController::class, 'create'])->name('availability.create');
        Route::post('/availability', [\App\Http\Controllers\Photographer\AvailabilityController::class, 'store'])->name('availability.store');
        Route::post('/availability/working-hours', [\App\Http\Controllers\Photographer\AvailabilityController::class, 'updateWorkingHours'])->name('availability.update-working-hours');
        Route::post('/availability/{id}/delete', [\App\Http\Controllers\Photographer\AvailabilityController::class, 'destroy'])->name('availability.delete');

        // Photographer Booking Order Management & Gallery Upload Routes
        Route::get('/bookings', [PhotographerBookingController::class, 'index'])->name('bookings');
        Route::post('/bookings/{id}/status', [PhotographerBookingController::class, 'updateStatus'])->name('bookings.status');
        Route::post('/bookings/{id}/gallery', [PhotographerBookingController::class, 'uploadGallery'])->name('bookings.gallery');
        Route::post('/bookings/{id}/reschedule', [PhotographerBookingController::class, 'requestReschedule'])->name('bookings.reschedule');
        Route::post('/bookings/{id}/reschedule-respond', [PhotographerBookingController::class, 'respondReschedule'])->name('bookings.reschedule-respond');
        Route::post('/bookings/{id}/rate-client', [PhotographerBookingController::class, 'rateClient'])->name('bookings.rate-client');

        // Photographer Custom FAQ Routes
        Route::get('/faqs', [\App\Http\Controllers\Photographer\FaqController::class, 'index'])->name('faqs');
        Route::post('/faqs', [\App\Http\Controllers\Photographer\FaqController::class, 'store'])->name('faqs.store');
        Route::post('/faqs/{id}/update', [\App\Http\Controllers\Photographer\FaqController::class, 'update'])->name('faqs.update');
        Route::post('/faqs/{id}/delete', [\App\Http\Controllers\Photographer\FaqController::class, 'destroy'])->name('faqs.destroy');

        // Real-Time Chat Photographer Studio Routes
        Route::get('/chat', [ChatController::class, 'photographerIndex'])->name('chat');
        Route::get('/chat/client/{clientId}', [ChatController::class, 'photographerStartChat'])->name('chat.client');
        Route::get('/chat/messages/{chatBookingId}', [ChatController::class, 'getMessages'])->name('chat.messages');
        Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');

        // Photographer Financial Reports & Export Routes
        Route::get('/reports', [\App\Http\Controllers\Photographer\ReportController::class, 'index'])->name('reports');
        Route::get('/reports/export', [\App\Http\Controllers\Photographer\ReportController::class, 'export'])->name('reports.export');
        Route::post('/reports/{id}/request-payout', [\App\Http\Controllers\Photographer\ReportController::class, 'requestPayout'])->name('reports.request-payout');
    });
});
/*
|--------------------------------------------------------------------------
| User Profile & Account Security Routes (Universal Auth)
|--------------------------------------------------------------------------
*/
// User Profile & Security Routes (Supports /profile, /client/profile, /photographer/profile, /admin/profile)
Route::middleware('auth')->group(function () {
    Route::get('/chat/unread-count', [\App\Http\Controllers\Public\ChatController::class, 'getUnreadCount'])->name('chat.unread-count');
    Route::get('/profile', [\App\Http\Controllers\Public\ProfileController::class, 'index'])->name('profile.index');
    Route::get('/client/profile', [\App\Http\Controllers\Public\ProfileController::class, 'index']);
    Route::get('/admin/profile', [\App\Http\Controllers\Public\ProfileController::class, 'index']);
    Route::post('/profile/update', [\App\Http\Controllers\Public\ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/password', [\App\Http\Controllers\Public\ProfileController::class, 'updatePassword'])->name('profile.password');
});
// Public Help, FAQ & Privacy Routes
Route::get('/faq', [\App\Http\Controllers\Public\HelpController::class, 'faq'])->name('public.faq');
Route::get('/kebijakan-privasi', [\App\Http\Controllers\Public\HelpController::class, 'privacy'])->name('public.privacy');
Route::get('/syarat-ketentuan', [\App\Http\Controllers\Public\HelpController::class, 'privacy'])->name('public.terms');