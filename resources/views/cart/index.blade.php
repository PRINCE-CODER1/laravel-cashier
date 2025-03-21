<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto p-6 bg-white shadow-lg rounded-lg mt-4">
        <h2 class="text-2xl font-semibold mb-4">🛒 Your Shopping Cart</h2>

        @if ($cartItems->isEmpty())
            <p class="text-gray-500">Your cart is empty.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="p-3 border">Product</th>
                            <th class="p-3 border">Price</th>
                            <th class="p-3 border">Quantity</th>
                            <th class="p-3 border">Total</th>
                            <th class="p-3 border">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cartItems as $item)
                            <tr class="border text-center">
                                <td class="p-3">{{ $item->product->name }}</td>
                                <td class="p-3">${{ number_format($item->product->price, 2) }}</td>
                                <td class="p-3">
                                    <form action="{{ route('cart.update', $item->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="number" name="quantity" min="1" value="{{ $item->quantity }}"
                                            class="w-16 text-center border rounded-md">
                                        <button class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600">
                                            Update
                                        </button>
                                    </form>
                                </td>
                                <td class="p-3">${{ number_format($item->product->price * $item->quantity, 2) }}</td>
                                <td class="p-3">
                                    <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600">
                                            Remove
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 flex justify-between items-center">
                <h3 class="text-xl font-semibold">Total: ${{ number_format($total, 2) }}</h3>
                <form action="{{ route('cart.clear') }}" method="POST">
                    @csrf
                    <button class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                        Clear Cart
                    </button>
                </form>
                <form action="{{ route('cart.processPayment') }}" method="POST">
                    @csrf
                    <button class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">
                        Proceed to Checkout
                    </button>
                </form>
            </div>
        @endif
    </div>
</x-app-layout>
