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
                    {{ __("You're Subscribed!") }}
                </div>

                @if(auth()->user()->subscribed('default')) 
                    @if(auth()->user()->subscribedToPrice('price_1R4g1eGayIRH1T2nBMNiaNRu'))
                        <div class="p-6 text-gray-900">
                            <p>{{ __("You're subscribed to the Basic plan!") }}</p>
                            <p>{{ __("You can now access all the Basic content.") }}</p>
                        </div>
                    @elseif(auth()->user()->subscribedToPrice('price_1R4g0jGayIRH1T2noDYI037z'))
                        <div class="p-6 text-gray-900">
                            <p>{{ __("You're subscribed to the Premium plan!") }}</p>
                            <p>{{ __("You can now access all the premium content.") }}</p>
                        </div>
                    @elseif(auth()->user()->subscribedToPrice('price_1R4g2CGayIRH1T2njmolmNXt'))
                        <div class="p-6 text-gray-900">
                            <p>{{ __("You're subscribed to the Lifetime plan!") }}</p>
                            <p>{{ __("You can now access all the All content.") }}</p>
                        </div>
                    @endif
                    
                    <!-- Show Cancel Button Only If Subscription Exists -->
                    <form action="{{ route('payment.cancel') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            Cancel Subscription
                        </button>
                    </form>
                @else
                    <div class="p-6 text-red-600">
                        <p>{{ __("You do not have an active subscription.") }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
