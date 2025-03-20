<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    <table class="table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Download</th>
            </tr>
        </thead>
        <tbody>
            @foreach (auth()->user()->invoices() as $invoice)
                <tr>
                    <td>{{ $invoice->date()->toFormattedDateString() }}</td>
                    <td>${{ $invoice->total() }}</td>
                    <td>{{ $invoice->paid ? 'Paid' : 'Unpaid' }}</td>
                    <td>
                        <a href="{{ route('invoice.download', $invoice->id) }}" class="btn btn-primary">
                            Download
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
</x-app-layout>