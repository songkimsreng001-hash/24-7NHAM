<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AdminPortalController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ContactUsFormController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\DisputeController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\UpdateProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/about', function () {
    return view('about');
});

Route::get('/user/{id}', function ($id) {
    return "User ID: is $id";
});

Route::get('/post/{slug}', function ($slug = 'default-post') {
    return "Post Slug: $slug";
});

Route::get('/test', function () {
    $route = route('dashboard');

    return "The URL for the dashboard route is: $route";
});
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', function () {
        return 'User Profile';
    });

    Route::get('/category', [CategoryController::class, 'index'])->name('category.list');
    Route::get('/category/create', [CategoryController::class, 'create'])->name('category.create');
    Route::post('/category', [CategoryController::class, 'store'])->name('category.store');
    Route::get('/category/{categoryId}/edit', [CategoryController::class, 'edit'])->name('category.edit');
    Route::put('/category/{categoryId}', [CategoryController::class, 'update'])->name('category.update');
    Route::delete('/category/{categoryId}', [CategoryController::class, 'destroy'])->name('category.delete');
    Route::get('/category/{cateId}', [CategoryController::class, 'show'])->name('category.show');

    Route::get('/product', [ProductController::class, 'index'])->name('product.index');
    Route::get('/product/create', [ProductController::class, 'create'])->name('product.create');
    Route::post('/product', [ProductController::class, 'store'])->name('product.store');
    Route::get('/product/{product}', [ProductController::class, 'show'])->name('product.show');
    Route::delete('/product/{product}', [ProductController::class, 'destroy'])->name('product.destroy');
    Route::get('/product/{product}/edit', [ProductController::class, 'edit'])->name('product.edit');
    Route::put('/product/{product}', [ProductController::class, 'update'])->name('product.update');

    Route::get('/book', [BookController::class, 'index'])->name('book.index');
    Route::get('/book/create', [BookController::class, 'create'])->name('book.create');
    Route::post('/book', [BookController::class, 'store'])->name('book.store');
    Route::get('/book/{book}', [BookController::class, 'show'])->name('book.show');
    Route::delete('/book/{book}', [BookController::class, 'destroy'])->name('book.destroy');
    Route::get('/book/{book}/edit', [BookController::class, 'edit'])->name('book.edit');
    Route::put('/book/{book}', [BookController::class, 'update'])->name('book.update');

    // ==========================================
    // CUSTOMER DELIVERY ECOSYSTEM
    // ==========================================
    // Order History & Tracking
    Route::get('/my-orders', [CustomerOrderController::class, 'index'])->name('customer.orders.index');
    Route::get('/my-orders/{order}', [CustomerOrderController::class, 'show'])->name('customer.orders.show');
    Route::post('/my-orders/{order}/reorder', [CustomerOrderController::class, 'reorder'])->name('customer.orders.reorder');
    Route::post('/my-orders/{order}/cancel', [CustomerOrderController::class, 'cancel'])->name('customer.orders.cancel');

    // Multi-Address Management & Leaflet Pin-drop
    Route::get('/addresses', [AddressController::class, 'index'])->name('addresses.index');
    Route::post('/addresses', [AddressController::class, 'store'])->name('addresses.store');
    Route::post('/addresses/{id}/default', [AddressController::class, 'setDefault'])->name('addresses.default');
    Route::delete('/addresses/{id}', [AddressController::class, 'destroy'])->name('addresses.destroy');
    Route::match(['get', 'post'], '/addresses/calculate-distance', [AddressController::class, 'calculateDistance'])->name('addresses.distance');

    // Route aliases for address.* (singular compatibility)
    Route::match(['get', 'post'], '/address/calculate-distance', [AddressController::class, 'calculateDistance'])->name('address.distance');
    Route::post('/address', [AddressController::class, 'store'])->name('address.store');
    Route::get('/address', [AddressController::class, 'index'])->name('address.index');

    // Promo Codes Validation & Apply
    Route::post('/promo/apply', [PromoController::class, 'apply'])->name('promo.apply');

    // Favorites System
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites/toggle/{productId}', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

    // In-App Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');

    // In-App Chat (Order channel & Support channel)
    Route::get('/chat/orders/{orderId}', [ChatController::class, 'getOrderMessages'])->name('chat.order.messages');
    Route::post('/chat/orders/{orderId}', [ChatController::class, 'sendOrderMessage'])->name('chat.order.send');
    Route::get('/support/chat', [ChatController::class, 'supportChatView'])->name('support.chat');
    Route::get('/support/messages', [ChatController::class, 'getSupportMessages'])->name('support.messages');
    Route::post('/support/messages', [ChatController::class, 'sendSupportMessage'])->name('support.send');

    // Reviews & Ratings
    Route::post('/orders/{orderId}/review', [ReviewController::class, 'store'])->name('order.review.store');

    // Disputes
    Route::post('/orders/{orderId}/dispute', [DisputeController::class, 'store'])->name('order.dispute.store');

    // ==========================================
    // COURIER / DRIVER PORTAL
    // ==========================================
    Route::prefix('driver')->group(function () {
        Route::get('/', [DriverController::class, 'dashboard'])->name('driver.dashboard');
        Route::post('/toggle-status', [DriverController::class, 'toggleAvailability'])->name('driver.toggle');
        Route::post('/orders/{id}/accept', [DriverController::class, 'acceptOrder'])->name('driver.accept');
        Route::get('/active', [DriverController::class, 'activeDelivery'])->name('driver.active');
        Route::post('/orders/{id}/status', [DriverController::class, 'updateStatus'])->name('driver.updateStatus');
        Route::post('/orders/{id}/preset', [DriverController::class, 'sendQuickMessage'])->name('driver.preset');
        Route::get('/earnings', [DriverController::class, 'earnings'])->name('driver.earnings');
        Route::get('/profile', [DriverController::class, 'profile'])->name('driver.profile');
        Route::post('/profile', [DriverController::class, 'updateProfile'])->name('driver.profile.update');
    });

    // ==========================================
    // ADMIN PORTAL
    // ==========================================
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/orders', [OrderController::class, 'index'])->name('admin.order');
        Route::post('/orders/approve/{id}', [OrderController::class, 'approve'])->name('admin.approve');
        Route::post('/orders/reject/{id}', [OrderController::class, 'reject'])->name('admin.reject');
        Route::post('/orders/{id}/delivery-status', [OrderController::class, 'updateDeliveryStatus'])->name('admin.orders.updateDeliveryStatus');
        Route::post('/orders/{id}/assign-driver', [OrderController::class, 'assignDriver'])->name('admin.orders.assignDriver');
        Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

        // Enhanced Admin Portal Modules
        Route::get('/admin/users', [AdminPortalController::class, 'users'])->name('admin.users');
        Route::post('/admin/users/{id}/toggle-status', [AdminPortalController::class, 'toggleUserStatus'])->name('admin.users.toggle');
        Route::post('/admin/users/{id}/approve-driver', [AdminPortalController::class, 'approveDriver'])->name('admin.users.approveDriver');
        Route::post('/admin/users/{id}/assign-role', [AdminPortalController::class, 'assignRole'])->name('admin.users.assignRole');

        Route::get('/admin/active-orders', [AdminPortalController::class, 'activeOrders'])->name('admin.orders.active');
        Route::post('/admin/orders/{id}/reassign', [AdminPortalController::class, 'reassignDriver'])->name('admin.orders.reassign');
        Route::post('/admin/orders/{id}/cancel', [AdminPortalController::class, 'cancelOrder'])->name('admin.orders.cancel');

        Route::get('/admin/broadcast', [AdminPortalController::class, 'broadcastForm'])->name('admin.broadcast');
        Route::post('/admin/broadcast', [AdminPortalController::class, 'sendBroadcast'])->name('admin.broadcast.send');

        Route::get('/admin/promos', [AdminPortalController::class, 'promos'])->name('admin.promos');
        Route::post('/admin/promos', [AdminPortalController::class, 'storePromo'])->name('admin.promos.store');
        Route::post('/admin/promos/{id}/toggle', [AdminPortalController::class, 'togglePromo'])->name('admin.promos.toggle');

        Route::get('/admin/disputes', [AdminPortalController::class, 'disputes'])->name('admin.disputes');
        Route::post('/admin/disputes/{id}/resolve', [DisputeController::class, 'resolve'])->name('admin.disputes.resolve');

        Route::get('/admin/analytics', [AdminPortalController::class, 'analytics'])->name('admin.analytics');
        Route::get('/admin/audit-logs', [AdminPortalController::class, 'auditLogs'])->name('admin.audit');
    });
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Public Shipment Tracking (Lookup by tracking number or order ID)
Route::get('/track', [CustomerOrderController::class, 'track'])->name('order.track');
Route::get('/track/{tracking_number}', [CustomerOrderController::class, 'track'])->name('order.track.search');

