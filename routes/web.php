<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/subscriptions', [SubscriptionController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('subscriptions.index');

// Pricing Page with Cancel Button for Subscribed Users
Route::get('/pricing', function () {
    $user = auth()->user();
    return view('pricing', compact('user'));
})->middleware(['auth'])->name('pricing');

Route::get('/checkout/{plan?}', [CheckoutController::class, '__invoke'])->middleware(['auth'])->name('checkout');
Route::get('/one-time-checkout/{priceId}', [CheckoutController::class, 'oneTimeCheckout'])->middleware(['auth'])->name('one-time.checkout');
Route::get('/payment-success', [CheckoutController::class, 'paymentSuccess'])->middleware(['auth'])->name('payment.success');
Route::post('/cancel', [CheckoutController::class, 'cancel'])->middleware(['auth'])->name('payment.cancel');
Route::get('/invoice/latest', function () {
    $user = auth()->user();
    $invoice = $user->invoices()->first(); 
    
    if ($invoice) {
        return $user->downloadInvoice($invoice->id, [
            'vendor' => 'Zero It Solutions',
            'product' => $subscription ? $subscription->stripe_price : 'One-Time Payment',
        ]);
    } else {
        return redirect()->route('dashboard')->with('error', 'No invoices found.');
    }
})->name('invoice.latest');


// products logic here 
Route::get('/products', [ProductController::class, 'index'])->name('products');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'store'])->name('cart.store');
Route::patch('/cart/{cart}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{cart}', [CartController::class, 'destroy'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
Route::post('/checkout', [CartController::class, 'processPayment'])->name('cart.processPayment');
Route::get('/orders', [CartController::class, 'orders'])->name('orders.index');
Route::get('/orders/{order}', [CartController::class, 'ordersShow'])->name('orders.show');






Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
