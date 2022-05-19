<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <x-auth-card>
            <x-slot name="logo">
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </x-slot>

            <form method="POST" action="{{ route('pay') }}">
                @csrf
                <div>
                    <x-label for="value" :value="__('Value you want to pay')" />

                    <x-input id="value" class="block mt-1 w-full" type="number" min="5" step="0.01" name="value"
                        :value="old('value')" required autofocus />
                </div>

                <div>
                    <x-label for="currency" :value="__('Currency')" />

                    <select id="currency" name="currency"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        @foreach ($currencies as $currency)
                            <option value="{{ $currency->iso }}">{{ strtoupper($currency->iso) }}</option>
                        @endforeach
                    </select>
                </div>
                <div x-data="{ tab: 'none' }">
                    <x-label for="platform" :value="__('Platform')" />

                    <label>
                        <input type="radio" name="payment_platform" value="{{ $paypal->id }}" @click="tab = 'paypal'">
                        <img class="w-16 h-auto" src="{{ asset($paypal->image) }}">
                    </label>
                    <label>
                        <input type="radio" name="payment_platform" value="{{ $stripe->id }}" @click="tab = 'stripe'">
                        <img class="w-16 h-auto" src="{{ asset($stripe->image) }}">
                    </label>
                    <div class="mt-2 p-2" x-show="tab === 'paypal'">
                        Paypal
                    </div>
                    <div class="mt-2 p-2" x-show="tab === 'stripe'">
                        Stripe
                    </div>
                </div>
                <div class="flex items-center justify-end mt-4">

                    <x-button class="ml-3">
                        {{ __('Pay') }}
                    </x-button>
                </div>
            </form>
        </x-auth-card>
    </div>
</x-app-layout>