// Using a Controller (Recommended)
Route::get('/categories/{categoryId}/projects/{projectId}', [ProjectController::class, 'show'])->name('projects.show');

// Using a Closure Callback
Route::get('/users/{id}/{name}', function (string $id, string $name) {
    return 'User ID: '.$id.', Name: '.$name;
});

Route::get('/', [FrontendController::class, 'index']);
Route::get('/list', [FrontendController::class, 'list']);
Route::get('/show/{id}', [FrontendController::class, 'show']);

Route::get('/search', [FrontendController::class, 'getBySearch'])->name('frontend.search');
Route::get('/navbar-search', [FrontendController::class, 'navbarSearch'])->name('frontend.navbar-search');
Route::get('/frontend', [FrontendController::class, 'categories'])->name('frontend.categories');
Route::get('/frontend/{category}', [FrontendController::class, 'getByCategory'])->name('frontend.category');

// login and register
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/post-login', [AuthController::class, 'postLogin'])->name('login.post');
Route::get('/registration', [AuthController::class, 'registration'])->name('register');
Route::post('/post-registration', [AuthController::class, 'postRegistration'])->name('register.post');

// Google OAuth
Route::get('/auth/redirect', [AuthController::class, 'redirectToGoogle'])->name('auth.google.redirect');
Route::get('/auth/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
Route::get('/api/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback.api');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback.alt');

// change password
Route::get('/change-password', [ChangePasswordController::class, 'index'])->name('form.password');
Route::post('/change-password', [ChangePasswordController::class, 'store'])->name('change.password');

// update profile
Route::get('/update-profile/{user}', [UpdateProfileController::class, 'editProfile'])->name('profile.edit');
Route::patch('/update-profile/{user}', [UpdateProfileController::class, 'updateProfile'])->name('profile.update');

// forget password
Route::get('forget-password', [ForgotPasswordController::class, 'showForgetPasswordForm'])->name('forget.password.get');
Route::post('forget-password', [ForgotPasswordController::class, 'submitForgetPasswordForm'])->name('forget.password.post');
Route::get('reset-password/{token}', [ForgotPasswordController::class, 'showResetPasswordForm'])->name('reset.password.get');
Route::post('reset-password', [ForgotPasswordController::class, 'submitResetPasswordForm'])->name('reset.password.post');

// contact us form
Route::get('/contact', [ContactUsFormController::class, 'createForm']);
Route::post('/contact', [ContactUsFormController::class, 'ContactUsForm'])->name('contact.store');

// cart
Route::get('/cart', [StoreController::class, 'cart'])->name('cart');
Route::get('/add-to-cart/{id}', [StoreController::class, 'addToCart'])->name('add.to.cart');
Route::patch('/update-cart', [StoreController::class, 'update'])->name('update.cart');
Route::delete('/remove-from-cart', [StoreController::class, 'remove'])->name('remove.from.cart');

// checkout
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [StoreController::class, 'checkout'])->name('cart.checkout');
    Route::post('/checkout/complete', [StoreController::class, 'completeCheckout'])->name('checkout.complete');
    Route::post('/create-payment-intent', [PaymentController::class, 'createPaymentIntent'])->name('payment.intent');
});

Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
