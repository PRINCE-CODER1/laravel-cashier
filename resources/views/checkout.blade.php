<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
    <div class="max-w-4xl mx-auto p-6 bg-white shadow-lg rounded-lg">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Checkout</h2>
    
        <!-- Order Summary -->
        <div class="bg-gray-100 p-4 rounded-lg mb-6">
            <h3 class="text-lg font-semibold mb-3">Order Summary</h3>
            <div class="space-y-4">
                @foreach ($cartItems as $cart)
                    <div class="flex justify-between items-center border-b pb-2">
                        <div>
                            <p class="font-semibold">{{ $cart->product->name }}</p>
                            <p class="text-sm text-gray-500">Quantity: {{ $cart->quantity }}</p>
                        </div>
                        <p class="text-gray-700 font-semibold">${{ number_format($cart->product->price * $cart->quantity, 2) }}</p>
                    </div>
                @endforeach
            </div>
            <div class="flex justify-between mt-4 text-lg font-bold">
                <p>Total:</p>
                <p>${{ number_format($total, 2) }}</p>
            </div>
        </div>
    
        <!-- Payment Button -->
        <form action="{{ route('checkout.process') }}" method="POST">
            @csrf
            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg text-lg font-bold hover:bg-blue-700 transition">
                Proceed to Payment
            </button>
        </form>
    </div>
</x-app-layout>
