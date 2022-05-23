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

            <form method="POST" action="{{ route('subscribe.store') }}" id="paymentForm">
                @csrf
                <div class="m-2 p-2">
                    @foreach ($plans as $plan)
                        <label>
                            <input type="radio" name="plan" value="{{ $plan->slug }}">
                            <p class="text-2xl font-bold text-blue-300">{{ $plan->slug }}</p>
                            <p class="text-2xl font-bold text-blue-300">{{ $plan->visual_price }}</p>
                        </label>
                    @endforeach
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
                        <div id="cardElement">
                        </div>
                        <div id="cardErrors" role="alert"></div>
                        <input type="hidden" name="payment_method" id="paymentMethod">
                    </div>
                </div>
                <div class="flex items-center justify-end mt-4">
                    <x-button id="payButton" class="ml-3">
                        {{ __('Pay') }}
                    </x-button>
                </div>
            </form>
        </x-auth-card>
    </div>
    @push('scripts')
        <script src="https://js.stripe.com/v3/"></script>
        <script>
            const stripe = Stripe('{{ config('services.stripe.key') }}');

            const elements = stripe.elements({
                locale: 'en'
            });
            const cardElement = elements.create('card');
            cardElement.mount('#cardElement');
        </script>
        <script>
            const form = document.getElementById('paymentForm');
            const payButton = document.getElementById('payButton');

            payButton.addEventListener('click', async (e) => {
                if (form.elements.payment_platform.value === "{{ $stripe->id }}") {
                    e.preventDefault();
                    const {
                        paymentMethod,
                        error
                    } = await stripe.createPaymentMethod(
                        'card', cardElement, {
                            billing_details: {
                                "name": "{{ auth()->user()->name }}",
                                "email": "{{ auth()->user()->email }}",
                            }
                        }
                    );

                    if (error) {
                        const displayError = document.getElementById('cardErrors');
                        displayError.textContent = error.message;
                    } else {
                        tokenInput = document.getElementById('paymentMethod');

                        tokenInput.value = paymentMethod.id;
                        form.submit();
                    }
                }

            });
        </script>
    @endpush
</x-app-layout>
