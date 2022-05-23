<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\PaymentPlatform;
use App\Resolvers\PaymentPlatformResolver;
use App\Services\PaypalService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    protected $paymentPlatformResolver;

    public function __construct(PaymentPlatformResolver $paymentPlatformResolver)
    {
        $this->paymentPlatformResolver = $paymentPlatformResolver;
    }

    public function index()
    {
        $currencies = Currency::all();
        $paypal = PaymentPlatform::where('name', 'paypal')->first();
        $stripe = PaymentPlatform::where('name', 'stripe')->first();

        return view('dashboard', compact('currencies', 'paypal', 'stripe'));
    }

    public function pay(Request $request)
    {

        $request->validate([
            'value' => ['required', 'numeric', 'min:5'],
            'currency' => ['required', 'exists:currencies,iso'],
            'payment_platform' => ['required', 'exists:payment_platforms,id'],
        ]);

        $paymentPlatform = $this->paymentPlatformResolver
            ->resolveService($request->payment_platform);

        session()->put('paymentPlatformId', $request->payment_platform);
        if ($request->user()->hasActiveSubscription()) {
            $request->value = round($request->value * 0.9, 2);
        }

        return $paymentPlatform->handlePayment($request);
    }

    public function approval()
    {
        if (session()->has('paymentPlatformId')) {
            $paymentPlatform = $this->paymentPlatformResolver
                ->resolveService(session()->get('paymentPlatformId'));

            return $paymentPlatform->handleApproval();
        }
        return to_route('dashboard')->withErrors('we cannot retrive the peymant');
    }

    public function cancelled()
    {
        return to_route('dashboard')->withErrors('You cancelled the payment');
    }
}
