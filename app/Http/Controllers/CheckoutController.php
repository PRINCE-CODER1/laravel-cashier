<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use App\Models\Payment;
use Stripe\Checkout\Session;

class CheckoutController extends Controller
{
    /**
     * Handle the subscription checkout.
     */
    public function __invoke(Request $request, $plan = null)
    {
        $defaultPlan = 'price_1R4g1eGayIRH1T2nBMNiaNRu';
        $plan = $plan ?? $defaultPlan;

        $user = $request->user();

        $hasOneTimePayment = Payment::where('user_id', $user->id)
            ->where('status', 'paid')
            ->exists();

        if ($hasOneTimePayment) {
            return redirect()->route('dashboard')->with('error', 'You have already made a one-time payment. Subscription is not needed.');
        }

        if ($user->subscribed('default')) {
            return redirect()->route('pricing')->with('error', 'You are already subscribed.');
        }

        return $user->newSubscription('default', $plan)->checkout([
            'success_url' => route('dashboard'),
            'cancel_url' => route('pricing'),
        ]);
    }


    /**
     * Handle one-time payment checkout.
     */
    public function oneTimeCheckout(Request $request, $priceId)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $checkoutSession = Session::create([
                'payment_method_types' => ['card'],
                'customer_email' => $request->user()->email,
                'line_items' => [[
                    'price' => $priceId,
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('dashboard'),
                'cancel_url' => route('pricing'),
            ]);

            return redirect($checkoutSession->url);
        } catch (\Exception $e) {
            return redirect()->route('pricing')->with('error', 'Failed to create checkout session.');
        }
    }

    /**
     * Handle payment success.
     */
    public function paymentSuccess(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        $sessionId = $request->query('session_id');
        $session = Session::retrieve($sessionId);

        if (!$session) {
            return redirect()->route('dashboard')->with('error', 'Payment session not found.');
        }

        $user = auth()->user();
        $user->update(['is_subscribed' => true]);

        if ($session->mode === 'subscription') {
            $user->update(['is_subscribed' => true]);
        
            $user->subscriptions()->create([
                'name' => 'default',
                'stripe_id' => $session->subscription,
                'stripe_status' => 'active',
            ]);
        } else {
            Payment::create([
                'user_id' => $user->id,
                'stripe_payment_id' => $session->payment_intent,
                'amount' => $session->amount_total / 100,
                'status' => $session->payment_status,
            ]);
        
            $user->update(['is_subscribed' => true]); 
        }

        return redirect()->route('dashboard')->with('success', 'Transaction completed successfully.');
    }

    /**
     * Cancel subscription or refund one-time payment.
     */
    public function cancel(Request $request)
    {
        $user = $request->user();
    
        if ($user->subscribed('default')) {
            try {
                // Get the subscription
                $subscription = $user->subscription('default');
    
                // Log current subscription status before canceling
                \Log::info('Before canceling subscription', [
                    'user_id' => $user->id,
                    'stripe_status' => $subscription->stripe_status,
                    'ends_at' => $subscription->ends_at,
                ]);
    
                // Check if the subscription is already canceled
                if ($subscription->canceled()) {
                    return redirect()->route('pricing')->with('error', 'Your subscription is already canceled.');
                }
    
                // Cancel the subscription immediately
                $subscription->cancelNow();
    
                // Update the local database
                $user->update(['is_subscribed' => false]);
    
                // Log the cancellation success
                \Log::info('Subscription canceled successfully', [
                    'user_id' => $user->id,
                    'stripe_status' => $subscription->stripe_status,
                    'ends_at' => $subscription->ends_at,
                ]);
    
                return redirect()->route('pricing')->with('success', 'Your subscription has been canceled.');
            } catch (\Exception $e) {
                // Log the error
                \Log::error('Subscription cancellation failed: ' . $e->getMessage());
    
                return redirect()->route('pricing')->with('error', 'Failed to cancel subscription. Please try again.');
            }
        }
    
        return redirect()->route('pricing')->with('error', 'No active subscription found.');
    }
    
}
