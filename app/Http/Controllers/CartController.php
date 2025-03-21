<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Webhook;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = Cart::where('user_id', Auth::id())->with('product')->get();
        $total = $cartItems->sum(fn($cart) => $cart->product->price * $cart->quantity);
        return view('cart.index', compact('cartItems','total'));
    }

    public function store(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);

        $cart = Cart::where('user_id', Auth::id())->where('product_id', $request->product_id)->first();

        if ($cart) {
            $cart->increment('quantity');
        } else {
            Cart::create([
                'user_id' => Auth::id(),
                'product_id' => $request->product_id,
                'quantity' => 1
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Product added to cart.');
    }

    public function update(Request $request, Cart $cart)
    {
        $cart->update(['quantity' => $request->quantity]);
        return back()->with('success', 'Cart updated successfully.');
    }

    public function destroy(Cart $cart)
    {
        $cart->delete();
        return back()->with('success', 'Item removed from cart.');
    }

    public function clear()
    {
        Cart::where('user_id', Auth::id())->delete();
        return back()->with('success', 'Cart cleared.');
    }

    /**
     * Handle Stripe Checkout
     */
    public function processPayment(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $cartItems = Cart::where('user_id', Auth::id())->with('product')->get();
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        try {
            $lineItems = [];
                foreach ($cartItems as $cart) {
                    $unitAmount = intval($cart->product->price * 100);
                    if ($unitAmount <= 0) {
                        return redirect()->route('cart.index')->with('error', 'Invalid product price.');
                    }

                    $lineItems[] = [
                        'price_data' => [
                            'currency' => 'usd',
                            'product_data' => ['name' => $cart->product->name],
                            'unit_amount' => $unitAmount,
                        ],
                        'quantity' => $cart->quantity,
                    ];
                }

                $checkoutSession = Session::create([
                    'payment_method_types' => ['card'],
                    'customer_email' => Auth::user()?->email,
                    'line_items' => $lineItems,
                    'mode' => 'payment',
                    'success_url' => route('orders.index') . '?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => route('cart.index'),
                ]);

        
            // Create Order
            $order = Order::create([
                'user_id' => Auth::id(),
                'stripe_session_id' => $checkoutSession->id,
                'total' => $cartItems->sum(fn($cart) => $cart->product->price * $cart->quantity),
                'status' => 'pending',
            ]);
        
            // Store Order Items
            foreach ($cartItems as $cart) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cart->product_id,
                    'quantity' => $cart->quantity,
                    'price' => $cart->product->price,
                ]);
            }
        
            return redirect($checkoutSession->url);
        } catch (\Exception $e) {
            return redirect()->route('cart.index')->with('error', 'Something went wrong: ' . $e->getMessage());
        }
        
    }

    /**
     * Handle Orders
     */
    public function orders(Request $request)
    {
        $orders = Order::where('user_id', Auth::id())->latest()->get();

        // Check payment status for orders and update the order status
        $sessionId = $request->query('session_id');
        if ($sessionId) {
            try {
                Stripe::setApiKey(config('services.stripe.secret'));
                $session = Session::retrieve($sessionId);

                if ($session && $session->payment_status === 'paid') {
                    $order = Order::where('stripe_session_id', $sessionId)->first();
                    if ($order && $order->status === 'pending') {
                        // Update the order status to 'completed'
                        $order->update(['status' => 'completed']);
                        Cart::where('user_id', $order->user_id)->delete();
                        return redirect()->route('orders.index')->with('success', 'Your order has been placed successfully!');
                    }
                }
            } catch (\Exception $e) {
                Log::error('Stripe Checkout Error: ' . $e->getMessage());
                return redirect()->route('orders.index')->with('error', 'Something went wrong: ' . $e->getMessage());
            }
        }

        return view('orders.index', compact('orders'));
    }
    // Show details of a single order
    public function ordersShow(Order $order)
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            abort(403, 'Unauthorized access');
        }

        // Ensure the order belongs to the authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this order');
        }

        return view('orders.show', compact('order'));
    }


    
}
