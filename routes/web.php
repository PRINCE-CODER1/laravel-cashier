<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\SubscriptionController;
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



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
