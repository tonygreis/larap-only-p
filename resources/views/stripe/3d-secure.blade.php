<x-app-layout>
    <h1>Complete the security steps</h1>
    @push('scripts')
        <script src="https://js.stripe.com/v3/"></script>
        <script>
            const stripe = Stripe('{{ config('services.stripe.key') }}');

            stripe.handleCardAction("{{ $clientSecret }}")
                .then(function(result) {
                    if (result.error) {
                        window.location.replace("{{ route('cancelled') }}");
                    } else {
                        window.location.replace("{{ route('approval') }}");
                    }
                })
        </script>
    @endpush
</x-app-layout>
