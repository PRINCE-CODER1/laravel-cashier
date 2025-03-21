<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

  

    <div class="max-w-4xl mx-auto p-6 bg-white shadow-lg rounded-lg mt-4">
        <h2 class="text-2xl font-semibold mb-4">📦 Order Details (ID: #{{ $order->id }})</h2>

        <div class="mb-4">
            <p class="text-lg"><strong>Status:</strong>
                <span class="px-3 py-1 rounded
                    @if($order->status === 'pending') bg-yellow-500 text-white
                    @elseif($order->status === 'completed') bg-green-500 text-white
                    @else bg-red-500 text-white @endif">
                    {{ ucfirst($order->status) }}
                </span>
            </p>
            <p class="text-lg"><strong>Total:</strong> ${{ number_format($order->total, 2) }}</p>
            <p class="text-lg"><strong>Date:</strong> {{ $order->created_at->format('M d, Y') }}</p>
        </div>

        <h3 class="text-xl font-semibold mb-2">🛍️ Items in Order</h3>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-3 border">Product</th>
                        <th class="p-3 border">Price</th>
                        <th class="p-3 border">Quantity</th>
                        <th class="p-3 border">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr class="border text-center">
                            <td class="p-3">{{ $item->product->name }}</td>
                            <td class="p-3">${{ number_format($item->price, 2) }}</td>
                            <td class="p-3">{{ $item->quantity }}</td>
                            <td class="p-3">${{ number_format($item->price * $item->quantity, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <a href="{{ route('orders.index') }}"
                class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Back to Orders
            </a>
        </div>
    </div>




</x-app-layout>
