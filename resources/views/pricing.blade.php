<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <!-- Pricing -->
    <div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
        <!-- Title -->
        <div class="max-w-2xl mx-auto text-center mb-10 lg:mb-14">
            <h2 class="text-2xl font-bold md:text-4xl md:leading-tight dark:text-white">Subscription Plans</h2>
            <p class="mt-1 text-gray-600 dark:text-gray-400">Choose the plan that better fits your needs.</p>
        </div>
       
        <div class="mt-12 grid sm:grid-cols-1 lg:grid-cols-3 gap-6 lg:items-center">
            @php
                $isSubscribed = auth()->user()->subscribed('default') || auth()->user()->payments()->where('status', 'succeeded')->exists();
            @endphp

            <!-- Monthly Plan -->
            <div class="flex flex-col border border-gray-200 text-center rounded-xl p-8 dark:border-gray-700">
                <h4 class="font-medium text-lg text-gray-800 dark:text-gray-200">Monthly</h4>
                <span class="mt-5 font-bold text-5xl text-gray-800 dark:text-gray-200">
                    <span class="font-bold text-2xl -me-2">$</span> 4.99
                </span>
                <p class="mt-2 text-sm text-gray-500">No commitments. Cancel anytime.</p>

                <a href="{{ !$isSubscribed ? route('checkout', ['plan' => 'price_1R4g1eGayIRH1T2nBMNiaNRu']) : '#' }}"
                   class="mt-5 py-3 px-4 inline-flex justify-center items-center text-sm font-semibold rounded-lg 
                          {{ $isSubscribed ? 'bg-gray-300 text-gray-600 cursor-not-allowed' : 'bg-indigo-100 text-indigo-800 hover:bg-indigo-200 dark:hover:bg-indigo-900 dark:text-indigo-400' }}"
                   {{ $isSubscribed ? 'disabled' : '' }}>
                    Sign up
                </a>
            </div>
            <!-- End Monthly Plan -->

            <!-- Yearly Plan -->
            <div class="flex flex-col border-2 border-indigo-600 text-center shadow-xl rounded-xl p-8 dark:border-indigo-700">
                <p class="mb-3">
                    <span class="inline-flex items-center gap-1.5 py-1.5 px-3 rounded-lg text-xs uppercase font-semibold 
                                 bg-indigo-100 text-indigo-800 dark:bg-indigo-600 dark:text-white">
                        Most popular
                    </span>
                </p>
                <h4 class="font-medium text-lg text-gray-800 dark:text-gray-200">Yearly</h4>
                <span class="mt-5 font-bold text-5xl text-gray-800 dark:text-gray-200">
                    <span class="font-bold text-2xl -me-2">$</span> 34.99
                </span>
                <p class="mt-2 text-sm text-gray-500">Save 30% with full access for 1 year.</p>

                <a href="{{ !$isSubscribed ? route('checkout', ['plan' => 'price_1R4g0jGayIRH1T2noDYI037z']) : '#' }}"
                   class="mt-5 py-3 px-4 inline-flex justify-center items-center text-sm font-semibold rounded-lg 
                          {{ $isSubscribed ? 'bg-gray-300 text-gray-600 cursor-not-allowed' : 'bg-indigo-600 text-white hover:bg-indigo-700' }}"
                   {{ $isSubscribed ? 'disabled' : '' }}>
                    Sign up
                </a>
            </div>
            <!-- End Yearly Plan -->

            <!-- Lifetime Plan -->
            <div class="flex flex-col border border-gray-200 text-center rounded-xl p-8 dark:border-gray-700">
                <h4 class="font-medium text-lg text-gray-800 dark:text-gray-200">Lifetime</h4>
                <span class="mt-5 font-bold text-5xl text-gray-800 dark:text-gray-200">
                    <span class="font-bold text-2xl -me-2">$</span> 174.99
                </span>
                <p class="mt-2 text-sm text-gray-500">Pay once. Lifetime access.</p>

                <a href="{{ !$isSubscribed ? route('one-time.checkout', ['priceId' => 'price_1R4g2CGayIRH1T2njmolmNXt']) : '#' }}"
                   class="mt-5 py-3 px-4 inline-flex justify-center items-center text-sm font-semibold rounded-lg 
                          {{ $isSubscribed ? 'bg-gray-300 text-gray-600 cursor-not-allowed' : 'bg-indigo-100 text-indigo-800 hover:bg-indigo-200' }}"
                   {{ $isSubscribed ? 'disabled' : '' }}>
                    Sign up
                </a>
            </div>
            <!-- End Lifetime Plan -->
        </div>
        <!-- End Grid -->
        @if($isSubscribed)
            <div class="mt-8 p-4 border border-green-400 rounded-lg bg-green-100 text-green-800">
                <strong>You already have access!</strong> 🎉 <br>

                @if(auth()->user()->subscribed('default'))
                    <form action="{{ route('payment.cancel') }}" method="POST" class="mt-3">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                            Cancel Subscription
                        </button>
                    </form>
                @endif
            </div>
        @endif
    </div>
</x-app-layout>
