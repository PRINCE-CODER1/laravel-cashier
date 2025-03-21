<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="max-w-5xl mx-auto p-6 bg-white shadow-lg rounded-lg mt-4">
        <h2 class="text-2xl font-semibold mb-4">📦 My Orders</h2>

        {{-- Success & Error Messages --}}
        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        {{-- Orders Table --}}
        @if ($orders->isEmpty())
            <p class="text-gray-500">You have no orders.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700">
                            <th class="p-3 border">Order ID</th>
                            <th class="p-3 border">Total</th>
                            <th class="p-3 border">Status</th>
                            <th class="p-3 border">Date</th>
                            <th class="p-3 border">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr class="border text-center @if($loop->even) bg-gray-50 @endif">
                                <td class="p-3">#{{ $order->id }}</td>
                                <td class="p-3">${{ number_format($order->total, 2) }}</td>
                                <td class="p-3">
                                    <span class="px-3 py-1 rounded text-white uppercase 
                                        {{ $order->status === 'pending' ? 'bg-yellow-500' : ($order->status === 'completed' ? 'bg-green-500' : 'bg-red-500') }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="p-3">{{ $order->created_at->format('M d, Y') }}</td>
                                <td class="p-3">
                                    <a href="{{ route('orders.show', $order->id) }}">View Order</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app-layout>
