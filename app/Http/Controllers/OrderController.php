<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())->latest()->get();
        return view('orders.index', compact('orders'));
    }

    // Show details of a single order
    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        return view('orders.show', compact('order'));
    }

    // Cancel an order
    public function cancel(Order $order)
    {
        if ($order->user_id !== Auth::id() || $order->status !== 'pending') {
            return redirect()->route('orders.index')->with('error', 'Order cannot be canceled.');
        }

        $order->update(['status' => 'canceled']);

        return redirect()->route('orders.index')->with('success', 'Order canceled successfully.');
    }
    public function verifyPayment(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }
    
        // Don't update if already completed
        if ($order->status === 'completed') {
            return redirect()->route('orders.index')->with('success', 'Order is already completed.');
        }
    
        try {
            Stripe::setApiKey(config('services.stripe.secret'));
    
            $session = \Stripe\Checkout\Session::retrieve($order->stripe_session_id);
    
            if ($session->payment_status === 'paid') {
                $order->update(['status' => 'completed']);
                Cart::where('user_id', $order->user_id)->delete();
                return redirect()->route('orders.index')->with('success', 'Payment verified! Order completed.');
            } else {
                return redirect()->route('orders.index')->with('error', 'Payment not completed yet.');
            }
        } catch (\Exception $e) {
            return redirect()->route('orders.index')->with('error', 'Error verifying payment: ' . $e->getMessage());
        }
    }
    
}
